<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ProfileController as BaseProfileController;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    public function show(): View
    {
        return view('admin.profile');
    }

    protected function profileRouteName(): string
    {
        return 'admin.profile';
    }
}
