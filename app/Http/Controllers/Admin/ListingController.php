<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ListingController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): View
    {
        $listings = Location::with('owner')->paginate(self::PER_PAGE);

        return view('admin.listings', compact('listings'));
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

        return back()->with('success', 'Listing status updated.');
    }

    public function approve(Location $listing): RedirectResponse
    {
        $listing->status = ListingStatus::Approved;
        $listing->save();

        return back()->with('success', 'Listing approved.');
    }

    public function reject(Location $listing): RedirectResponse
    {
        $listing->status = ListingStatus::Rejected;
        $listing->save();

        return back()->with('success', 'Listing rejected.');
    }

    public function destroy(Location $listing): RedirectResponse
    {
        $listing->delete();

        return back()->with('success', 'Listing deleted successfully.');
    }
}
