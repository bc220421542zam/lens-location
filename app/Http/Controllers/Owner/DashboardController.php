<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Transaction;
use App\Support\TimeSeriesCounts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CHART_DAYS = 7;

    private const RECENT_LISTINGS = 10;

    public function index(): View
    {
        $ownerId = auth()->id();

        // Total / approved / pending in one pass instead of three COUNTs.
        $listingTotals = Location::where('user_id', $ownerId)
            ->selectRaw(
                'COUNT(*) as total_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as approved_count,
                 COUNT(CASE WHEN status = ? THEN 1 END) as pending_count',
                [ListingStatus::Approved->value, ListingStatus::Pending->value]
            )
            ->first();

        $stats = [
            'total'          => (int) $listingTotals->total_count,
            'active'         => (int) $listingTotals->approved_count,
            'pending'        => (int) $listingTotals->pending_count,
            'bookings'       => $this->ownerBookings($ownerId)->count(),
            // Destination charges transfer automatically, so anything still
            // 'unpaid' is in flight rather than awaiting a manual payout.
            'in_transit' => Transaction::where('owner_id', $ownerId)
                ->where('status', 'paid')
                ->where('payout_status', 'unpaid')
                ->sum('owner_earning'),
        ];

        // Two grouped queries replace 28 per-day COUNTs.
        $listings = TimeSeriesCounts::daily(
            Location::where('user_id', $ownerId),
            self::CHART_DAYS,
            [
                'approved' => ['status', ListingStatus::Approved->value],
                'pending'  => ['status', ListingStatus::Pending->value],
            ]
        );

        $bookings = TimeSeriesCounts::daily($this->ownerBookings($ownerId), self::CHART_DAYS);

        $chartData = [
            'labels'   => $listings['labels'],
            'listings' => $listings['series']['total'],
            'bookings' => $bookings['series']['total'],
            'approved' => $listings['series']['approved'],
            'pending'  => $listings['series']['pending'],
        ];

        $recentListings = Location::where('user_id', $ownerId)
            ->latest()
            ->take(self::RECENT_LISTINGS)
            ->get();

        return view('owner.dashboard', compact('stats', 'recentListings', 'chartData'));
    }

    /** Bookings against any listing this owner owns. */
    private function ownerBookings(int $ownerId): Builder
    {
        return Booking::whereHas('location', fn ($q) => $q->where('user_id', $ownerId));
    }
}
