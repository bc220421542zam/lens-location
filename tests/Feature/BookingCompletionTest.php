<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Support\BookingCompleter;
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

    private function booking(BookingStatus $status): Booking
    {
        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        return Booking::create([
            'location_id'  => $location->id,
            'customer_id'  => $this->customer->id,
            'booking_date' => now()->addDay(),
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
            'paid_at'       => now(),
        ]);
    }

    public function test_paid_confirmed_booking_completes_for_the_customer(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);
        $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
    }

    public function test_paid_confirmed_booking_completes_for_the_owner(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);
        $this->paidTransaction($booking);

        BookingCompleter::forOwner($this->owner->id);

        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
    }

    public function test_unpaid_confirmed_booking_stays_confirmed(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        BookingCompleter::forCustomer($this->customer->id);

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

    public function test_pending_booking_is_not_completed(): void
    {
        $booking = $this->booking(BookingStatus::Pending);
        $this->paidTransaction($booking);

        BookingCompleter::forCustomer($this->customer->id);

        $this->assertSame(BookingStatus::Pending, $booking->fresh()->status);
    }

    public function test_another_customers_booking_is_untouched(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);
        $this->paidTransaction($booking);

        $other = User::create([
            'role' => 'customer', 'first_name' => 'X', 'last_name' => 'Y',
            'email' => 'other'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        BookingCompleter::forCustomer($other->id);

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_bookings_page_completes_on_load_and_shows_review_action(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);
        $this->paidTransaction($booking);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings'))
            ->assertOk()
            ->assertSee('Leave a Review');

        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
    }

    public function test_owner_bookings_page_completes_on_load(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);
        $this->paidTransaction($booking);

        $this->actingAs($this->owner)
            ->get(route('owner.bookings'))
            ->assertOk();

        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);
    }
}
