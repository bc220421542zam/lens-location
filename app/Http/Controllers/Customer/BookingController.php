<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Review;
use App\Notifications\BookingRequestNotification;
use App\Support\BookingCompleter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        // Visiting clears the My Bookings dot: the owner's status updates are
        // now seen.
        $request->user()->unreadNotifications()
            ->where('data->type', 'booking_status')
            ->update(['read_at' => now()]);

        $request->validate([
            'status' => 'nullable|in:pending,confirmed,completed,visited,cancelled,expired',
        ]);

        // Backstop for bookings the scheduler hasn't settled yet: expire the
        // unpaid past-due ones and complete the paid ones the webhook missed
        // (e.g. the Stripe CLI wasn't running locally). Normally a no-op.
        BookingCompleter::expireUnpaidForCustomer(auth()->id());
        BookingCompleter::forCustomer(auth()->id());

        $bookings = Booking::with('location.owner')
            // Drives the Paid badge and hides the Pay button, without an extra
            // query per booking.
            ->withExists(['transactions as paid_transaction_exists' => fn ($q) => $q->where('status', PaymentStatus::Paid)])
            ->where('customer_id', auth()->id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('booking_date')
            ->get();

        // Get the location_ids this customer has already reviewed, so we can show "Edit Review" vs "Leave a Review"
        $reviewedLocationIds = Review::where('user_id', auth()->id())
            ->whereIn('location_id', $bookings->pluck('location_id'))
            ->pluck('location_id');

        $bookings->each(function ($booking) use ($reviewedLocationIds) {
            $booking->has_review = $reviewedLocationIds->contains($booking->location_id);
        });

        // Counted in the database rather than by hydrating every booking again.
        // Upcoming counts both unpaid and paid future bookings (payment moves
        // a booking straight to `completed`); completed counts every paid
        // booking, whether the date has arrived (`visited`) or not.
        $totals = Booking::where('customer_id', auth()->id())
            ->selectRaw(
                'COUNT(*) as total_count,
                 COUNT(CASE WHEN status IN (?, ?) AND booking_date >= ? THEN 1 END) as upcoming_count,
                 COUNT(CASE WHEN status IN (?, ?) THEN 1 END) as completed_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as cancelled_count',
                [
                    BookingStatus::Confirmed->value, BookingStatus::Completed->value, now()->toDateTimeString(),
                    BookingStatus::Completed->value, BookingStatus::Visited->value,
                    BookingStatus::Cancelled->value,
                ]
            )
            ->first();

        $stats = [
            'total'     => (int) $totals->total_count,
            'upcoming'  => (int) $totals->upcoming_count,
            'completed' => (int) $totals->completed_count,
            'cancelled' => (int) $totals->cancelled_count,
        ];

        return view('customer.bookings', compact('bookings', 'stats'));
    }

    /**
     * Booking details. Reachable for every status - a cancelled or expired
     * booking still needs to be inspectable.
     */
    public function show(Booking $booking): View
    {
        $this->authorizeOwnership($booking);

        // Same backstop the index runs, so opening this page directly still
        // sees an up-to-date status rather than a stale `confirmed`.
        BookingCompleter::expireUnpaidForCustomer(auth()->id());
        BookingCompleter::forCustomer(auth()->id());
        $booking->refresh();

        $booking->load(['location.owner', 'transactions']);

        $booking->has_review = Review::where('user_id', auth()->id())
            ->where('location_id', $booking->location_id)
            ->exists();

        return view('customer.bookings.show', compact('booking'));
    }

    public function create(Location $location): View
    {
        abort_unless($location->status === ListingStatus::Approved, 404);

        return view('customer.locations.book', compact('location'));
    }

    public function store(StoreBookingRequest $request, Location $location): RedirectResponse
    {
        abort_unless($location->status === ListingStatus::Approved, 404);

        $hours = $request->integer('hours');

        $booking = Booking::create([
            'location_id' => $location->id,
            'customer_id' => auth()->id(),
            'booking_date' => $request->date('booking_date'),
            'hours' => $hours,
            'total_price' => (float) $location->price_per_hour * $hours,
            'shoot_type' => $request->input('shoot_type'),
            'status' => BookingStatus::Pending,
        ]);

        // Notify the owner of the new request unless they turned the
        // new-booking preference off in their profile.
        if ($location->owner && ($location->owner->notif_new_booking ?? true)) {
            $location->owner->notify(new BookingRequestNotification($booking));
        }

        return redirect()
            ->route('customer.bookings')
            ->with('success', 'Booking request sent. Waiting for owner confirmation.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);
        abort_unless($booking->status === BookingStatus::Pending, 422, 'Only pending bookings can be cancelled.');

        $booking->status = BookingStatus::Cancelled;
        $booking->save();

        return back()->with('success', 'Booking cancelled.');
    }

    private function authorizeOwnership(Booking $booking): void
    {
        abort_unless($booking->customer_id === auth()->id(), 403);
    }
}