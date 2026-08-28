<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\LocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocationSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_seeder_creates_twelve_approved_listings_per_category(): void
    {
        $this->owner();

        foreach (['Studio', 'Outdoor', 'Rooftop', 'Indoor', 'Garden'] as $name) {
            Category::create(['name' => $name]);
        }

        $this->seed(LocationSeeder::class);

        $this->assertSame(60, Location::count());

        foreach (['studio', 'outdoor', 'rooftop', 'indoor', 'garden'] as $category) {
            $this->assertSame(
                12,
                Location::where('category', $category)->count(),
                "expected 12 {$category} listings",
            );
        }
    }

    public function test_seeded_listings_are_approved_owned_and_category_linked(): void
    {
        $owner = $this->owner();

        foreach (['Studio', 'Outdoor', 'Rooftop', 'Indoor', 'Garden'] as $name) {
            Category::create(['name' => $name]);
        }

        $this->seed(LocationSeeder::class);

        foreach (Location::all() as $listing) {
            $this->assertSame($owner->id, $listing->user_id);
            $this->assertSame(ListingStatus::Approved, $listing->status);
            $this->assertNotNull($listing->category_id);
            $this->assertTrue($listing->price_per_hour > 0);
        }
    }

    public function test_seeded_listings_get_a_category_photo(): void
    {
        $this->owner();

        foreach (['Studio', 'Outdoor', 'Rooftop', 'Indoor', 'Garden'] as $name) {
            Category::create(['name' => $name]);
        }

        $this->seed(LocationSeeder::class);

        foreach (Location::all() as $listing) {
            $this->assertStringStartsWith(
                'placeholders/'.$listing->category.'/',
                $listing->image,
            );
            $this->assertStringEndsWith('.jpg', $listing->image);
            $this->assertTrue(Storage::disk('public')->exists($listing->image));
        }
    }

    public function test_seeder_falls_back_to_svg_when_bundled_photos_are_missing(): void
    {
        $this->owner();

        $this->seed(LocationSeeder::class);

        // Pretend the bundled photos were never committed: the seeder must
        // degrade to the generated SVG rather than error.
        Storage::disk('public')->deleteDirectory('placeholders');
        rename(database_path('seeders/images/studio'), database_path('seeders/images/studio-tmp'));

        try {
            $this->seed(LocationSeeder::class);

            $this->assertTrue(Location::where('category', 'studio')->get()->every(
                fn ($listing) => Storage::disk('public')->exists($listing->image),
            ));
        } finally {
            rename(database_path('seeders/images/studio-tmp'), database_path('seeders/images/studio'));
        }
    }

    public function test_seeder_backfills_images_on_rerun(): void
    {
        $this->owner();

        $this->seed(LocationSeeder::class);

        // Simulate rows created before placeholders existed.
        Location::query()->update(['image' => null]);

        $this->seed(LocationSeeder::class);

        $this->assertSame(60, Location::count());
        $this->assertSame(0, Location::whereNull('image')->count());
    }

    public function test_seeder_is_idempotent_and_reuses_any_owner(): void
    {
        $this->owner();

        $this->seed(LocationSeeder::class);
        $this->seed(LocationSeeder::class);

        $this->assertSame(60, Location::count());
    }

    private function owner(): User
    {
        return User::create([
            'role' => 'owner', 'first_name' => 'O', 'last_name' => 'Wner',
            'email' => 'owner'.uniqid().'@example.com', 'phone' => '03001234567',
            'password' => bcrypt('password'),
        ]);
    }
}
