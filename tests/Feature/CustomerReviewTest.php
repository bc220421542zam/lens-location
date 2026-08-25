<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReviewTest extends TestCase
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

    private function booking(BookingStatus $status, ?User $customer = null): Booking
    {
        return Booking::create([
            'location_id'  => $this->location->id,
            'customer_id'  => ($customer ?? $this->customer)->id,
            'booking_date' => now()->subDay(),
            'hours'        => 4,
            'total_price'  => 5200,
            'status'       => $status,
        ]);
    }

    public function test_customer_can_open_the_review_form_for_a_completed_booking(): void
    {
        $booking = $this->booking(BookingStatus::Completed);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings.review', $booking))
            ->assertOk();
    }

    public function test_customer_can_submit_a_review(): void
    {
        $booking = $this->booking(BookingStatus::Completed);

        $this->actingAs($this->customer)
            ->post(route('customer.bookings.review.store', $booking), [
                'rating'  => 5,
                'comment' => 'Great light, easy owner.',
            ])
            ->assertRedirect(route('customer.bookings'))
            ->assertSessionHas('success');

        $review = Review::sole();

        $this->assertSame($this->customer->id, $review->user_id);
        $this->assertSame($this->location->id, $review->location_id);
        $this->assertSame($booking->id, $review->booking_id);
        $this->assertSame(5, $review->rating);
    }

    public function test_resubmitting_updates_the_existing_review(): void
    {
        $booking = $this->booking(BookingStatus::Completed);

        $this->actingAs($this->customer)->post(route('customer.bookings.review.store', $booking), [
            'rating' => 3, 'comment' => 'Fine.',
        ]);

        $this->actingAs($this->customer)->post(route('customer.bookings.review.store', $booking), [
            'rating' => 5, 'comment' => 'Better than I first thought.',
        ]);

        $this->assertSame(1, Review::count());
        $this->assertSame(5, Review::sole()->rating);
    }

    public function test_incomplete_booking_cannot_be_reviewed(): void
    {
        foreach ([BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::Cancelled] as $status) {
            $booking = $this->booking($status);

            $this->actingAs($this->customer)
                ->get(route('customer.bookings.review', $booking))
                ->assertForbidden();
        }

        $this->assertSame(0, Review::count());
    }

    public function test_another_customers_booking_cannot_be_reviewed(): void
    {
        $intruder = User::create([
            'role' => 'customer', 'first_name' => 'X', 'last_name' => 'Y',
            'email' => 'other'.uniqid().'@example.com', 'phone' => '03001112222',
            'password' => bcrypt('password'),
        ]);

        $booking = $this->booking(BookingStatus::Completed);

        $this->actingAs($intruder)
            ->get(route('customer.bookings.review', $booking))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->post(route('customer.bookings.review.store', $booking), ['rating' => 1])
            ->assertForbidden();

        $this->assertSame(0, Review::count());
    }

    public function test_rating_is_validated(): void
    {
        $booking = $this->booking(BookingStatus::Completed);

        $this->actingAs($this->customer)
            ->from(route('customer.bookings.review', $booking))
            ->post(route('customer.bookings.review.store', $booking), ['rating' => 9])
            ->assertSessionHasErrors('rating');

        $this->assertSame(0, Review::count());
    }
}
