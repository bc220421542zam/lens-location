<?php

namespace App\Http\Controllers;
use App\Models\Location;
use App\Models\Booking;
use App\Models\Payment; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $payment = Payment::where('user_id', auth()->id())->first();
        return view('owner.profile', compact('payment'));
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
    public function updatePhoto(Request $request)
        {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user = auth()->user();

            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profiles', 'public');
                $user->profile_picture = $path;
                $user->save();
            }

            return back()->with('success', 'Profile photo updated.');
        }
        public function updatePayment(Request $request)
        {
            $request->validate([
                'account_holder' => 'nullable|string|max:255',
                'bank_name'      => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:255',
                'wallet_type'    => 'nullable|string|max:100',
                'wallet_number'  => 'nullable|string|max:20',
            ]);

            Payment::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'account_holder' => $request->account_holder,
                    'bank_name'      => $request->bank_name,
                    'account_number' => $request->account_number,
                    'wallet_type'    => $request->wallet_type,
                    'wallet_number'  => $request->wallet_number,
                ]
            );

            return redirect()->route('owner.profile')->with('success', 'Payment info saved successfully.');
        }
        
        public function updatePassword(Request $request)
        {
            $request->validate([
                'current_password'          => 'required',
                'new_password'              => 'required|min:8|confirmed',
                'new_password_confirmation' => 'required',
            ]);

            $user = auth()->user();

            // Check current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Current password is incorrect.'
                ]);
            }

            // Check new password is not same as current
            if (Hash::check($request->new_password, $user->password)) {
                return back()->withErrors([
                    'new_password' => 'New password must be different from current password.'
                ]);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->route('owner.profile')
                ->with('success', 'Password updated successfully.');
        }
}