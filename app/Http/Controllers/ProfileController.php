<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdatePhotoRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Support\ProfilePhotoStorage;
use Illuminate\Http\RedirectResponse;
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
            ->with('tab', 'personal');
    }

    public function updatePhoto(UpdatePhotoRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->profile_picture = ProfilePhotoStorage::replace($user, $request->file('profile_picture'));
        $user->save();

        return back()->with('success', 'Profile photo updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('tab', 'password');
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return redirect()
            ->route($this->profileRouteName())
            ->with('success', 'Password updated successfully.')
            ->with('tab', 'password');
    }
}
