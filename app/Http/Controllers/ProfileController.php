<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdatePhotoRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

abstract class ProfileController extends Controller
{
    /** Route name to redirect back to after profile actions (e.g. 'admin.profile'). */
    abstract protected function profileRouteName(): string;

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()
            ->route($this->profileRouteName())
            ->with('success', 'Profile updated successfully.')
            ->with('tab', 'profile');
    }

    public function updatePhoto(UpdatePhotoRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new photo on public disk
        $path = $request->file('profile_picture')->store('profile-photos', 'public');
        $user->profile_picture = $path;
        $user->save();

        return back()->with('success', 'Profile photo updated.');
    }

    // Update Password
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('tab', 'change-password');
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return redirect()
            ->route($this->profileRouteName())
            ->with('success', 'Password updated successfully.')
            ->with('tab', 'change-password');
    }
    // Notifications
    public function updateNotifications(Request $request)
{
    $request->validate([
        'notif_push'  => 'boolean',
        'notif_email' => 'boolean',
        'notif_sms'   => 'boolean',
    ]);

    $request->user()->update($request->only([
        'notif_push', 'notif_email', 'notif_sms',
    ]));

    return response()->json(['status' => 'ok']);
}
}