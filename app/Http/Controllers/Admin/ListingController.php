<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Notifications\ListingStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $request->validate([
            'search'    => 'nullable|string|max:255',
            'status'    => 'nullable|in:pending,approved,rejected',
            'category'  => 'nullable|string|max:255',
            'max_price' => 'nullable|numeric|min:0',
            'sort'      => 'nullable|in:title,city,category,price_per_hour,status',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $allowedSorts = ['title', 'city', 'category', 'price_per_hour', 'status'];

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $listings = Location::query()
            ->with('owner')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('title', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhereHas('owner', fn ($q) => $q
                        ->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term));
            }))
            ->when($request->filled('status'),   fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price_per_hour', '<=', $request->float('max_price')))
            ->when(
                $sort && in_array($sort, $allowedSorts),
                fn ($q) => $q->orderBy($sort, $direction),
                fn ($q) => $q->latest()
            )
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $listings->onEachSide(1);

        $categories = Location::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.listings', compact('listings', 'categories'));
    }

    public function show(Location $listing): View
    {
        $listing->load('owner');

        return view('admin.listings.show', compact('listing'));
    }

    public function approve(Location $listing): RedirectResponse
    {
        // A rejected listing is terminal and cannot be re-approved.
        if ($listing->status === ListingStatus::Rejected) {
            return back()->with('error', 'A rejected listing cannot be approved.');
        }

        if ($listing->status === ListingStatus::Approved) {
            return back()->with('success', 'Listing is already approved.');
        }

        $listing->status = ListingStatus::Approved;
        $listing->save();

        //  Notify the owner that their listing was approved
        if ($listing->owner) {
            $listing->owner->notify(new ListingStatusNotification(
                $listing->title,
                'approved'
            ));
        }

        return back()->with('success', 'Listing approved.');
    }

    public function reject(Location $listing): RedirectResponse
    {
        // Already rejected — nothing to do (rejection is terminal).
        if ($listing->status === ListingStatus::Rejected) {
            return back()->with('success', 'Listing is already rejected.');
        }

        $listing->status = ListingStatus::Rejected;
        $listing->save();

        // Notify the owner that their listing was rejected
        if ($listing->owner) {
            $listing->owner->notify(new ListingStatusNotification(
                $listing->title,
                'rejected'
            ));
        }

        return back()->with('success', 'Listing rejected.');
    }

    public function destroy(Location $listing): RedirectResponse
    {
        $listing->delete();

        return back()->with('success', 'Listing deleted successfully.');
    }
}