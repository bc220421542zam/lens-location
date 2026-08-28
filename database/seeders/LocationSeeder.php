<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds realistic, Pakistan-localized listings - 12 per category across the
 * five admin-managed categories (Studio, Outdoor, Rooftop, Indoor, Garden).
 *
 * Listings are distributed round-robin across existing active owners (or one
 * demo owner is created when none exist), marked Approved so they show up in
 * the customer browse page immediately, and their category_id is backfilled
 * from the categories table. Idempotent: re-runs skip titles already present.
 *
 *   php artisan db:seed --class=LocationSeeder
 */
class LocationSeeder extends Seeder
{
    /**
     * @var array<int, array{title: string, description: string, address: string, city: string, category: string, price: int}>
     */
    private const LISTINGS = [
        // ------------------------------------------------------------ Studio
        ['title' => 'Lumina Studio', 'category' => 'studio', 'city' => 'Lahore', 'address' => 'Sector Y, DHA Phase 6', 'price' => 8000,
            'description' => 'A bright white-cyclorama studio with natural window light, motorized backdrops and a dedicated makeup corner. Ideal for fashion and product shoots.'],
        ['title' => 'White Canvas Studio', 'category' => 'studio', 'city' => 'Lahore', 'address' => 'Gulberg III, Main Boulevard', 'price' => 6500,
            'description' => 'Minimal, all-white interiors with high ceilings, softboxes, reflectors and a lounge area. Great for editorial and catalogue work.'],
        ['title' => 'Northlight Photo Studio', 'category' => 'studio', 'city' => 'Islamabad', 'address' => 'F-7 Markaz, Street 20', 'price' => 9000,
            'description' => 'North-facing glass wall gives soft, even daylight all afternoon. Includes colour gels, flash triggers and a tethering station.'],
        ['title' => 'Frame One Studios', 'category' => 'studio', 'city' => 'Karachi', 'address' => 'Bahadurabad, Block 3', 'price' => 7000,
            'description' => 'Converted warehouse studio with exposed brick, moody corners and a large infinity cove. Popular for music videos and portraits.'],
        ['title' => 'The Velvet Room', 'category' => 'studio', 'city' => 'Lahore', 'address' => 'MM Alam Road, Gulberg', 'price' => 12000,
            'description' => 'Luxury studio with velvet drapes, antique furniture props and dramatic tungsten lighting. Suits bridal and couture campaigns.'],
        ['title' => 'Aperture Works', 'category' => 'studio', 'city' => 'Islamabad', 'address' => 'G-9 Markaz, Plaza 12', 'price' => 5500,
            'description' => 'Compact budget-friendly studio with seamless paper rolls, LED panels and a small product table. Perfect for startups and e-commerce.'],
        ['title' => 'Studio 21', 'category' => 'studio', 'city' => 'Karachi', 'address' => 'Clifton Block 4, Schon Circle', 'price' => 8500,
            'description' => 'Sea-facing studio with both daylight and blackout zones, a hair and makeup suite, and an on-site styling rail.'],
        ['title' => 'Mono Studio', 'category' => 'studio', 'city' => 'Lahore', 'address' => 'Johar Town, Emporium Mall Road', 'price' => 5000,
            'description' => 'Monochrome-themed studio with grey-toned corners, floating shelves and a neon wall. Great for content creators.'],
        ['title' => 'Chroma Studio', 'category' => 'studio', 'city' => 'Islamabad', 'address' => 'Blue Area, Jinnah Avenue', 'price' => 7500,
            'description' => 'Full RGB lighting rig, green screen and a 360-degree photo platform. Built for commercials and tech reviews.'],
        ['title' => 'The Loft Studio', 'category' => 'studio', 'city' => 'Karachi', 'address' => 'PECHS Block 6, Shahrah-e-Faisal', 'price' => 6000,
            'description' => 'Industrial loft with skylights, concrete floors and movable partition walls. Flexible space for teams and large setups.'],
        ['title' => 'Ivory Studio', 'category' => 'studio', 'city' => 'Lahore', 'address' => 'Model Town Link Road', 'price' => 4500,
            'description' => 'Warm ivory interiors with a classic fireplace set, wooden floors and soft morning light. Ideal for family portraits.'],
        ['title' => 'Studio Noor', 'category' => 'studio', 'city' => 'Islamabad', 'address' => 'I-8 Markaz, Suite 14', 'price' => 6800,
            'description' => 'Bright studio with a large arched window feature wall, rattan props and a coffee bar. A favourite for lifestyle shoots.'],

        // ----------------------------------------------------------- Outdoor
        ['title' => 'Shalimar Garden Point', 'category' => 'outdoor', 'city' => 'Lahore', 'address' => 'Shalimar Bagh, G.T. Road', 'price' => 3000,
            'description' => 'Mughal-era terraced gardens with fountains and marble walkways. Stunning backdrops for pre-wedding and heritage shoots.'],
        ['title' => 'Lake View Park Spot', 'category' => 'outdoor', 'city' => 'Islamabad', 'address' => 'Park Road, Zone IV', 'price' => 3500,
            'description' => 'Lakeside lawns and wooden piers with the Margalla Hills behind. Golden-hour reflections make it a photographers favourite.'],
        ['title' => 'Clifton Beach Shoot', 'category' => 'outdoor', 'city' => 'Karachi', 'address' => 'Clifton Beach, Sea View Road', 'price' => 2500,
            'description' => 'Open beach with camels, kites and sunset silhouettes. Best at low tide for wide fashion and cinematic shots.'],
        ['title' => 'Badshahi Mosque Backdrop', 'category' => 'outdoor', 'city' => 'Lahore', 'address' => 'Walled City, Fort Road', 'price' => 4000,
            'description' => 'Iconic Mughal architecture and red-sandstone facades. Dawn access recommended for empty courtyards and soft light.'],
        ['title' => 'Trail 3 Forest', 'category' => 'outdoor', 'city' => 'Islamabad', 'address' => 'Margalla Hills National Park', 'price' => 2000,
            'description' => 'Forested hiking trail with streams, rock walls and dense canopy. Suits adventure, fitness and moody portrait shoots.'],
        ['title' => 'Do Darya Seafront', 'category' => 'outdoor', 'city' => 'Karachi', 'address' => 'DHA Phase 8, Abdul Sattar Edhi Avenue', 'price' => 3000,
            'description' => 'Coastal promenade lined with wooden decks and fishing boats. Gorgeous dusk light over the Arabian Sea.'],
        ['title' => 'Race Course Park', 'category' => 'outdoor', 'city' => 'Lahore', 'address' => 'Jail Road, Race Course Park', 'price' => 2500,
            'description' => 'Manicured lawns, jacaranda-lined paths and a lake. A quiet, green pocket in the heart of the city.'],
        ['title' => 'Rawal Lake Promenade', 'category' => 'outdoor', 'city' => 'Islamabad', 'address' => 'Rawal Dam, Park Road', 'price' => 2200,
            'description' => 'Windswept lakeshore with reeds and fishing jetties. Great for romantic couple shoots and drift cinematography.'],
        ['title' => 'Quaids Mausoleum Grounds', 'category' => 'outdoor', 'city' => 'Karachi', 'address' => 'M.A. Jinnah Road, Mazar-e-Quaid', 'price' => 1500,
            'description' => 'Sweeping white-marble architecture with geometric gardens and fountains. Minimalist and monumental.'],
        ['title' => 'Jallo Forest Park', 'category' => 'outdoor', 'city' => 'Lahore', 'address' => 'Jallo More, Canal Bank Road', 'price' => 1800,
            'description' => 'Eucalyptus woods with a lake and railway track through the trees. Rustic, quiet and full of texture.'],
        ['title' => 'Daman-e-Koh Overlook', 'category' => 'outdoor', 'city' => 'Islamabad', 'address' => 'Margalla Road, Daman-e-Koh', 'price' => 2800,
            'description' => 'Panoramic city overlook with pine terraces and evening city lights. Ideal for cityscape and drone-assisted shoots.'],
        ['title' => 'Hawkes Bay Beach', 'category' => 'outdoor', 'city' => 'Karachi', 'address' => 'Hawkes Bay Road, Mauripur', 'price' => 2000,
            'description' => 'Quieter beach with huts, rocky outcrops and long empty stretches. Perfect for fashion editorials at sunrise.'],

        // ----------------------------------------------------------- Rooftop
        ['title' => 'Skyline Rooftop', 'category' => 'rooftop', 'city' => 'Lahore', 'address' => 'MM Alam Road, Gulberg', 'price' => 10000,
            'description' => '360-degree city views with a pool deck, string lights and lounge seating. The go-to for night portraits and music videos.'],
        ['title' => 'Rooftop One', 'category' => 'rooftop', 'city' => 'Islamabad', 'address' => 'Bahria Town, Phase 4', 'price' => 8000,
            'description' => 'Spacious rooftop with Margalla views, a wooden deck and pergola. Even light in the morning, city glow after dark.'],
        ['title' => 'Harbor Rooftop', 'category' => 'rooftop', 'city' => 'Karachi', 'address' => 'DHA Phase 5, Khayaban-e-Bukhari', 'price' => 9000,
            'description' => 'Sea-breeze rooftop with a plunge pool, cane furniture and harbour views. Airy and bright for lifestyle campaigns.'],
        ['title' => 'Midnight Terrace', 'category' => 'rooftop', 'city' => 'Lahore', 'address' => 'Gulberg II, Ali Tower', 'price' => 12000,
            'description' => 'Moody rooftop lounge with neon signage, velvet sofas and a glass floor section. Built for after-dark shoots.'],
        ['title' => 'Cloud Nine Deck', 'category' => 'rooftop', 'city' => 'Islamabad', 'address' => 'F-6 Super Market, Centaurus View', 'price' => 11000,
            'description' => 'High-rise deck overlooking the Centaurus skyline with a helipad-style platform and glass balustrade.'],
        ['title' => 'Port View Rooftop', 'category' => 'rooftop', 'city' => 'Karachi', 'address' => 'Clifton Block 2, Boat Basin', 'price' => 9500,
            'description' => 'Rooftop facing the port with industrial cranes silhouettes at sunset and a long exposed-brick wall.'],
        ['title' => 'The Upper Deck', 'category' => 'rooftop', 'city' => 'Lahore', 'address' => 'Johar Town, Khayaban-e-Firdousi', 'price' => 7000,
            'description' => 'Budget-friendly rooftop with astro-turf, festoon lights and a swing. Cheerful spot for birthdays and family shoots.'],
        ['title' => 'Margalla Rooftop', 'category' => 'rooftop', 'city' => 'Islamabad', 'address' => 'E-11 Markaz, Margalla View Apartments', 'price' => 7500,
            'description' => 'Mountain-facing rooftop with wooden pergolas and potted olive trees. Nature meets city in one frame.'],
        ['title' => 'Sunset Deck', 'category' => 'rooftop', 'city' => 'Karachi', 'address' => 'DHA Phase 2, Sunset Boulevard', 'price' => 13000,
            'description' => 'West-facing premium deck with an infinity-edge pool, daybeds and unobstructed sunset views over the city.'],
        ['title' => 'City Lights Terrace', 'category' => 'rooftop', 'city' => 'Lahore', 'address' => 'Liberty Market, M.M. Alam Extension', 'price' => 8500,
            'description' => 'Busy urban rooftop above Liberty with neon shop signs below and distant Minar-e-Pakistan views.'],
        ['title' => 'Capital Vista Rooftop', 'category' => 'rooftop', 'city' => 'Islamabad', 'address' => 'F-8 Markaz, Nizam Avenue', 'price' => 10000,
            'description' => 'Corner rooftop with a marble floor, mirror wall and fairy-light canopy. Elegant for bridal and sangeet shoots.'],
        ['title' => 'Bayview Rooftop', 'category' => 'rooftop', 'city' => 'Karachi', 'address' => 'Zamzama, DHA Phase 5', 'price' => 14000,
            'description' => 'Designer rooftop with a glass-bottomed pool, white cabanas and sea glimpses between towers. High-end production space.'],

        // ------------------------------------------------------------ Indoor
        ['title' => 'Heritage Haveli Hall', 'category' => 'indoor', 'city' => 'Lahore', 'address' => 'Androon Lahore, Bhati Gate', 'price' => 9000,
            'description' => 'Restored haveli courtyard hall with frescoed walls, wooden jharokhas and antique furniture. Pure old-Lahore character.'],
        ['title' => 'The Grand Hall', 'category' => 'indoor', 'city' => 'Lahore', 'address' => 'Gulberg III, Zafar Ali Road', 'price' => 12000,
            'description' => 'Chandelier-lit banquet hall with 20-foot ceilings, a spiral staircase and velvet seating. Suits bridal and event films.'],
        ['title' => 'Mughal Courtyard Hall', 'category' => 'indoor', 'city' => 'Lahore', 'address' => 'Walled City, Delhi Gate', 'price' => 8000,
            'description' => 'Indoor courtyard with carved arches, mosaic floors and soft skylight. A painterly setting for portraits.'],
        ['title' => 'Empire Hall', 'category' => 'indoor', 'city' => 'Islamabad', 'address' => 'F-10 Markaz, Sector F-10/2', 'price' => 10000,
            'description' => 'Modern pillar-less hall with parquet floors, stage lighting and retractable partitions. Flexible for large crews.'],
        ['title' => 'The Atrium', 'category' => 'indoor', 'city' => 'Islamabad', 'address' => 'Blue Area, Savour Tower', 'price' => 11000,
            'description' => 'Glass-ceiling atrium with indoor trees, marble staircases and moving shadows through the day.'],
        ['title' => 'Frontier House Hall', 'category' => 'indoor', 'city' => 'Rawalpindi', 'address' => 'Peshawar Road, Saddar', 'price' => 7000,
            'description' => 'Colonial-style hall with high windows, dark wood panelling and vintage fans. Warm, classic interiors.'],
        ['title' => 'Mehfil Banquet', 'category' => 'indoor', 'city' => 'Lahore', 'address' => 'Johar Town, Canal Bank Road', 'price' => 7500,
            'description' => 'Recently renovated banquet hall with pastel walls, crystal lighting and a grand entrance porch.'],
        ['title' => 'Casa Bella Interior', 'category' => 'indoor', 'city' => 'Lahore', 'address' => 'DHA Phase 4, Y Block', 'price' => 6000,
            'description' => 'Furnished villa interior with contemporary art, layered rugs and a reading nook. Lifestyle-home aesthetic.'],
        ['title' => 'The Oak Room', 'category' => 'indoor', 'city' => 'Islamabad', 'address' => 'F-7/3, Khyaban-e-Margalla', 'price' => 8500,
            'description' => 'Wood-panelled lounge with leather chesterfields, a fireplace and library shelves. Moody executive portraits.'],
        ['title' => 'Urban Loft Interior', 'category' => 'indoor', 'city' => 'Karachi', 'address' => 'Bahadurabad, Tariq Road', 'price' => 6500,
            'description' => 'Open-plan loft apartment with exposed beams, concrete textures and a music corner. Creators love the vibe.'],
        ['title' => 'The Ivory Hall', 'category' => 'indoor', 'city' => 'Karachi', 'address' => 'Clifton Block 5, Khayaban-e-Iqbal', 'price' => 9500,
            'description' => 'All-white event hall with floor-to-ceiling windows, drapes and a bridal suite attached.'],
        ['title' => 'Kashmir Corner Hall', 'category' => 'indoor', 'city' => 'Rawalpindi', 'address' => 'Saddar, Bank Road', 'price' => 5500,
            'description' => 'Compact community hall with mirrored pillars and flexible stage. Budget-friendly for small productions.'],

        // ------------------------------------------------------------ Garden
        ['title' => 'Rose Garden Estate', 'category' => 'garden', 'city' => 'Lahore', 'address' => 'Baghbanpura, Shalimar Link Road', 'price' => 5000,
            'description' => 'Blooming rose gardens with box hedges, a lily pond and a white gazebo. Romantic pre-wedding favourite.'],
        ['title' => 'The Lawn at DHA', 'category' => 'garden', 'city' => 'Lahore', 'address' => 'DHA Phase 3, Z Block', 'price' => 6000,
            'description' => 'Manicured private lawn with mature trees, garden swings and soft evening uplighting.'],
        ['title' => 'Jasmine Garden Venue', 'category' => 'garden', 'city' => 'Faisalabad', 'address' => 'Jaranwala Road, Canal Park', 'price' => 4000,
            'description' => 'Fragrant jasmine arbours, lawn carpets and a small fountain court. Quiet city escape for family shoots.'],
        ['title' => 'The Palm Garden', 'category' => 'garden', 'city' => 'Islamabad', 'address' => 'E-7, Margalla Road', 'price' => 7000,
            'description' => 'Landscaped palm garden with stone pathways, a koi pond and Margalla views. Serene and private.'],
        ['title' => 'Gulshan Lawn', 'category' => 'garden', 'city' => 'Karachi', 'address' => 'Gulshan-e-Iqbal Block 15', 'price' => 4500,
            'description' => 'Large lawn venue with canopies, flowerbeds and banyan trees. Handles big event crews comfortably.'],
        ['title' => 'The Garden House', 'category' => 'garden', 'city' => 'Karachi', 'address' => 'Defence Phase 8, Bukhari Commercial', 'price' => 5500,
            'description' => 'Modern villa garden with a pergola, decking and an outdoor kitchen. Lifestyle and brand-content ready.'],
        ['title' => 'Citrus Grove', 'category' => 'garden', 'city' => 'Lahore', 'address' => 'Raiwind Road, Model Town Extension', 'price' => 4800,
            'description' => 'Orchard garden with citrus trees, straw-bale seating and golden afternoon light between the rows.'],
        ['title' => 'Bluebell Gardens', 'category' => 'garden', 'city' => 'Islamabad', 'address' => 'Bahria Town Phase 7, Sector B', 'price' => 5500,
            'description' => 'Spring-flowering garden with tulip beds, a wooden bridge and fairy lights strung through the trees.'],
        ['title' => 'The Secret Garden', 'category' => 'garden', 'city' => 'Lahore', 'address' => 'Model Town Park View', 'price' => 4200,
            'description' => 'Hidden walled garden with ivy, an antique door and a mosaic bench. Intimate and full of character.'],
        ['title' => 'Magnolia Lawns', 'category' => 'garden', 'city' => 'Islamabad', 'address' => 'F-11 Markaz, Sector F-11/1', 'price' => 6000,
            'description' => 'Tiered lawns with magnolia trees, a water feature and paved photo plinths for group portraits.'],
        ['title' => 'Riverside Garden', 'category' => 'garden', 'city' => 'Rawalpindi', 'address' => 'Soan River Belt, Dhoke Ratta', 'price' => 3500,
            'description' => 'Natural riverside garden with wild grass, pebble beaches and sunset reflections. Unpolished and beautiful.'],
        ['title' => 'The Courtyard Garden', 'category' => 'garden', 'city' => 'Karachi', 'address' => 'Clifton Block 8, Sea View', 'price' => 5000,
            'description' => 'Cobbled courtyard garden with bougainvillea walls, a mosaic fountain and coastal breeze.'],
    ];

