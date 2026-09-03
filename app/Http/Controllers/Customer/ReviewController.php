<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Booking $booking): View
    {
        $this->authorizeReview($booking);

        $userReview = Review::where('user_id', Auth::id())
            ->where('location_id', $booking->location_id)
            ->first();

        return view('customer.reviews.create', compact('booking', 'userReview'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeReview($booking);

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'location_id' => $booking->location_id],
            [
                'booking_id' => $booking->id,
                'rating'     => $request->integer('rating'),
                'comment'    => $request->string('comment'),
            ]
        );

        return redirect()
            ->route('customer.bookings')
            ->with('success', 'Thanks for your review!');
    }

    /**
     * Bookings are owned via `customer_id` - there is no `user_id` column on the
     * table, so comparing against one always yielded null and 403'd every
     * request, including the rightful customer's.
     *
     * Matches authorizeOwnership() in the sibling Customer controllers.
     *
     * Reviews need a paid transaction and a `completed` or `visited` booking:
     * payment completes the booking, and the settle pass moves it to `visited`
     * once the booking date has passed. Both are reviewable. This mirrors the
     * `canReview` flag in the booking views.
     */
    private function authorizeReview(Booking $booking): void
    {
        abort_unless($booking->customer_id === Auth::id(), 403);
        abort_unless(
            in_array($booking->status, [BookingStatus::Completed, BookingStatus::Visited], true)
                && $booking->transactions()->where('status', PaymentStatus::Paid)->exists(),
            403,
            'You can only review a paid, completed booking.',
        );
    }
}
