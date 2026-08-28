<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingDetailsTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $customer;

    private Location $location;

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

        $this->location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);
    }

    private function booking(BookingStatus $status): Booking
    {
        return Booking::create([
            'location_id' => $this->location->id,
            'customer_id' => $this->customer->id,
            'booking_date' => now()->subDay(),
            'hours' => 4,
            'total_price' => 5200,
            'status' => $status,
        ]);
    }

    public function test_customer_can_view_details_for_every_status(): void
    {
        foreach (BookingStatus::cases() as $status) {
            $booking = $this->booking($status);

            $this->actingAs($this->customer)
                ->get(route('customer.bookings.show', $booking))
                ->assertOk()
                ->assertSee($booking->location->title);
        }
    }

    public function test_another_customers_booking_details_are_forbidden(): void
    {
        $booking = $this->booking(BookingStatus::Confirmed);

        $intruder = User::create([
            'role' => 'customer', 'first_name' => 'X', 'last_name' => 'Y',
            'email' => 'other'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($intruder)
            ->get(route('customer.bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_bookings_list_shows_the_view_link_for_every_status(): void
    {
        $booking = $this->booking(BookingStatus::Cancelled);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings'))
            ->assertOk()
            ->assertSee(route('customer.bookings.show', $booking), escape: false);
    }
}
