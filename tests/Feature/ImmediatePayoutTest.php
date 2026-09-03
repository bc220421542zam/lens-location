<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Support\BookingCompleter;
use App\Support\StripeGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImmediatePayoutTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $customer;

    private int $ownerSeq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
            'stripe_account_id' => 'acct_test_1',
            'stripe_transfers_status' => 'active',
        ]);

        $this->customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'Ust',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);
    }

    private function onboardedOwner(): User
    {
        $this->ownerSeq++;

        return User::create([
            'role' => 'owner', 'first_name' => 'X', 'last_name' => 'Yz',
            'email' => 'ownerx'.$this->ownerSeq.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
            'stripe_account_id' => 'acct_test_'.$this->ownerSeq,
            'stripe_transfers_status' => 'active',
        ]);
    }

    private function booking(BookingStatus $status = BookingStatus::Completed, ?User $owner = null): Booking
    {
        $location = Location::create([
            'user_id' => ($owner ?? $this->owner)->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        return Booking::create([
            'location_id'  => $location->id,
            'customer_id'  => $this->customer->id,
            'booking_date' => now()->subDay(),
            'hours'        => 4,
            'total_price'  => 5200,
            'status'       => $status,
        ]);
    }

    private function paidHeldTransaction(Booking $booking, ?User $owner = null): Transaction
    {
        return Transaction::create([
            'booking_id'    => $booking->id,
            'customer_id'   => $this->customer->id,
            'owner_id'      => ($owner ?? $this->owner)->id,
            'amount'        => 5200,
            'platform_fee'  => 520,
            'owner_earning' => 4680,
            'status'        => PaymentStatus::Paid,
            'payout_status' => PayoutStatus::Held,
            'paid_at'       => now(),
            'held_since'    => now(),
        ]);
    }

    /**
     * Swap the gateway for a stub recording every transfer call. The optional
     * hook can throw to simulate a Stripe failure.
     */
    private function fakeGateway(?\Closure $hook = null): object
    {
        $stub = new class extends StripeGateway
        {
            public array $transfers = [];

            public ?\Closure $hook = null;

            public function __construct() {}

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

    public function test_visiting_a_booking_transfers_the_owners_share_immediately(): void
    {
        $gateway = $this->fakeGateway();

        $booking = $this->booking();
        $txn = $this->paidHeldTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);

        // The 90/10 split is stamped, then the transfer fires on the spot.
        $txn->refresh();

        $this->assertSame(PayoutStatus::PaidOut, $txn->payout_status);
        $this->assertSame('520.00', $txn->platform_commission);
        $this->assertSame('4680.00', $txn->owner_payout_amount);
        $this->assertSame('tr_'.$txn->id, $txn->stripe_transfer_id);
        $this->assertNotNull($txn->transferred_at);

        $this->assertCount(1, $gateway->transfers);
        $this->assertSame($txn->id, $gateway->transfers[0]['transaction_id']);
        $this->assertSame($this->owner->id, $gateway->transfers[0]['owner_id']);
    }

    public function test_transfer_fires_once_per_visited_booking(): void
    {
        $gateway = $this->fakeGateway();

        $booking = $this->booking();
        $this->paidHeldTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);
        BookingCompleter::forCustomer($this->customer->id);

        // The second pass finds nothing still eligible - no double transfer.
        $this->assertCount(1, $gateway->transfers);
    }

    public function test_owner_without_active_stripe_account_stays_eligible(): void
    {
        $gateway = $this->fakeGateway();

        // The owner never finished Stripe onboarding.
        $owner = User::create([
            'role' => 'owner', 'first_name' => 'N', 'last_name' => 'O',
            'email' => 'noob'.uniqid().'@example.com', 'phone' => '03009998888',
            'password' => bcrypt('password'),
        ]);

        $booking = $this->booking(owner: $owner);
        $txn = $this->paidHeldTransaction($booking, $owner);

        BookingCompleter::forCustomer($this->customer->id);

        // The visit itself still happened; the money waits for the batch.
        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);

        $txn->refresh();

        $this->assertSame(PayoutStatus::Eligible, $txn->payout_status);
        $this->assertNull($txn->stripe_transfer_id);
        $this->assertNull($txn->transferred_at);
        $this->assertCount(0, $gateway->transfers);
    }

    public function test_failed_transfer_stays_eligible_and_visit_is_unaffected(): void
    {
        $gateway = $this->fakeGateway(fn () => throw new \RuntimeException('Stripe down'));

        $booking = $this->booking();
        $txn = $this->paidHeldTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);

        // Money never left the platform: eligible, no transfer id, no date.
        $txn->refresh();

        $this->assertSame(PayoutStatus::Eligible, $txn->payout_status);
        $this->assertNull($txn->stripe_transfer_id);
        $this->assertNull($txn->transferred_at);
        $this->assertCount(1, $gateway->transfers);
    }

    public function test_disputed_transaction_is_never_transferred(): void
    {
        $gateway = $this->fakeGateway();

        $booking = $this->booking();
        $txn = $this->paidHeldTransaction($booking);
        $txn->update(['disputed_at' => now()]);

        BookingCompleter::forCustomer($this->customer->id);

        // Disputes freeze the whole booking out of the visit pass.
        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::Held, $txn->fresh()->payout_status);
        $this->assertCount(0, $gateway->transfers);
    }

    public function test_bookings_page_load_visits_and_transfers(): void
    {
        $gateway = $this->fakeGateway();

        $booking = $this->booking();
        $txn = $this->paidHeldTransaction($booking);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings'))
            ->assertOk();

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::PaidOut, $txn->fresh()->payout_status);
        $this->assertNotNull($txn->fresh()->transferred_at);
        $this->assertCount(1, $gateway->transfers);
    }

    public function test_settle_command_transfers_at_visit_time(): void
    {
        $gateway = $this->fakeGateway();

        $booking = $this->booking();
        $txn = $this->paidHeldTransaction($booking);

        $this->artisan('bookings:settle')->assertExitCode(0);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::PaidOut, $txn->fresh()->payout_status);
        $this->assertCount(1, $gateway->transfers);
    }
}
