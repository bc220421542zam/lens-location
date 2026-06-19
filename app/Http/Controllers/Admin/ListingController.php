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
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

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

    public function toggleApproval(Location $listing): RedirectResponse
    {
        $listing->status = $listing->status->toggleApproval();
        $listing->save();

        //  Notify the owner about the status change
        if ($listing->owner) {
            $listing->owner->notify(new ListingStatusNotification(
                $listing->title,
                $listing->status->value  // passes the actual current status
            ));
        }

        return back()->with('success', 'Listing status updated.');
    }

    public function approve(Location $listing): RedirectResponse
    {
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