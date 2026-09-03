<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRenderSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_dashboards_render(): void
    {
        $customer = User::create([
            'role' => 'customer', 'first_name' => 'C', 'last_name' => 'U',
            'email' => 'c'.uniqid().'@example.com', 'phone' => '03001111111',
            'password' => bcrypt('password'),
        ]);
        $owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'W',
            'email' => 'o'.uniqid().'@example.com', 'phone' => '03002222222',
            'password' => bcrypt('password'),
        ]);
        $admin = User::create([
            'role' => 'admin', 'first_name' => 'A', 'last_name' => 'D',
            'email' => 'a'.uniqid().'@example.com', 'phone' => '03003333333',
            'password' => bcrypt('password'),
        ]);

        $location = Location::create([
            'user_id' => $owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'Studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
        ]);

        Booking::create([
            'customer_id' => $customer->id, 'location_id' => $location->id,
            'booking_date' => now(), 'start_time' => now()->format('H:i'),
            'end_time' => now()->addHour()->format('H:i'),
            'hours' => 1, 'total_price' => 1300, 'status' => 'completed',
        ]);

        $this->actingAs($customer)->get(route('customer.dashboard'))->assertOk();
        $this->actingAs($owner)->get(route('owner.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }
}
