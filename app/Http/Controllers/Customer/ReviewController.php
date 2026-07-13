<?php

namespace App\Http\Controllers\Customer;

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
        abort_unless($booking->user_id === Auth::id(), 403);
        abort_unless($booking->status->value === 'completed', 403, 'You can only review completed bookings.');

        $userReview = Review::where('user_id', Auth::id())
            ->where('location_id', $booking->location_id)
            ->first();

        return view('customer.reviews.create', compact('booking', 'userReview'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        abort_unless($booking->status->value === 'completed', 403, 'You can only review completed bookings.');

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
}