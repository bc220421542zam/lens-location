<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Register Method
    public function register(Request $request)
{
    $validated = $request->validate([
        'role' => 'required|in:admin,owner,photographer',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required',
        'password' => 'required|min:6|confirmed',
    ]);

    //Create User
    $user = User::create([
        'role' => $validated['role'],
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'password' => Hash::make($validated['password']),
        
    ]);

    //  login first
    Auth::login($user);
    $request->session()->regenerate();

    // redirect last
    if ($user->role === 'admin') 
        {
            return redirect()->route('admin.dashboard'); // Admin dashboard
        } elseif ($user->role === 'owner') 
        {
            return redirect()->route('owner.dashboard'); // Owner dashboard
        } else {
            return redirect()->route('photographer.dashboard'); // Photographer dashboard
        }
}

    //  login method
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

    //  Role-based redirect
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'owner') {
        return redirect()->route('owner.dashboard');
    } else {
        return redirect()->route('photographer.dashboard');
    }
}
     return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
    }

    //  logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}