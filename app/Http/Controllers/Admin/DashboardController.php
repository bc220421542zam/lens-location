<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use App\Support\TimeSeriesCounts;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CHART_DAYS = 7;

    private const RECENT_LISTINGS = 10;

    public function index(): View
    {
        // Total and rejected come from one pass over `locations` instead of two.
        $listingTotals = Location::selectRaw(
            'COUNT(*) as total_count, COUNT(CASE WHEN status = ? THEN 1 END) as rejected_count',
            [ListingStatus::Rejected->value]
        )->first();

        $stats = [
            'users'             => User::count(),
            'listings'          => (int) $listingTotals->total_count,
            'bookings'          => Booking::count(),
            'rejected_listings' => (int) $listingTotals->rejected_count,
        ];

        // One grouped query per table rather than one COUNT per day per metric.
        $users    = TimeSeriesCounts::daily(User::query(), self::CHART_DAYS);
        $bookings = TimeSeriesCounts::daily(Booking::query(), self::CHART_DAYS);
        $listings = TimeSeriesCounts::daily(Location::query(), self::CHART_DAYS, [
            'rejected' => ['status', ListingStatus::Rejected->value],
        ]);

        $chartData = [
            'labels'   => $users['labels'],
            'users'    => $users['series']['total'],
            'listings' => $listings['series']['total'],
            'bookings' => $bookings['series']['total'],
            'rejected' => $listings['series']['rejected'],
            'stats'    => $stats,
        ];

        $recentListings = Location::with('owner')
            ->latest()
            ->take(self::RECENT_LISTINGS)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentListings', 'chartData'));
    }
}
