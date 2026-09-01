<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Location;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search'   => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        // The nine featured listings the home page shows. A search from the
        // hero narrows this same set — results always come from the listings
        // displayed on the page, never the wider catalog.
        $locations = Location::query()
            ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->where('is_visible', true)], 'rating')
            ->where('status', ListingStatus::Approved)
            ->latest()
            ->take(9)
            ->get();

        $isSearch = $request->filled('search') || $request->filled('category');

        if ($isSearch) {
            $term = mb_strtolower((string) $request->string('search'));
            $category = mb_strtolower((string) $request->string('category'));

            $locations = $locations
                ->filter(function (Location $location) use ($term, $category): bool {
                    // Category matched case-insensitively: the categories
                    // table holds `Studio` while listings were saved with
                    // the lowercase form.
                    if ($category !== '' && mb_strtolower($location->category) !== $category) {
                        return false;
                    }

                    if ($term !== '') {
                        $haystack = mb_strtolower(implode(' ', [
                            $location->title, $location->city, $location->address,
                        ]));

                        if (! str_contains($haystack, $term)) {
                            return false;
                        }
                    }

                    return true;
                })
                ->values();
        }

        // Drives the hero search's category select (same source the browse
        // page filters use).
        $categories = Category::orderBy('name')->pluck('name');

        // Admin-managed marketing stats. Defaults keep the page identical
        // on a fresh database (or in tests) before any seed/save happens.
        $stats = [
            'locations' => Setting::get('home_locations', '500+'),
            'shoots'    => Setting::get('home_shoots', '12,000+'),
            'rating'    => Setting::get('home_rating', '4.9★'),
        ];

        return view('home', compact('locations', 'categories', 'isSearch', 'stats'));
    }

    public function about(): View
    {
        $stats = [
            'locations' => Setting::get('about_locations', '500+'),
            'roles'     => Setting::get('about_roles', '3'),
            'rating'    => Setting::get('about_rating', '4.9★'),
        ];

        return view('about', compact('stats'));
    }
}
