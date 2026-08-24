<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\ProfileController as BaseProfileController;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    /**
     * Payout details used to live in a `payments` table that had no migration,
     * so this method threw on every request. Stripe Connect owns payout details
     * now - the view renders the connect card from the authenticated user.
     */
    public function show(): View
    {
        return view('owner.profile');
    }

    protected function profileRouteName(): string
    {
        return 'owner.profile';
    }
}
