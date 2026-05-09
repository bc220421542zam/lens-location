<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class RoleRedirector
{
    public static function to(User $user): RedirectResponse
    {
        return redirect()->route($user->role->dashboardRoute());
    }
}
