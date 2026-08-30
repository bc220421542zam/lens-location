<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerLocationGalleryTest extends TestCase
{
    use RefreshDatabase;

    private function listing(array $images): Location
    {
        $owner = User::create([
            'role'       => 'owner',
            'first_name' => 'Test',
            'last_name'  => 'Owner',
            'email'      => 'owner'.uniqid().'@example.com',
            'phone'      => '03001234567',
            'password'   => bcrypt('password'),
        ]);

        return Location::create([
            'user_id'        => $owner->id,
            'title'          => 'Rooftop Studio',
            'description'    => 'A nice place',
            'address'        => '12 Main Street',
            'city'           => 'Lahore',
            'category'       => 'studio',
            'price_per_hour' => 3000,
            'image'          => $images[0],
            'images'         => $images,
            'status'         => ListingStatus::Approved,
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title'          => 'Rooftop Studio',
            'description'    => 'A nice place',
            'address'        => '12 Main Street',
            'city'           => 'Lahore',
            'category'       => 'studio',
            'price_per_hour' => 3000,
            'latitude'       => 31.5204,
            'longitude'      => 74.3587,
        ], $overrides);
    }

    public function test_owner_can_replace_a_photo_and_choose_a_new_cover(): void
    {
        Storage::fake('public');

        foreach (['locations/a.jpg', 'locations/b.jpg', 'locations/c.jpg'] as $path) {
            Storage::disk('public')->put($path, 'x');
        }

        $location = $this->listing(['locations/a.jpg', 'locations/b.jpg', 'locations/c.jpg']);

        $response = $this->actingAs($location->owner)->put(
            route('owner.locations.update', $location),
            $this->payload([
                // b is dropped, a fresh upload takes the cover slot.
                'image_order' => ['new:0', 'locations/c.jpg', 'locations/a.jpg'],
                'images'      => [UploadedFile::fake()->image('new.jpg')],
            ])
        );

        $response->assertRedirect(route('owner.listings'));

        $location->refresh();
        $gallery = $location->gallery();

        $this->assertCount(3, $gallery);
        $this->assertSame(['locations/c.jpg', 'locations/a.jpg'], array_slice($gallery, 1));
        $this->assertSame($gallery[0], $location->image);
        Storage::disk('public')->assertExists($gallery[0]);
        Storage::disk('public')->assertMissing('locations/b.jpg');
    }

    public function test_owner_cannot_drop_below_the_minimum_gallery_size(): void
    {
        Storage::fake('public');

        $location = $this->listing(['locations/a.jpg', 'locations/b.jpg', 'locations/c.jpg']);

        $this->actingAs($location->owner)
            ->put(route('owner.locations.update', $location), $this->payload([
                'image_order' => ['locations/a.jpg'],
            ]))
            ->assertSessionHasErrors('images');

        $this->assertCount(3, $location->refresh()->gallery());
    }

    public function test_unknown_paths_cannot_be_smuggled_into_the_gallery(): void
    {
        Storage::fake('public');

        $location = $this->listing(['locations/a.jpg', 'locations/b.jpg', 'locations/c.jpg']);

        $this->actingAs($location->owner)
            ->put(route('owner.locations.update', $location), $this->payload([
                'image_order' => ['locations/a.jpg', 'locations/b.jpg', 'locations/c.jpg', '../../.env'],
            ]))
            ->assertRedirect(route('owner.listings'));

        $this->assertSame(
            ['locations/a.jpg', 'locations/b.jpg', 'locations/c.jpg'],
            $location->refresh()->gallery()
        );
    }

    public function test_a_legacy_single_image_listing_can_still_be_edited(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('locations/old.jpg', 'x');

        $location = $this->listing(['locations/old.jpg']);
        $location->update(['images' => null]);   // pre-gallery listing

        $this->actingAs($location->owner)
            ->put(route('owner.locations.update', $location), $this->payload([
                'title'       => 'Renamed',
                'image_order' => ['locations/old.jpg'],
            ]))
            ->assertRedirect(route('owner.listings'));

        $location->refresh();

        $this->assertSame('Renamed', $location->title);
        $this->assertSame(['locations/old.jpg'], $location->gallery());
    }
}
