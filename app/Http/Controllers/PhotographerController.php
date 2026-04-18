<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotographerController extends Controller
{
    public function dashboard()
    {
        return view('photographer.dashboard');
    }
    
     public function bookings()
    {
        return view('photographer.bookings');
    }

    public function listings()
    {
        return view('photographer.listings');
    }
     public function profile()
    {
        return view('photographer.profile');
    }
}
