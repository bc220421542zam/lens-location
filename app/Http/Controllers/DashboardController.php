<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Location;

class DashboardController extends Controller
{
    // Dashboard
    public function index()
    {
        $stats = [
            'users'    => User::count(),
            'listings' => Location::count(),
            'bookings' => 0,
        ];

        $recentListings = Location::with('owner')
                            ->latest()
                            ->take(10)
                            ->get();

        return view('admin.dashboard', compact('stats', 'recentListings'));
    }

    // Users page
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%')
                  ->orWhere('email',      'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role'))   { $query->where('role',   $request->role); }
        if ($request->filled('status')) { $query->where('status', $request->status); }

        $users = $query->paginate(4)->withQueryString();
        return view('admin.users', compact('users'));
    }

    // User detail (JSON for popup)
    public function userDetail($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Delete user
    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    // Toggle user status
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();
        return back()->with('success', 'User status updated.');
    }

    // Listings page
    public function listings()
    {
        $listings = Location::with('owner')->paginate(10);
        return view('admin.listings', compact('listings'));
    }

    // Toggle listing status
    public function toggleListing($id)
    {
        $listing = Location::findOrFail($id);
        $listing->status = $listing->status === 'approved' ? 'pending' : 'approved';
        $listing->save();
        return back()->with('success', 'Listing status updated.');
    }

    // Delete listing
    public function deleteListing($id)
    {
        Location::findOrFail($id)->delete();
        return back()->with('success', 'Listing deleted successfully.');
    }

    // Show single listing
    public function showListing($id)
    {
        $listing = Location::with('owner')->findOrFail($id);
        return view('admin.listings.show', compact('listing'));
    }

    // Admin profile
    public function profile()
    {
        return view('admin.profile');
    }

    // Update photo
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

    // Update profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->phone      = $request->phone;
        $user->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully.')
            ->with('tab', 'personal');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        // Manual validator so we can attach ->with('tab','password') on failure
        $validator = Validator::make($request->all(), [
            'current_password'          => 'required',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('tab', 'password');
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('tab', 'password');
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()
                ->withErrors(['new_password' => 'New password must be different from current.'])
                ->with('tab', 'password');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Password updated successfully.')
            ->with('tab', 'password');
    }
}