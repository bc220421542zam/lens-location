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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingCompletionTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);

        $this->customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'Ust',
            'email' => 'cust'.uniqid().'@example.com', 'phone' => '03007654321',
            'password' => bcrypt('password'),
        ]);
    }

    private function booking(BookingStatus $status, ?Carbon $bookingDate = null): Booking
    {
        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        return Booking::create([
            'location_id'  => $location->id,
            'customer_id'  => $this->customer->id,
            // Defaults to an already-started shoot: visiting should only ever
            // happen once the booking date has arrived.
            'booking_date' => $bookingDate ?? now()->subDay(),
            'hours'        => 4,
            'total_price'  => 5200,
            'status'       => $status,
        ]);
    }

    private function paidTransaction(Booking $booking): Transaction
    {
        return Transaction::create([
            'booking_id'    => $booking->id,
            'customer_id'   => $this->customer->id,
            'owner_id'      => $this->owner->id,
            'amount'        => 5200,
            'platform_fee'  => 520,
            'owner_earning' => 4680,
            'status'        => PaymentStatus::Paid,
            'payout_status' => PayoutStatus::Held,
            'paid_at'       => now(),
            'held_since'    => now(),
        ]);
    }

    public function test_paid_started_confirmed_booking_is_visited_for_the_customer(): void
    {
        // A payment the webhook never promoted: the visit pass catches a
        // confirmed booking straight up to visited.
        $booking = $this->booking(BookingStatus::Confirmed);
        $transaction = $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);

        // Visiting releases the escrow and stamps the split.
        $transaction->refresh();

        $this->assertSame(PayoutStatus::Eligible, $transaction->payout_status);
        $this->assertSame('520.00', $transaction->platform_commission);
        $this->assertSame('4680.00', $transaction->owner_payout_amount);
    }

    public function test_paid_started_confirmed_booking_is_visited_for_the_owner(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);
        $this->paidTransaction($booking);

        BookingCompleter::forOwner($this->owner->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::Eligible, Transaction::where('booking_id', $booking->id)->sole()->payout_status);
    }

    public function test_paid_completed_booking_is_visited_once_the_booking_date_passes(): void
    {
        // The normal paid path: payment completed the booking, and the settle
        // pass moves it to visited once the date has arrived.
        $booking = $this->booking(BookingStatus::Completed);
        $transaction = $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::Eligible, $transaction->fresh()->payout_status);
    }

    public function test_paid_completed_future_booking_stays_completed(): void
    {
        $booking = $this->booking(BookingStatus::Completed, now()->addDay());
        $transaction = $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        // Paid and the shoot is still ahead - not visited, escrow stays held.
        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::Held, $transaction->fresh()->payout_status);
    }

    public function test_visiting_twice_is_idempotent_for_the_transaction(): void
    {
        $booking = $this->booking(BookingStatus::Completed);
        $transaction = $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);
        BookingCompleter::forCustomer($this->customer->id);

        // The second run must not re-stamp the split or move the status again.
        $this->assertSame(PayoutStatus::Eligible, $transaction->fresh()->payout_status);
    }

    public function test_unpaid_confirmed_booking_stays_confirmed(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        BookingCompleter::forCustomer($this->customer->id);

        // Expiring is the expiry pass's job - the visit pass leaves it alone.
        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_booking_with_only_a_pending_transaction_stays_confirmed(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        Transaction::create([
            'booking_id'    => $booking->id,
            'customer_id'   => $this->customer->id,
            'owner_id'      => $this->owner->id,
            'amount'        => 5200,
            'platform_fee'  => 520,
            'owner_earning' => 4680,
            'status'        => PaymentStatus::Pending,
        ]);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_cancelled_booking_is_never_resurrected(): void
    {
        $booking = $this->booking(BookingStatus::Cancelled);
        $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Cancelled, $booking->fresh()->status);
    }

    public function test_pending_booking_is_not_visited(): void
    {
        $booking = $this->booking(BookingStatus::Pending);
        $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Pending, $booking->fresh()->status);
    }

    public function test_another_customers_booking_is_untouched(): void
    {
        $booking = $this->booking(BookingStatus::Completed);
        $this->paidTransaction($booking);

        $other = User::create([
            'role' => 'customer', 'first_name' => 'X', 'last_name' => 'Y',
            'email' => 'other'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        BookingCompleter::forCustomer($other->id);

        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
    }

    public function test_bookings_page_visits_on_load_and_shows_review_action(): void
    {
        $booking = $this->booking(BookingStatus::Completed);
        $this->paidTransaction($booking);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings'))
            ->assertOk()
            ->assertSee('Leave a Review');

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
    }

    public function test_owner_bookings_page_visits_on_load(): void
    {
        $booking = $this->booking(BookingStatus::Completed);
        $this->paidTransaction($booking);

        $this->actingAs($this->owner)
            ->get(route('owner.bookings'))
            ->assertOk();

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
    }

    public function test_paid_future_booking_stays_confirmed_until_the_booking_date_passes(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed, now()->addDay());
        $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        // Paid, but the shoot hasn't happened yet - not visited.
        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);

        Carbon::setTestNow(now()->addDays(2));

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_booking_is_visited_once_the_start_time_passes_even_mid_shoot(): void
    {
        // Started an hour ago but still booked for 4 hours: the slot is lost
        // to the owner from the start time, so the booking is "visited" now.
        $booking = $this->booking(BookingStatus::Completed, now()->subHour());
        $transaction = $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::Eligible, $transaction->fresh()->payout_status);
    }

    public function test_same_day_booking_is_visited_and_released_for_payout_by_the_settle_command(): void
    {
        // booking_date = today: the settle pass must visit the booking the
        // same day it runs, not wait for the date to be strictly in the past.
        $booking = $this->booking(BookingStatus::Completed, now()->startOfDay());
        $transaction = $this->paidTransaction($booking);

        $this->artisan('bookings:settle')
            ->expectsOutput('Settled bookings: 1 visited, 0 expired.')
            ->assertExitCode(0);

        $this->assertSame(BookingStatus::Visited, $booking->fresh()->status);

        // Visited -> Transfer Status: Ready to Pay (eligible for the payout
        // batch, which then moves it to Transferred).
        $this->assertSame(PayoutStatus::Eligible, $transaction->fresh()->payout_status);
    }

    public function test_disputed_transaction_blocks_visit_and_escrow_stays_held(): void
    {
        $booking = $this->booking(BookingStatus::Completed);
        $transaction = $this->paidTransaction($booking);

        $transaction->update(['disputed_at' => now()]);

        BookingCompleter::forCustomer($this->customer->id);

        // Flagged for admin review: no auto-visit, no payout release.
        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
        $this->assertSame(PayoutStatus::Held, $transaction->fresh()->payout_status);
    }
}
