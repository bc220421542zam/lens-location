<?php

namespace App\Http\Controllers;
use App\Models\Location;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            //'pending'  => $locations->where('status', 'rejected')->count(),
            'bookings' => 0, // update when bookings table is ready
        ];

        return view('owner.listings', compact('locations', 'stats'));
    }

    public function bookings()
    {
        $locationIds = Location::where('user_id', auth()->id())->pluck('id');

        $query = Booking::with(['location', 'photographer'])
                    ->whereIn('location_id', $locationIds)
                    ->latest();

        // Filter by status tab
        // if ($request->filled('status')) {
        //     $query->where('status', $request->status);
        // }

        $bookings = $query->get();

        $stats = [
            'total'     => $bookings->count(),
            'pending'   => $bookings->where('status', 'pending')->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
        ];

        return view('owner.bookings', compact('bookings', 'stats'));
    }

    public function acceptBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'confirmed';
        $booking->save();
        return back()->with('success', 'Booking accepted.');
    }

    public function rejectBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();
        return back()->with('success', 'Booking rejected.');
    }

     public function profile()
    {
        return view('owner.profile');
    }
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
        ]);

        $user->first_name    = $request->first_name;
        $user->last_name     = $request->last_name;
        $user->business_name = $request->business_name;
        $user->phone         = $request->phone;
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}