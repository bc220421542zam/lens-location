<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\ProfileController as BaseProfileController;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    public function show(): View
    {
        return view('photographer.profile');
    }

    protected function profileRouteName(): string
    {
        return 'photographer.profile';
    }
}
