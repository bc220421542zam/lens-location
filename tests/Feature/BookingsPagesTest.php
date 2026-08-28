<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $owner;

    private User $customer;

    private Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

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

        $this->location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);
    }

    private function booking(BookingStatus $status, ?User $customer = null): Booking
    {
        return Booking::create([
            'location_id' => $this->location->id,
            'customer_id' => ($customer ?? $this->customer)->id,
            'booking_date' => now()->addDay(),
            'hours' => 4,
            'total_price' => 5200,
            'status' => $status,
        ]);
    }

    public function test_admin_bookings_page_lists_bookings_with_view_link(): void
    {
        $booking = $this->booking(BookingStatus::Pending);

        $this->actingAs($this->admin)
            ->get(route('admin.bookings'))
            ->assertOk()
            ->assertSee('#'.$booking->id)
            ->assertSee($this->customer->first_name)
            ->assertSee($this->location->title)
            ->assertSee(route('admin.bookings.show', $booking), escape: false);
    }

    public function test_admin_bookings_page_filters_by_status(): void
    {
        $pending = $this->booking(BookingStatus::Pending);
        $completed = $this->booking(BookingStatus::Completed);

        $this->actingAs($this->admin)
            ->get(route('admin.bookings', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('#'.$completed->id)
            ->assertDontSee('#'.$pending->id);
    }

    public function test_admin_booking_details_page_renders_both_parties(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'owner_id' => $this->owner->id,
            'amount' => 5200,
            'platform_fee' => 520,
            'owner_earning' => 4680,
            'status' => PaymentStatus::Paid,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.bookings.show', $booking))
            ->assertOk()
            ->assertSee($this->customer->email)
            ->assertSee($this->owner->name)
            ->assertSee($this->location->title)
            ->assertSee('Paid');
    }

    public function test_admin_payments_booking_cell_links_to_booking_details(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'owner_id' => $this->owner->id,
            'amount' => 5200,
            'platform_fee' => 520,
            'owner_earning' => 4680,
            'status' => PaymentStatus::Paid,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.payments'))
            ->assertOk()
            ->assertSee(route('admin.bookings.show', $booking), escape: false);
    }

    public function test_owner_booking_details_page_renders_for_the_listing_owner(): void
    {
        $booking = $this->booking(BookingStatus::Pending);

        $this->actingAs($this->owner)
            ->get(route('owner.bookings.show', $booking))
            ->assertOk()
            ->assertSee($this->customer->name)
            ->assertSee($this->location->title)
            ->assertSee('Accept');
    }

    public function test_owner_booking_details_are_forbidden_for_another_owner(): void
    {
        $booking = $this->booking(BookingStatus::Pending);

        $otherOwner = User::create([
            'role' => 'owner', 'first_name' => 'X', 'last_name' => 'Y',
            'email' => 'other'.uniqid().'@example.com', 'phone' => '03001113333',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($otherOwner)
            ->get(route('owner.bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_owner_bookings_list_shows_view_link(): void
    {
        $booking = $this->booking(BookingStatus::Completed);

        $this->actingAs($this->owner)
            ->get(route('owner.bookings'))
            ->assertOk()
            ->assertSee(route('owner.bookings.show', $booking), escape: false);
    }

    public function test_owner_earnings_page_links_to_booking_details(): void
    {
        $booking = $this->booking(BookingStatus::Completed);

        Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'owner_id' => $this->owner->id,
            'amount' => 5200,
            'platform_fee' => 520,
            'owner_earning' => 4680,
            'status' => PaymentStatus::Paid,
        ]);

        $this->actingAs($this->owner)
            ->get(route('owner.earnings'))
            ->assertOk()
            ->assertSee(route('owner.bookings.show', $booking), escape: false);
    }
}
