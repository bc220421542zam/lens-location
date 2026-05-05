<?php

namespace App\Http\Controllers;
use App\Models\Location;
use Illuminate\Http\Request;

class OwnerController extends Controller
{

    public function listings()
    {
        $locations = Location::where('user_id', auth()->id())
                        ->latest()
                        ->get();

        $stats = [
            'total'    => $locations->count(),
            'active'   => $locations->where('status', 'approved')->count(),
            'pending'  => $locations->where('status', 'pending')->count(),
            'bookings' => 0, // update when bookings table is ready
        ];

        return view('owner.listings', compact('locations', 'stats'));
    }

    public function bookings()
    {
        return view('owner.bookings');
    }
     public function profile()
    {
        return view('owner.profile');
    }
}