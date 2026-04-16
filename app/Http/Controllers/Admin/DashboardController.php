<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;


class DashboardController extends Controller
{
    //Dashboard window
    public function index(){
        return view('admin.dashboard');
    }
    //Users window
    public function users()
    {
        $users = User::paginate(4);
        return view('admin.users', compact('users'));
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
