<?php

namespace App\Http\Controllers\Owner;

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
            'total'    => Location::where('user_id', auth()->id())->count(),
            'active'   => Location::where('user_id', auth()->id())->where('status', 'approved')->count(),
            'pending'  => Location::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'bookings' => Booking::whereHas('location', fn($q) => $q->where('user_id', auth()->id()))->count(),
        ];

        $days = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i));

        $chartData = [
            'labels'   => $days->map(fn($d) => $d->format('D'))->values(),
            'listings' => $days->map(fn($d) => Location::where('user_id', auth()->id())->whereDate('created_at', $d)->count())->values(),
            'bookings' => $days->map(fn($d) => Booking::whereHas('location', fn($q) => $q->where('user_id', auth()->id()))->whereDate('created_at', $d)->count())->values(),
            'approved' => $days->map(fn($d) => Location::where('user_id', auth()->id())->where('status', 'approved')->whereDate('created_at', $d)->count())->values(),
            'pending'  => $days->map(fn($d) => Location::where('user_id', auth()->id())->where('status', 'pending')->whereDate('created_at', $d)->count())->values(),    
        ];

        $recentListings = Location::where('user_id', auth()->id())->latest()->take(10)->get();

        return view('owner.dashboard', compact('stats', 'recentListings', 'chartData'));
    }
}