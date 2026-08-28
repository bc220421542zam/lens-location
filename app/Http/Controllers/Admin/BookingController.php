<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\BookingCompleter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Every booking on the platform, modelled on the customer's My Bookings
     * page: status stats, a status filter and a search over customer and
     * listing.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'search' => 'nullable|string|max:255',
        ]);

        // Backstop so paid bookings whose shoot has ended show as completed
        // here too, not only on the customer/owner pages.
        BookingCompleter::forAll();

        $bookings = Booking::with(['location.owner', 'customer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->whereHas('customer', fn ($q) => $q
                    ->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term))
                    ->orWhereHas('location', fn ($q) => $q->where('title', 'like', $term));
            }))
            ->latest('booking_date')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $totals = Booking::selectRaw(
            'COUNT(*) as total_count,
             COUNT(CASE WHEN status = ? THEN 1 END) as pending_count,
             COUNT(CASE WHEN status = ? THEN 1 END) as confirmed_count,
             COUNT(CASE WHEN status = ? THEN 1 END) as completed_count,
             COUNT(CASE WHEN status = ? THEN 1 END) as cancelled_count',
            [
                BookingStatus::Pending->value,
                BookingStatus::Confirmed->value,
                BookingStatus::Completed->value,
                BookingStatus::Cancelled->value,
            ]
        )->first();

        $stats = [
            'total'     => (int) $totals->total_count,
            'pending'   => (int) $totals->pending_count,
            'confirmed' => (int) $totals->confirmed_count,
            'completed' => (int) $totals->completed_count,
            'cancelled' => (int) $totals->cancelled_count,
        ];

        return view('admin.bookings', compact('bookings', 'stats'));
    }

    /**
     * One booking from the admin side: both parties, the listing, and the
     * payment rows, for every status.
     */
    public function show(Booking $booking): View
    {
        BookingCompleter::forAll();
        $booking->refresh();

        $booking->load(['location.owner', 'customer', 'transactions']);

        return view('admin.bookings.show', compact('booking'));
    }
}
