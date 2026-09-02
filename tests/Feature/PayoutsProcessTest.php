<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\PayoutBatchStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\PayoutTransferFailedNotification;
use App\Support\StripeGateway;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PayoutsProcessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // A Monday, so the weekly window (previous Mon..Sun) is deterministic.
        Carbon::setTestNow('2026-08-31 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private int $ownerSeq = 0;

    private function owner(array $overrides = []): User
    {
        $this->ownerSeq++;

        return User::create(array_merge([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.$this->ownerSeq.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
            'stripe_account_id' => 'acct_test_'.$this->ownerSeq,
            'stripe_transfers_status' => 'active',
        ], $overrides));
    }

    private function eligibleTransaction(User $owner, ?Carbon $heldSince = null): Transaction
    {
        $customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'Ust',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);

        $location = Location::create([
            'user_id' => $owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $booking = Booking::create([
            'location_id' => $location->id,
            'customer_id' => $customer->id,
            'booking_date' => now()->subDays(5),
            'hours' => 2,
            'total_price' => 10000,
            'status' => 'completed',
        ]);

        return Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'owner_id' => $owner->id,
            'amount' => 10000,
            'platform_fee' => 1000,
            'owner_earning' => 9000,
            'platform_commission' => 1000,
            'owner_payout_amount' => 9000,
            'status' => PaymentStatus::Paid,
            'payout_status' => PayoutStatus::Eligible,
            'paid_at' => $heldSince ?? now()->subDays(5),
            'held_since' => $heldSince ?? now()->subDays(5),
        ]);
    }

    /**
     * Swap the gateway for a stub recording every transfer call. The optional
     * hook can throw to simulate a Stripe failure on a given transaction.
     */
    private function fakeGateway(?\Closure $hook = null): object
    {
        $stub = new class extends StripeGateway
        {
            public array $transfers = [];

            public ?\Closure $hook = null;

            public function __construct() {}

            public function currency(): string
            {
                return 'pkr';
            }

            public function transfer(Transaction $transaction, User $owner): \Stripe\Transfer
            {
                $this->transfers[] = ['transaction_id' => $transaction->id, 'owner_id' => $owner->id];

                if ($this->hook) {
                    ($this->hook)($transaction);
                }

                return \Stripe\Transfer::constructFrom(['id' => 'tr_'.$transaction->id]);
            }
        };

        $stub->hook = $hook;

        $this->app->instance(StripeGateway::class, $stub);

        return $stub;
    }

    public function test_weekly_run_batches_eligible_transactions_per_owner(): void
    {
        $gateway = $this->fakeGateway();

        $ownerA = $this->owner();
        $ownerB = $this->owner();

        $txnA1 = $this->eligibleTransaction($ownerA, heldSince: now()->subDays(4));   // inside window
        $txnA2 = $this->eligibleTransaction($ownerA, heldSince: now()->subDays(20));  // catch-up
        $txnB1 = $this->eligibleTransaction($ownerB, heldSince: now()->subDays(3));

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        // One transfer per transaction, straight to the right owner.
        $this->assertCount(3, $gateway->transfers);

        // One payout batch per owner, with the previous week's window.
        $this->assertSame(2, Payout::count());

        $payoutA = Payout::where('owner_id', $ownerA->id)->sole();
        $this->assertSame('18000.00', $payoutA->total_amount);
        $this->assertSame(PayoutBatchStatus::Processed, $payoutA->status);
        $this->assertNotNull($payoutA->processed_at);
        $this->assertEquals(now()->startOfWeek(Carbon::MONDAY)->subWeek(), $payoutA->period_start);

        // Transactions are paid out and linked through the pivot.
        foreach ([$txnA1, $txnA2] as $txn) {
            $txn->refresh();

            $this->assertSame(PayoutStatus::PaidOut, $txn->payout_status);
            $this->assertSame('tr_'.$txn->id, $txn->stripe_transfer_id);
        }

        $this->assertSame(2, $payoutA->transactions()->count());
        $this->assertSame(1, Payout::where('owner_id', $ownerB->id)->sole()->transactions()->count());
    }

    public function test_monthly_run_uses_the_previous_calendar_month_window(): void
    {
        $this->fakeGateway();

        $owner = $this->owner();

        // Held mid-July: inside the previous month's window (Jul 1 - Jul 31).
        $this->eligibleTransaction($owner, heldSince: Carbon::create(2026, 7, 10));

        $this->artisan('payouts:process', ['--period' => 'monthly'])
            ->assertExitCode(0);

        $payout = Payout::sole();

        $this->assertEquals(Carbon::create(2026, 7, 1, 0, 0, 0), $payout->period_start);
        $this->assertEquals(Carbon::create(2026, 7, 31, 23, 59, 59), $payout->period_end);
    }

    public function test_second_run_pays_nothing_twice(): void
    {
        $gateway = $this->fakeGateway();

        $owner = $this->owner();
        $this->eligibleTransaction($owner);

        $this->artisan('payouts:process', ['--period' => 'weekly'])->assertExitCode(0);
        $this->artisan('payouts:process', ['--period' => 'weekly'])->assertExitCode(0);

        $this->assertCount(1, $gateway->transfers);
        $this->assertSame(1, Payout::count());
    }

    public function test_failed_transfer_leaves_transactions_eligible_for_retry(): void
    {
        $owner = $this->owner();

        $okTxn = $this->eligibleTransaction($owner);
        $badTxn = $this->eligibleTransaction($owner);

        $gateway = $this->fakeGateway(function ($txn) use ($badTxn) {
            if ($txn->id === $badTxn->id) {
                throw new \Stripe\Exception\ApiConnectionException('Account closed');
            }
        });

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(1);

        // The owner's whole batch rolled back: nothing was marked paid out.
        $this->assertSame(PayoutStatus::Eligible, $okTxn->fresh()->payout_status);
        $this->assertSame(PayoutStatus::Eligible, $badTxn->fresh()->payout_status);
        $this->assertSame(0, Payout::count());

        // A retry with a working gateway pays both, and the first transfer is
        // called again - Stripe's idempotency key makes that call a no-op.
        $gateway->hook = null;

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        $this->assertSame(PayoutStatus::PaidOut, $okTxn->fresh()->payout_status);
        $this->assertSame(PayoutStatus::PaidOut, $badTxn->fresh()->payout_status);

        // 2 attempts in the failed run + 2 in the retry; the retry re-calls
        // transfer() for the SAME transactions (Stripe's idempotency key makes
        // the repeated calls harmless).
        $this->assertCount(4, $gateway->transfers);
        $this->assertCount(2, array_filter($gateway->transfers, fn ($t) => $t['transaction_id'] === $okTxn->id));
    }

    public function test_owner_without_active_stripe_account_is_skipped(): void
    {
        $this->fakeGateway();

        $owner = $this->owner(['stripe_account_id' => null, 'stripe_transfers_status' => null]);
        $this->eligibleTransaction($owner);

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        $this->assertSame(0, Payout::count());
        $this->assertSame(PayoutStatus::Eligible, Transaction::sole()->payout_status);
    }

    public function test_held_and_non_paid_transactions_are_never_selected(): void
    {
        $gateway = $this->fakeGateway();

        $owner = $this->owner();

        $held = $this->eligibleTransaction($owner);
        $held->update(['payout_status' => PayoutStatus::Held]);

        $pending = $this->eligibleTransaction($owner);
        $pending->update(['status' => PaymentStatus::Pending, 'payout_status' => PayoutStatus::Eligible]);

        $refunded = $this->eligibleTransaction($owner);
        $refunded->update(['status' => PaymentStatus::Refunded]);

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        $this->assertCount(0, $gateway->transfers);
        $this->assertSame(0, Payout::count());
    }

    public function test_successful_transfer_stamps_transferred_at(): void
    {
        $this->fakeGateway();

        $owner = $this->owner();
        $txn = $this->eligibleTransaction($owner);

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        $txn->refresh();

        $this->assertSame(PayoutStatus::PaidOut, $txn->payout_status);
        $this->assertNotNull($txn->transferred_at);
    }

    public function test_disputed_transaction_is_never_paid_out(): void
    {
        $gateway = $this->fakeGateway();

        $owner = $this->owner();
        $txn = $this->eligibleTransaction($owner);
        $txn->update(['disputed_at' => now()]);

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        // Frozen in escrow pending admin review - no transfer, still eligible.
        $this->assertCount(0, $gateway->transfers);
        $this->assertSame(0, Payout::count());
        $this->assertSame(PayoutStatus::Eligible, $txn->fresh()->payout_status);
        $this->assertNull($txn->fresh()->transferred_at);
    }

    public function test_failed_batch_notifies_admins(): void
    {
        Notification::fake();

        $admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03009999999',
            'password' => bcrypt('password'),
        ]);

        $owner = $this->owner();
        $txn = $this->eligibleTransaction($owner);

        $this->fakeGateway(function () {
            throw new \Stripe\Exception\ApiConnectionException('Account closed');
        });

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(1);

        // The transaction stays ready to pay for retry, and the admins know.
        $this->assertSame(PayoutStatus::Eligible, $txn->fresh()->payout_status);

        Notification::assertSentTo(
            $admin,
            PayoutTransferFailedNotification::class,
            fn ($notification) => $notification->pendingCount === 1
                && str_contains($notification->reason, 'Account closed'),
        );
    }

    public function test_unverified_owner_notifies_admins(): void
    {
        Notification::fake();

        $admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03009999999',
            'password' => bcrypt('password'),
        ]);

        // Stripe onboarding incomplete: transfers cannot be issued.
        $owner = $this->owner(['stripe_account_id' => null, 'stripe_transfers_status' => null]);
        $txn = $this->eligibleTransaction($owner);

        $this->artisan('payouts:process', ['--period' => 'weekly'])
            ->assertExitCode(0);

        $this->assertSame(PayoutStatus::Eligible, $txn->fresh()->payout_status);

        Notification::assertSentTo(
            $admin,
            PayoutTransferFailedNotification::class,
            fn ($notification) => str_contains($notification->reason, 'onboarding'),
        );
    }
}
