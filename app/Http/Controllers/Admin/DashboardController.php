<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;


class DashboardController extends Controller
{
    //Dashboard window
    public function index(){
        return view('admin.dashboard');
    }
    //Users window
    public function users()
    {
        $users = User::paginate(4);
        return view('admin.users', compact('users'));
    }
    //listings Window
     public function listings()
    {
        $listings = Location::with('owner')->paginate(10);
        return view('admin.listings', compact('listings'));
    }

    //user profile
    public function profile()
    {
        return view('admin.profile');
    }
}
