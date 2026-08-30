<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowseListingsTest extends TestCase
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

    public function test_category_dropdown_comes_from_the_categories_table(): void
    {
        Category::create(['name' => 'Rooftop']);

        $this->actingAs($this->customer)
            ->get(route('customer.listings'))
            ->assertOk()
            // A category with zero listings still appears, unlike the old
            // distinct-strings-from-locations behaviour.
            ->assertSee('Rooftop');
    }

    public function test_category_filter_is_case_insensitive(): void
    {
        $studio = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $garden = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Garden', 'description' => 'd',
            'address' => 'b', 'city' => 'Lahore', 'category' => 'garden',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        // Filtering with the Title Case name still matches the lowercase rows.
        $this->actingAs($this->customer)
            ->get(route('customer.listings', ['category' => 'Studio']))
            ->assertOk()
            ->assertSee($studio->title)
            ->assertDontSee($garden->title);
    }

    public function test_search_matches_the_address(): void
    {
        $studio = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Studio', 'description' => 'd',
            'address' => '12 Gulberg Boulevard', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $garden = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Garden', 'description' => 'd',
            'address' => 'b', 'city' => 'Karachi', 'category' => 'garden',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $this->actingAs($this->customer)
            ->get(route('customer.listings', ['search' => 'Gulberg']))
            ->assertOk()
            ->assertSee($studio->title)
            ->assertDontSee($garden->title);
    }

    public function test_search_no_longer_matches_the_description(): void
    {
        $studio = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Studio',
            'description' => 'quiet lakeside hideaway',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $this->actingAs($this->customer)
            ->get(route('customer.listings', ['search' => 'hideaway']))
            ->assertOk()
            ->assertDontSee($studio->title);
    }

    public function test_payments_pages_reference_the_booking(): void
    {
        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'The Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        $booking = Booking::create([
            'location_id' => $location->id,
            'customer_id' => $this->customer->id,
            'booking_date' => now()->subDay(),
            'hours' => 4,
            'total_price' => 5200,
            'status' => 'confirmed',
        ]);

        Transaction::create([
            'booking_id' => $booking->id,
            'customer_id' => $this->customer->id,
            'owner_id' => $this->owner->id,
            'amount' => 5200,
            'platform_fee' => 520,
            'owner_earning' => 4680,
            'status' => PaymentStatus::Paid,
        ]);

        // Customer history links the row back to the booking details page.
        $this->actingAs($this->customer)
            ->get(route('customer.payments'))
            ->assertOk()
            ->assertSee(route('customer.bookings.show', $booking), escape: false);

        $admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'Dmin',
            'email' => 'admin'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments'))
            ->assertOk()
            ->assertSee('#'.$booking->id);
    }
}
