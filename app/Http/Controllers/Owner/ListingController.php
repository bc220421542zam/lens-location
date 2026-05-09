<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(): View
    {
        $locations = Location::where('user_id', auth()->id())
            ->latest()
            ->get();

        $stats = [
            'total'    => $locations->count(),
            'active'   => $locations->where('status', ListingStatus::Approved)->count(),
            'pending'  => $locations->where('status', ListingStatus::Pending)->count(),
            'bookings' => 0,
        ];

        return view('owner.listings', compact('locations', 'stats'));
    }
}
