<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\ProfileController as BaseProfileController;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    public function show(): View
    {
        return view('customer.profile');
    }

    protected function profileRouteName(): string
    {
        return 'customer.profile';
    }
}
