<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::where('customer_id', auth()->id())->get();

        $stats = [
            'total'     => $bookings->count(),
            'upcoming'  => $bookings
                ->where('status', BookingStatus::Confirmed)
                ->where('booking_date', '>=', now())
                ->count(),
            'completed' => $bookings->where('status', BookingStatus::Completed)->count(),
            'spent'     => $bookings
                ->where('status', BookingStatus::Completed)
                ->sum('total_price'),
        ];

        $upcoming = Booking::with('location.owner')
            ->where('customer_id', auth()->id())
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->where('booking_date', '>=', now())
            ->orderBy('booking_date')
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('stats', 'upcoming'));
    }
}
