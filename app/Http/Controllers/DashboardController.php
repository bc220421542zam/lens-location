<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;


class DashboardController extends Controller
{
    //Dashboard window
    public function index()
    {
        return view('admin.dashboard');
    }
    //Dashboard pop-up detail page
    public function userDetail($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user); // returns user data as JSON for the popup
    }
    // Users window — UPDATED with search and filters
    public function users(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%')
                  ->orWhere('email',      'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(4)->withQueryString();
        return view('admin.users', compact('users'));
    }

    // DELETE user
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    // TOGGLE user status active/blocked
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();
        return back()->with('success', 'User status updated.');
    }

    //listings Window
     public function listings()
    {
        $listings = Location::with('owner')->paginate(10);
        return view('admin.listings', compact('listings'));
    }

    //user profile
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }
    //Update Profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->phone = $request->phone;
         
        if ($request->hasFile('profile_picture')) 
        {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $user->profile_picture = $path;
        }
        $user->save();
        return back()->with('success' , 'Profile update successfully.');
    }
}
