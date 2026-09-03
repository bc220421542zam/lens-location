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

class BookingExpiryTest extends TestCase
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
            'booking_date' => $bookingDate ?? now()->subDay(),
            'hours'        => 4,
            'total_price'  => 5200,
            'status'       => $status,
        ]);
    }

    public function test_unpaid_confirmed_booking_expires_once_the_booking_date_passes(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        BookingCompleter::expireUnpaidForCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Expired, $booking->fresh()->status);
    }

    public function test_unpaid_future_booking_stays_confirmed(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed, now()->addDay());

        BookingCompleter::expireUnpaidForCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_paid_booking_does_not_expire(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        Transaction::create([
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

        BookingCompleter::expireUnpaidForCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_cancelled_and_completed_bookings_are_never_expired(): void
    {
        $cancelled = $this->booking(BookingStatus::Cancelled);
        $completed = $this->booking(BookingStatus::Completed);

        BookingCompleter::expireUnpaidForCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Cancelled, $cancelled->fresh()->status);
        $this->assertSame(BookingStatus::Completed, $completed->fresh()->status);
    }

    public function test_another_customers_booking_is_untouched(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        $other = User::create([
            'role' => 'customer', 'first_name' => 'X', 'last_name' => 'Y',
            'email' => 'other'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        BookingCompleter::expireUnpaidForCustomer($other->id);

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_expired_booking_is_never_completed_and_never_paid_out(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        BookingCompleter::expireUnpaidForCustomer($this->customer->id);
        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Expired, $booking->fresh()->status);
        $this->assertSame(0, Transaction::count());
    }

    public function test_settle_command_expires_and_visits(): void
    {
        $unpaid = $this->booking(BookingStatus::Confirmed);
        $paid   = $this->booking(BookingStatus::Confirmed);

        Transaction::create([
            'booking_id'    => $paid->id,
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

        $this->artisan('bookings:settle')
            ->expectsOutput('Settled bookings: 1 visited, 1 expired.')
            ->assertExitCode(0);

        $this->assertSame(BookingStatus::Expired, $unpaid->fresh()->status);
        $this->assertSame(BookingStatus::Visited, $paid->fresh()->status);
    }

    public function test_expired_booking_cannot_be_paid(): void
    {
        $booking = $this->booking(BookingStatus::Expired);

        $this->actingAs($this->customer)
            ->post(route('customer.bookings.pay', $booking->id))
            ->assertRedirect()
            ->assertSessionHas('error', 'This booking has expired - the payment window closed at the booking date.');

        $this->assertSame(0, Transaction::count());
    }

    public function test_pay_is_blocked_once_the_booking_date_passes_even_before_settlement(): void
    {
        // Still `confirmed` - the settle pass hasn't run - but the booking
        // date has arrived, so the payment window is closed regardless.
        $booking = $this->booking(BookingStatus::Confirmed, now()->subHour());

        $this->actingAs($this->customer)
            ->post(route('customer.bookings.pay', $booking->id))
            ->assertRedirect()
            ->assertSessionHas('error', 'This booking has expired - the payment window closed at the booking date.');

        $this->assertSame(0, Transaction::count());
    }
}
