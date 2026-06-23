<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\View\View;
use Carbon\Carbon;

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
        'pending'   => $bookings->where('status', BookingStatus::Pending)->count(),
        'cancelled' => $bookings->where('status', BookingStatus::Cancelled)->count(),
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

    $months = collect(range(5, 0))->map(function ($i) {
        return Carbon::now()->subMonths($i);
    });

    $labels = [];
    $totalArr = [];
    $upcomingArr = [];
    $completedArr = [];
    $cancelledArr = [];

    foreach ($months as $month) {
        $labels[] = $month->format('M');

        $monthBookings = Booking::where('customer_id', auth()->id())
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->get();

        $totalArr[]     = $monthBookings->count();
        $upcomingArr[]  = $monthBookings->where('status', BookingStatus::Confirmed)->count();
        $completedArr[] = $monthBookings->where('status', BookingStatus::Completed)->count();
        $cancelledArr[] = $monthBookings->where('status', BookingStatus::Cancelled)->count();
    }

    $chartData = [
        'labels'    => $labels,
        'total'     => $totalArr,
        'upcoming'  => $upcomingArr,
        'completed' => $completedArr,
        'cancelled' => $cancelledArr,
        'stats' => [
            'total'     => $stats['total'],
            'upcoming'  => $stats['upcoming'],
            'completed' => $stats['completed'],
            'cancelled' => $stats['cancelled'],
        ],
    ];

    return view('customer.dashboard', compact('stats', 'upcoming', 'chartData'));
}
}