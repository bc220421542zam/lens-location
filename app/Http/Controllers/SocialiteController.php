<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Events\NewUser;
use App\Models\User;
use App\Notifications\AdminNewUserNotification;
use App\Support\RoleRedirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google sign-in doubles as sign-up: an email without an account gets a
     * customer account on first sign-in (mirroring the manual register flow,
     * which also notifies the admins). An existing user registered manually
     * just gets their google_id linked.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
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

        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if ($user === null) {
            [$firstName, $lastName] = array_pad(explode(' ', trim($googleUser->getName()), 2), 2, '');

            $user = User::create([
                'role'       => Role::Customer,
                'first_name' => $firstName !== '' ? $firstName : 'Google',
                'last_name'  => $lastName !== '' ? $lastName : 'User',
                'email'      => $googleUser->getEmail(),
                'phone'      => '',
                'password'   => Str::random(32), // hashed by the `hashed` cast
                'google_id'  => $googleUser->getId(),
            ]);

            $this->notifyAdminsOfNewUser($user);
        } elseif ($user->google_id === null) {
            // Link google_id for a user who registered manually before.
            $user->update(['google_id' => $googleUser->getId()]);
        }

        if ($user->isBlocked()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been blocked. Please contact admin.']);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return RoleRedirector::to($user);
    }

    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Facebook sign-in doubles as sign-up: an email without an account gets a
     * customer account on first sign-in (mirroring the manual register flow,
     * which also notifies the admins). An existing user registered manually
     * just gets their facebook_id linked.
     */
    public function handleFacebookCallback(Request $request): RedirectResponse
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please try signing in again.']);
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Facebook authentication failed. Please try again.']);
        }

        if (! $facebookUser->getEmail()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Your Facebook account does not share an email address. Please register with your email instead.']);
        }

        $user = User::where('facebook_id', $facebookUser->getId())
                    ->orWhere('email', $facebookUser->getEmail())
                    ->first();

        if ($user === null) {
            [$firstName, $lastName] = array_pad(explode(' ', trim($facebookUser->getName()), 2), 2, '');

            $user = User::create([
                'role'        => Role::Customer,
                'first_name'  => $firstName !== '' ? $firstName : 'Facebook',
                'last_name'   => $lastName !== '' ? $lastName : 'User',
                'email'       => $facebookUser->getEmail(),
                'phone'       => '',
                'password'    => Str::random(32), // hashed by the `hashed` cast
                'facebook_id' => $facebookUser->getId(),
            ]);

            $this->notifyAdminsOfNewUser($user);
        } elseif ($user->facebook_id === null) {
            // Link facebook_id for a user who registered manually before.
            $user->update(['facebook_id' => $facebookUser->getId()]);
        }

        if ($user->isBlocked()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been blocked. Please contact admin.']);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return RoleRedirector::to($user);
    }

    /**
     * Notify all admins about a newly registered user and broadcast the event.
     */
    private function notifyAdminsOfNewUser(User $user): void
    {
        User::where('role', 'admin')->each(
            fn (User $admin) => $admin->notify(new AdminNewUserNotification($user->name, $user->id))
        );

        try {
            broadcast(new NewUser($user->name, $user->id));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed: ' . $e->getMessage());
        }
    }
}
