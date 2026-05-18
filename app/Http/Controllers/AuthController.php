<?php

namespace App\Http\Controllers;

use App\Events\NewUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\AdminNewUserNotification;
use App\Support\RoleRedirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        // ↓ Notify all admins about new user
        User::where('role', 'admin')->each(fn($admin) =>
            $admin->notify(new AdminNewUserNotification($user->name, $user->id))
        );
        broadcast(new NewUser($user->name, $user->id));

        Auth::login($user);
        $request->session()->regenerate();

        return RoleRedirector::to($user);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->credentials())) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->isBlocked()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been blocked. Please contact admin.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return RoleRedirector::to($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google authentication failed. Please try again.',
            ]);
        }

        $exists = User::where('email', $googleUser->getEmail())->exists();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => bcrypt(Str::random(16)),
            ]
        );

        // Only notify admins if this is a brand new user
        if (!$exists) {
            User::where('role', 'admin')->each(fn($admin) =>
                $admin->notify(new AdminNewUserNotification($user->name, $user->id))
            );
            broadcast(new NewUser($user->name, $user->id));
            
        }

        if ($user->isBlocked()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been blocked. Please contact admin.',
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return RoleRedirector::to($user);
    }
}