<?php

namespace App\Http\Controllers\Customer;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrowseController extends Controller
{
    private const PER_PAGE = 12;

    public function index(Request $request): View
    {
        $locations = Location::query()
            ->with('owner')
            ->where('status', ListingStatus::Approved)
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('title', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('description', 'like', $term);
            }))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price_per_hour', '<=', $request->integer('max_price')))
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $categories = Location::where('status', ListingStatus::Approved)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('customer.listings', compact('locations', 'categories'));
    }

    public function show(Location $location): View
    {
        abort_unless($location->status === ListingStatus::Approved, 404);

        $location->load('owner');

        return view('customer.locations.show', compact('location'));
    }
}