    public function run(): void
    {
        $owners = User::where('role', 'owner')->where('status', 'active')->get();

        if ($owners->isEmpty()) {
            $owners = Collection::make([User::create([
                'role' => 'owner', 'first_name' => 'Demo', 'last_name' => 'Owner',
                'email' => 'demo-owner@example.com', 'phone' => '03001111111',
                'password' => bcrypt('password'),
            ])]);
        }

        $categoryIds = Category::all()
            ->mapWithKeys(fn (Category $category) => [strtolower($category->name) => $category->id]);

        // One keyword-matched photo per listing, copied from the bundled
        // database/seeders/images set onto the public disk. Falls back to a
        // generated SVG when the bundled file is missing.
        $offsets = [];
        $imageByTitle = [];
        foreach (self::LISTINGS as $listing) {
            $category = strtolower($listing['category']);
            $offsets[$category] = ($offsets[$category] ?? 0) + 1;
            $imageByTitle[$listing['title']] = $this->imageFor($category, $offsets[$category] - 1);
        }

        $created = 0;
        foreach (self::LISTINGS as $index => $listing) {
            $exists = Location::where('title', $listing['title'])->exists();
            if ($exists) {
                continue;
            }

            Location::create([
                'user_id'        => $owners[$index % $owners->count()]->id,
                'title'          => $listing['title'],
                'description'    => $listing['description'],
                'address'        => $listing['address'],
                'city'           => $listing['city'],
                'category'       => strtolower($listing['category']),
                'category_id'    => $categoryIds[strtolower($listing['category'])] ?? null,
                'price_per_hour' => $listing['price'],
                'image'          => $imageByTitle[$listing['title']],
                'status'         => ListingStatus::Approved,
            ]);
            $created++;
        }

        // Point every seeded listing at its photo, re-pointing rows that an
        // earlier run gave an SVG placeholder.
        $backfilled = Location::whereIn('title', array_keys($imageByTitle))
            ->get()
            ->each(function (Location $location) use ($imageByTitle) {
                if ($location->image !== $imageByTitle[$location->title]) {
                    $location->update(['image' => $imageByTitle[$location->title]]);
                }
            })
            ->count();

        // The SVG placeholders are superseded by the photos - but only for
        // categories whose bundled photos actually exist (otherwise the SVG
        // fallback stays in use and must not be deleted).
        foreach (['studio', 'outdoor', 'rooftop', 'indoor', 'garden'] as $category) {
            if (is_file(database_path("seeders/images/{$category}/1.jpg"))) {
                Storage::disk('public')->delete("placeholders/{$category}.svg");
            }
        }

        $this->command?->info("Seeded {$created} listings (".(count(self::LISTINGS) - $created).' already existed). Photos assigned to '.$backfilled.' listings.');
    }

