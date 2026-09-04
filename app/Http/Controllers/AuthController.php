<?php

namespace App\Http\Controllers;

use App\Events\NewUser;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\AdminNewUserNotification;
use App\Support\RoleRedirector;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        $this->notifyAdminsOfNewUser($user);

        Auth::login($user);
        $request->session()->regenerate();

        return RoleRedirector::to($user);
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
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

        $isNewUser = ! User::where('email', $googleUser->getEmail())->exists();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => bcrypt(Str::random(16)),
            ]
        );

        if ($isNewUser) {
            $this->notifyAdminsOfNewUser($user);
        }

        if ($user->isBlocked()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been blocked. Please contact admin.',
            ]);
        }

        // No "remember me": returning visitors must sign in again.
        Auth::login($user);
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
