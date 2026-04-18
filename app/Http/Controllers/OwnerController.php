<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerController extends Controller
{

    public function listings()
    {
        return view('owner.listings');
    }

    public function bookings()
    {
        return view('owner.bookings');
    }
     public function profile()
    {
        return view('owner.profile');
    }
}