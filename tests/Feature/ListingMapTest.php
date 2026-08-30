<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingMapTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_create_page_renders_the_map_picker(): void
    {
        $this->actingAs($this->owner)
            ->get(route('owner.locations.create'))
            ->assertOk()
            ->assertSee('Map Location')
            ->assertSee('listing-map')
            ->assertSee('name="latitude"', escape: false)
            ->assertSee('name="longitude"', escape: false)
            ->assertSee('listingMapPicker');
    }

    public function test_edit_page_initializes_the_pin_at_saved_coordinates(): void
    {
        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
            'latitude' => 31.5204, 'longitude' => 74.3587,
        ]);

        $this->actingAs($this->owner)
            ->get(route('owner.locations.edit', $location))
            ->assertOk()
            ->assertSee('Map Location')
            ->assertSee('31.5204')
            ->assertSee('74.3587');
    }

    public function test_store_requires_coordinates(): void
    {
        $this->actingAs($this->owner)
            ->post(route('owner.locations.store'), [])
            ->assertSessionHasErrors(['latitude', 'longitude']);

        $this->assertSame(0, Location::count());
    }

    public function test_update_requires_coordinates(): void
    {
        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
            'latitude' => 31.5204, 'longitude' => 74.3587,
        ]);

        $this->actingAs($this->owner)
            ->put(route('owner.locations.update', $location), [
                'title' => 'Studio', 'category' => 'studio', 'price_per_hour' => 1300,
                'description' => 'd', 'address' => 'a', 'city' => 'Lahore',
            ])
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_coordinates_persist_on_the_model(): void
    {
        $location = Location::create([
            'user_id' => $this->owner->id, 'title' => 'Studio', 'description' => 'd',
            'address' => 'a', 'city' => 'Lahore', 'category' => 'studio',
            'price_per_hour' => 1300, 'status' => ListingStatus::Approved,
            'latitude' => 31.5204, 'longitude' => 74.3587,
        ]);

        $this->assertSame(31.5204, (float) $location->fresh()->latitude);
        $this->assertSame(74.3587, (float) $location->fresh()->longitude);
    }
}