    /**
     * Copies the bundled keyword-matched photo for this category/offset onto
     * the public disk and returns its path. Falls back to a generated SVG when
     * the bundled image is missing (e.g. fresh checkout without the files).
     */
    private function imageFor(string $category, int $index): string
    {
        $source = database_path("seeders/images/{$category}/".($index + 1).'.jpg');
        $target = "placeholders/{$category}/".($index + 1).'.jpg';

        if (is_file($source)) {
            if (! Storage::disk('public')->exists($target)) {
                Storage::disk('public')->put($target, file_get_contents($source));
            }

            return $target;
        }

        return $this->placeholderFor($category);
    }

    /**
     * Dependency-free SVG fallback - no GD/Imagick required.
     */
    private function placeholderFor(string $category): string
    {
        $palette = [
            'studio'  => ['#4F46E5', '#7C3AED'],
            'outdoor' => ['#0EA5E9', '#10B981'],
            'rooftop' => ['#F59E0B', '#E11D48'],
            'indoor'  => ['#64748B', '#1E293B'],
            'garden'  => ['#22C55E', '#166534'],
        ];
        [$from, $to] = $palette[$category] ?? ['#4F46E5', '#312E81'];

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$from}"/>
      <stop offset="1" stop-color="{$to}"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="800" fill="url(#g)"/>
  <g fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="10" stroke-linecap="round" stroke-linejoin="round">
    <path d="M470 380 h80 l22 -32 h56 l22 32 h80 a42 42 0 0 1 42 42 v140 a42 42 0 0 1 -42 42 h-260 a42 42 0 0 1 -42 -42 v-140 a42 42 0 0 1 42 -42 z"/>
    <circle cx="600" cy="470" r="56"/>
  </g>
  <text x="600" y="660" text-anchor="middle" font-family="Georgia, serif" font-size="52" fill="#FFFFFF" opacity="0.95">{$category}</text>
  <text x="600" y="718" text-anchor="middle" font-family="Arial, sans-serif" font-size="24" fill="#FFFFFF" opacity="0.7">LensLocation</text>
</svg>
SVG;

        $path = 'placeholders/'.$category.'.svg';

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, $svg);
        }

        return $path;
    }
}
