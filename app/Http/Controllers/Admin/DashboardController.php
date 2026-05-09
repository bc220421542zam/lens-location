<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users'    => User::count(),
            'listings' => Location::count(),
            'bookings' => Booking::count(),
        ];

        $recentListings = Location::with('owner')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentListings'));
    }
}
