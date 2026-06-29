<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with('location.owner')
            ->where('customer_id', auth()->id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('booking_date')
            ->get();

        $allBookings = Booking::where('customer_id', auth()->id())->get();

        $stats = [
            'total'     => $allBookings->count(),
            'upcoming'  => $allBookings
                ->where('status', BookingStatus::Confirmed)
                ->where('booking_date', '>=', now())
                ->count(),
            'completed' => $allBookings->where('status', BookingStatus::Completed)->count(),
            'cancelled' => $allBookings->where('status', BookingStatus::Cancelled)->count(),
            // 'spent'     => $allBookings
            //     ->where('status', BookingStatus::Completed)
            //     ->sum('total_price'),
        ];

        return view('customer.bookings', compact('bookings', 'stats'));
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

        Booking::create([
            'location_id'     => $location->id,
            'customer_id' => auth()->id(),
            'booking_date'    => $request->date('booking_date'),
            'hours'           => $hours,
            'total_price'     => (float) $location->price_per_hour * $hours,
            'shoot_type'      => $request->input('shoot_type'),
            'status'          => BookingStatus::Pending,
        ]);

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
