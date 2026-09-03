<?php

namespace App\Support;

use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Immediate payout: when a booking becomes `visited`, the owner's share moves
 * to their connected Stripe account right away instead of waiting for the
 * weekly/monthly batch.
 *
 * Double-payment safety mirrors the batch engine: the Stripe transfer uses the
 * idempotency key `transfer-txn-{id}` (a retry after a crash returns the
 * existing transfer), and only `eligible` transactions are selected, so
 * nothing already transferred is ever touched again.
 *
 * Failures never block the visit - the booking is already `visited`, the
 * transaction simply stays `eligible` and the `payouts:process` batch retries
 * it on its next run (and notifies admins if money stays stuck).
 */
final class PayoutTransfer
{
    /**
     * Issue the owner's share for every paid, undisputed transaction of the
     * given bookings. Only eligible rows are picked, which makes the call
     * idempotent - re-running over already-transferred bookings touches
     * nothing.
     */
    public static function forBookings(Collection $bookingIds): int
    {
        if ($bookingIds->isEmpty()) {
            return 0;
        }

        $transactions = Transaction::whereIn('booking_id', $bookingIds)
            ->where('status', PaymentStatus::Paid)
            ->where('payout_status', PayoutStatus::Eligible)
            ->whereNull('disputed_at')
            ->with('owner')
            ->get();

        $transferred = 0;

        foreach ($transactions as $transaction) {
            if (self::transferOne($transaction)) {
                $transferred++;
            }
        }

        return $transferred;
    }

    private static function transferOne(Transaction $transaction): bool
    {
        $owner = $transaction->owner;

        // "Stripe not verified" is a normal state, not an error: leave the
        // transaction eligible so the batch retries once onboarding finishes.
        if (! $owner?->canReceivePayouts()) {
            Log::info('Skipped immediate payout - owner cannot receive transfers yet', [
                'transaction' => $transaction->id,
                'owner'       => $transaction->owner_id,
            ]);

            return false;
        }

        try {
            $transfer = app(StripeGateway::class)->transfer($transaction, $owner);

            $transaction->update([
                'payout_status'      => PayoutStatus::PaidOut,
                'stripe_transfer_id' => $transfer->id,
                'transferred_at'     => now(),
            ]);

            Log::info('Immediate payout transferred', [
                'transaction' => $transaction->id,
                'transfer'    => $transfer->id,
                'owner'       => $transaction->owner_id,
            ]);

            return true;
        } catch (\Throwable $e) {
            // The transaction stays eligible; the batch is the retry net.
            Log::error('Immediate payout transfer failed', [
                'transaction' => $transaction->id,
                'owner'       => $transaction->owner_id,
                'error'       => $e->getMessage(),
            ]);

            return false;
        }
    }
}
