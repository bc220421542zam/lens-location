<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('photographer.dashboard');
    }

    public function bookings(): View
    {
        return view('photographer.bookings');
    }

    public function listings(): View
    {
        return view('photographer.listings');
    }

    public function profile(): View
    {
        return view('photographer.profile');
    }
}
