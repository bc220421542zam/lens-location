<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users'             => User::count(),
            'listings'          => Location::count(),
            'bookings'          => Booking::count(),
            'rejected_listings' => Location::where('status', 'rejected')->count(),
        ];

        $days = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i));

        $chartData = [
            'labels'   => $days->map(fn($d) => $d->format('D'))->values(),
            'users'    => $days->map(fn($d) => User::whereDate('created_at', $d)->count())->values(),
            'listings' => $days->map(fn($d) => Location::whereDate('created_at', $d)->count())->values(),
            'bookings' => $days->map(fn($d) => Booking::whereDate('created_at', $d)->count())->values(),
            'rejected' => $days->map(fn($d) => Location::where('status', 'rejected')->whereDate('created_at', $d)->count())->values(),
            'stats'    => $stats,
        ];

        $recentListings = Location::with('owner')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentListings', 'chartData'));
    }
}