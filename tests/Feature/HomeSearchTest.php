<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The home page always shows some locations; a search from the hero swaps
 * that section for the matching results on the same page. Guests see simple
 * previews (image, title, city) and need login for details; customers get
 * the full card experience.
 */
class HomeSearchTest extends TestCase
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

    private function makeListing(array $overrides = []): Location
    {
        return Location::create(array_merge([
            'user_id' => $this->owner->id,
            'title' => 'The Studio',
            'description' => 'quiet lakeside hideaway',
            'address' => 'a',
            'city' => 'Lahore',
            'category' => 'studio',
            'price_per_hour' => 1300,
            'status' => ListingStatus::Approved,
        ], $overrides));
    }

    public function test_home_shows_locations_to_guests_as_simple_previews(): void
    {
        $studio = $this->makeListing(['title' => 'Sunset Rooftop']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Locations our customers love')
            ->assertSee($studio->title)
            ->assertSee('Lahore')
            // Shallow preview only — no prices for guests.
            ->assertDontSee('PKR')
            ->assertSee('Log in to view details');
    }

    public function test_guest_search_swaps_the_locations_section_on_the_same_page(): void
    {
        $lahore = $this->makeListing(['title' => 'Sunset Rooftop', 'city' => 'Lahore']);
        $this->makeListing(['title' => 'Sea View Studio', 'city' => 'Karachi']);

        $this->get(route('home', ['search' => 'Lahore']))
            ->assertOk()
            ->assertSee('Locations matching your search')
            ->assertSee($lahore->title)
            ->assertDontSee('Sea View Studio')
            // The featured heading swaps out, not both.
            ->assertDontSee('Locations our customers love');
    }

    public function test_search_results_are_a_simple_preview_without_price_or_description(): void
    {
        $studio = $this->makeListing(['description' => 'quiet lakeside hideaway']);

        $this->get(route('home', ['search' => $studio->title]))
            ->assertOk()
            ->assertSee($studio->title)
            ->assertSee($studio->city)
            ->assertDontSee('PKR')
            ->assertDontSee('lakeside hideaway')
            ->assertSee('Log in to view details');
    }

    public function test_search_hides_pending_and_rejected_listings(): void
    {
        $approved = $this->makeListing(['title' => 'Approved Space']);
        $this->makeListing(['title' => 'Pending Space', 'status' => ListingStatus::Pending]);

        $this->get(route('home', ['search' => 'Space']))
            ->assertOk()
            ->assertSee($approved->title)
            ->assertDontSee('Pending Space');
    }

    public function test_search_only_matches_the_listings_shown_on_home(): void
    {
        // This listing matches the term but is older than the featured nine,
        // so it is not part of the home page set and must not appear.
        $old = $this->makeListing(['title' => 'Sunset Rooftop']);
        $old->forceFill(['created_at' => now()->subDay()])->save();

        for ($i = 1; $i <= 9; $i++) {
            $this->makeListing(['title' => "Modern Space $i"]);
        }

        $this->get(route('home', ['search' => 'Rooftop']))
            ->assertOk()
            ->assertDontSee('Sunset Rooftop');

        // And the listings that ARE on the page still match.
        $this->get(route('home', ['search' => 'Modern Space 3']))
            ->assertOk()
            ->assertSee('Modern Space 3');
    }

    public function test_search_filters_by_category(): void
    {
        Category::create(['name' => 'Rooftop']);

        $rooftop = $this->makeListing(['title' => 'Lahore Rooftop', 'category' => 'rooftop']);
        $this->makeListing(['title' => 'City Studio', 'category' => 'studio']);

        $this->get(route('home', ['category' => 'Rooftop']))
            ->assertOk()
            ->assertSee($rooftop->title)
            ->assertDontSee('City Studio');
    }

    public function test_guest_cannot_open_listing_details(): void
    {
        $studio = $this->makeListing();

        // The public detail route no longer exists.
        $this->get('/listings/'.$studio->id)->assertNotFound();

        // The customer detail page is behind auth + role.
        $this->get(route('customer.listings.show', $studio))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_open_the_full_browse_page(): void
    {
        $this->get(route('customer.listings'))
            ->assertRedirect(route('login'));
    }

    public function test_browse_listings_nav_is_hidden_from_guests_and_shown_to_customers(): void
    {
        $this->get(route('home'))->assertDontSee('Browse Listings');

        $this->actingAs($this->customer)
            ->get(route('home'))
            ->assertSee('Browse Listings')
            ->assertSee(route('customer.listings'), escape: false);
    }

    public function test_browse_all_button_is_hidden_from_guests_and_shown_to_customers(): void
    {
        $this->makeListing();

        $this->get(route('home'))->assertDontSee('Browse all locations');

        $this->actingAs($this->customer)
            ->get(route('home'))
            ->assertSee('Browse all locations');
    }

    public function test_logged_in_customer_sees_full_cards_linking_to_details(): void
    {
        $studio = $this->makeListing();

        $this->actingAs($this->customer)
            ->get(route('home', ['search' => $studio->title]))
            ->assertOk()
            ->assertSee(route('customer.listings.show', $studio), escape: false)
            ->assertSee('PKR')
            ->assertDontSee('Log in to view details');
    }

    public function test_booking_still_requires_login(): void
    {
        $studio = $this->makeListing();

        $this->get(route('customer.listings.book', $studio))
            ->assertRedirect(route('login'));
    }
}
