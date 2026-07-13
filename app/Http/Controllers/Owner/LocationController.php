<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ListingStatus;
use App\Events\NewListing;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreLocationRequest;
use App\Http\Requests\Owner\UpdateLocationRequest;
use App\Models\Location;
use App\Models\User;
use App\Notifications\AdminNewListingNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LocationController extends Controller
{
    private const IMAGE_DISK   = 'public';
    private const IMAGE_FOLDER = 'locations';

    public function create(): View
    {
        return view('owner.locations.create');
    }

    public function show(Location $location): View
{
    $this->authorizeOwnership($location);

    $location->load('owner');

    return view('owner.locations.show', compact('location'));
}

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status']  = ListingStatus::Pending;
        $data['image']   = $this->storeImage($request);

        $listing = Location::create($data);

        //  Notify all admins about the new listing
        User::where('role', 'admin')->each(fn($admin) =>
            $admin->notify(new AdminNewListingNotification($listing->title, $listing->id))
        );

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location submitted! Waiting for admin approval.');
    }

    public function edit(Location $location): View
    {
        $this->authorizeOwnership($location);

        return view('owner.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $this->authorizeOwnership($location);

        $data          = $request->validated();
        $data['image'] = $this->storeImage($request, replacing: $location->image) ?? $location->image;

        $location->update($data);

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location updated successfully!');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorizeOwnership($location);

        if ($location->image) {
            Storage::disk(self::IMAGE_DISK)->delete($location->image);
        }
        $location->delete();

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location deleted successfully.');
    }

    private function storeImage($request, ?string $replacing = null): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        if ($replacing) {
            Storage::disk(self::IMAGE_DISK)->delete($replacing);
        }

        return $request->file('image')->store(self::IMAGE_FOLDER, self::IMAGE_DISK);
    }

    private function authorizeOwnership(Location $location): void
    {
        abort_unless($location->user_id === auth()->id(), 403);
    }
}