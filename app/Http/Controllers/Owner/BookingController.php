<?php

namespace App\Http\Controllers\Owner;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $ownerLocationIds = Location::where('user_id', auth()->id())->pluck('id');

        $bookings = Booking::with(['location', 'photographer'])
            ->whereIn('location_id', $ownerLocationIds)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->get();

        $allBookings = Booking::whereIn('location_id', $ownerLocationIds)->get();

        $stats = [
            'total'     => $allBookings->count(),
            'pending'   => $allBookings->where('status', BookingStatus::Pending)->count(),
            'confirmed' => $allBookings->where('status', BookingStatus::Confirmed)->count(),
            'completed' => $allBookings->where('status', BookingStatus::Completed)->count(),
        ];

        return view('owner.bookings', compact('bookings', 'stats'));
    }

    public function accept(Booking $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        $booking->status = BookingStatus::Confirmed;
        $booking->save();

        return back()->with('success', 'Booking accepted.');
    }

    public function reject(Booking $booking): RedirectResponse
    {
        $this->authorizeOwnership($booking);

        $booking->status = BookingStatus::Cancelled;
        $booking->save();

        return back()->with('success', 'Booking rejected.');
    }

    private function authorizeOwnership(Booking $booking): void
    {
        abort_unless($booking->location->user_id === auth()->id(), 403);
    }
}
