<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please try signing in again.']);
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }

        // Find by google_id or email
        $user = User::where('google_id', $googleUser->id)
                    ->orWhere('email', $googleUser->email)
                    ->first();

        // No account found — block access
        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No account found with this Google email. Please register first.']);
        }

        // Link google_id if user registered manually before
        if (!$user->google_id) {
            $user->update(['google_id' => $googleUser->id]);
        }

        Auth::login($user);
        return RoleRedirector::to($user);
    }
}