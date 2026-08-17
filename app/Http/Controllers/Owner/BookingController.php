<?php

namespace App\Http\Controllers\Owner;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
        ]);

        // A subquery keeps the owner's listing ids in the database instead of
        // round-tripping them into PHP and back out as a giant IN (...) list.
        $ownerLocationIds = Location::select('id')->where('user_id', auth()->id());

        $bookings = Booking::with(['location', 'customer'])
            ->whereIn('location_id', $ownerLocationIds)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->get();

        // Counted in the database rather than by hydrating every booking again.
        $totals = Booking::whereIn('location_id', $ownerLocationIds)
            ->selectRaw(
                'COUNT(*) as total_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as pending_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as confirmed_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as completed_count',
                [
                    BookingStatus::Pending->value,
                    BookingStatus::Confirmed->value,
                    BookingStatus::Completed->value,
                ]
            )
            ->first();

        $stats = [
            'total'     => (int) $totals->total_count,
            'pending'   => (int) $totals->pending_count,
            'confirmed' => (int) $totals->confirmed_count,
            'completed' => (int) $totals->completed_count,
        ];

        return view('owner.bookings', compact('bookings', 'stats'));
    }

    public function accept(Booking $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        $booking->status = BookingStatus::Confirmed;
        $booking->save();

        // Notify the customer their booking was confirmed.
        if ($booking->customer) {
            $booking->customer->notify(new BookingStatusNotification(
                $booking->location->title,
                'confirmed'
            ));
        }

        return back()->with('success', 'Booking accepted.');
    }

    public function reject(Booking $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        $booking->status = BookingStatus::Cancelled;
        $booking->save();

        // Notify the customer their booking was declined.
        if ($booking->customer) {
            $booking->customer->notify(new BookingStatusNotification(
                $booking->location->title,
                'cancelled'
            ));
        }

        return back()->with('success', 'Booking rejected.');
    }

    private function authorizeOwnership(Booking $booking): void
    {
        abort_unless($booking->location->user_id === auth()->id(), 403);
    }
}
