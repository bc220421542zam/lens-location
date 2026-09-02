<?php

namespace App\Console\Commands;

use App\Enums\PayoutBatchStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\PayoutTransferFailedNotification;
use App\Support\StripeGateway;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Escrow payout engine: aggregates eligible transactions per owner into a
 * payout batch and issues standalone Stripe Transfers to their connected
 * accounts, then marks the included transactions paid_out.
 *
 * Double-payment safety (three layers):
 *  1. Stripe idempotency key `transfer-txn-{id}` - a retry after a crash
 *     returns the existing transfer instead of moving money twice.
 *  2. The Eligible -> PaidOut flip happens inside the same DB transaction
 *     as the Stripe call.
 *  3. The eligible selection takes a lockForUpdate, so overlapping runs
 *     (weekly + monthly schedules) can't pick the same rows.
 *
 * The window is the PREVIOUS complete week (Mon..Sun) or calendar month, but
 * eligibility is a catch-up filter (`held_since <= end`, no lower bound), so
 * anything that completed late is picked up by the next run - nothing is
 * ever lost.
 */
class PayoutsProcessCommand extends Command
{
    protected $signature = 'payouts:process {--period=weekly : weekly|monthly}';

    protected $description = 'Batch-transfer eligible escrow transactions to owners via Stripe';

    public function handle(StripeGateway $stripe): int
    {
        $period = (string) $this->option('period');

        if (! in_array($period, ['weekly', 'monthly'], true)) {
            $this->error('--period must be weekly or monthly.');

            return self::INVALID;
        }

        [$start, $end] = $this->window($period);

        $this->info("Processing {$period} payouts, window {$start->toDateString()} .. {$end->toDateString()}");

        $groups = Transaction::query()
            ->where('status', PaymentStatus::Paid)
            ->where('payout_status', PayoutStatus::Eligible)
            ->where('held_since', '<=', $end)
            // Disputed money never leaves the platform while the dispute is open.
            ->whereNull('disputed_at')
            ->lockForUpdate()
            ->get()
            ->groupBy('owner_id');

        if ($groups->isEmpty()) {
            $this->info('Nothing eligible to pay out.');

            return self::SUCCESS;
        }

        $failures = 0;
        $transferred = 0;

        foreach ($groups as $ownerId => $transactions) {
            try {
                $transferred += $this->processOwner((int) $ownerId, $transactions, $start, $end, $stripe);
            } catch (\Throwable $e) {
                $failures++;

                Log::error('Payout batch failed', [
                    'owner_id' => $ownerId,
                    'period'   => $period,
                    'error'    => $e->getMessage(),
                ]);

                // The transactions stay eligible and retry on the next run; the
                // admins need to know money is stuck.
                $this->notifyAdminsOfFailure(
                    (string) ($transactions->first()->owner?->name ?? 'Owner #'.$ownerId),
                    $transactions->count(),
                    $e->getMessage(),
                );

                $this->error("Owner {$ownerId}: {$e->getMessage()}");
            }
        }

        $this->info("Done. {$transferred} transfer(s) issued; {$failures} owner batch(es) failed.");

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{0: Carbon, 1: Carbon} [start, end]
     */
    private function window(string $period): array
    {
        if ($period === 'monthly') {
            $start = now()->copy()->startOfMonth()->subMonth();

            return [$start, now()->copy()->startOfMonth()->subSecond()];
        }

        $start = now()->copy()->startOfWeek(Carbon::MONDAY)->subWeek();

        return [$start, now()->copy()->startOfWeek(Carbon::MONDAY)->subSecond()];
    }

    private function processOwner(int $ownerId, Collection $transactions, CarbonInterface $start, CarbonInterface $end, StripeGateway $stripe): int
    {
        $owner = User::findOrFail($ownerId);

        if (! $owner->canReceivePayouts()) {
            $this->warn("Owner {$ownerId} cannot receive payouts yet - leaving {$transactions->count()} transaction(s) eligible.");

            // "Stripe not verified" is the canonical payout failure: keep the
            // transactions ready to pay and tell the admins.
            $this->notifyAdminsOfFailure(
                $owner->name,
                $transactions->count(),
                'Stripe onboarding incomplete',
            );

            return 0;
        }

        $issued = 0;

        DB::transaction(function () use ($owner, $transactions, $start, $end, $stripe, &$issued) {
            // One batch row per owner per period; a retry reuses it.
            $payout = Payout::firstOrCreate(
                ['owner_id' => $owner->id, 'period_start' => $start, 'period_end' => $end],
                ['total_amount' => 0, 'status' => PayoutBatchStatus::Pending],
            );

            foreach ($transactions as $txn) {
                if ($txn->payout_status !== PayoutStatus::Eligible) {
                    continue; // flipped by a concurrent or prior run
                }

                $transfer = $stripe->transfer($txn, $owner);

                $txn->update([
                    'payout_status'      => PayoutStatus::PaidOut,
                    'stripe_transfer_id' => $transfer->id,
                    'transferred_at'     => now(),
                ]);
                $payout->transactions()->syncWithoutDetaching([$txn->id]);

                $issued++;

                $this->info("Transfer {$transfer->id}: transaction {$txn->id} -> owner {$owner->id} ({$txn->owner_payout_amount} {$txn->currency})");
            }

            $payout->update([
                'total_amount' => $payout->transactions()->sum('owner_payout_amount'),
                'status'       => PayoutBatchStatus::Processed,
                'processed_at' => now(),
            ]);
        });

        return $issued;
    }

    /**
     * Database notification to every admin. Best-effort: a notification
     * failure must not fail the batch run - the transactions are already
     * logged and stay eligible either way.
     */
    private function notifyAdminsOfFailure(string $ownerName, int $pendingCount, string $reason): void
    {
        try {
            User::where('role', 'admin')->each(
                fn (User $admin) => $admin->notify(
                    new PayoutTransferFailedNotification($ownerName, $pendingCount, $reason)
                )
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to notify admins of payout failure', ['error' => $e->getMessage()]);
        }
    }
}
