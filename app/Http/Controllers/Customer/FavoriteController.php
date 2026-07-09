<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favorites = Auth::user()
            ->favorites()
            ->with('owner')
            ->latest('favorites.created_at')
            ->paginate(9);

        return view('customer.favorites', compact('favorites'));
    }

    public function toggle(Location $location): RedirectResponse
    {
        $user = Auth::user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('location_id', $location->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from favorites.';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'location_id' => $location->id,
            ]);
            $message = 'Added to favorites.';
        }

        return back()->with('success', $message);
    }
}