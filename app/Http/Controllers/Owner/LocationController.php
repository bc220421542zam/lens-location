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
        $data['images']  = $this->storeImages($request);
        $data['image']   = $data['images'][0] ?? null;   // first upload doubles as the cover

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

        $data     = $request->validated();
        $existing = $location->gallery();
        $uploaded = $this->storeImages($request);
        $gallery  = $this->arrangeGallery($request, $existing, $uploaded);

        // Whatever the owner dropped from the gallery is gone for good.
        $this->deleteImages(array_diff($existing, $gallery));

        $data['images'] = $gallery;
        $data['image']  = $gallery[0] ?? null;   // the first image is the cover

        $location->update($data);

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location updated successfully!');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $this->authorizeOwnership($location);

        $this->deleteImages($location->gallery());
        $location->delete();

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location deleted successfully.');
    }

    /** @return array<int, string> */
    private function storeImages($request): array
    {
        return collect($request->file('images', []))
            ->map(fn($file) => $file->store(self::IMAGE_FOLDER, self::IMAGE_DISK))
            ->all();
    }

    /**
     * Rebuild the gallery from the arrangement posted by the edit form. Each
     * token is either a path already on the listing or `new:<index>` pointing
     * at one of this request's uploads; anything else is ignored so the form
     * cannot make the listing reference arbitrary files.
     *
     * @param  array<int, string> $existing
     * @param  array<int, string> $uploaded
     * @return array<int, string>
     */
    private function arrangeGallery($request, array $existing, array $uploaded): array
    {
        $order = $request->input('image_order');

        if (! is_array($order)) {
            // No arrangement posted (e.g. JavaScript disabled): keep it all.
            return array_values(array_unique(array_merge($existing, $uploaded)));
        }

        $gallery = [];

        foreach ($order as $token) {
            if (! is_string($token)) {
                continue;
            }

            if (str_starts_with($token, 'new:')) {
                $index = (int) substr($token, 4);

                if (isset($uploaded[$index])) {
                    $gallery[] = $uploaded[$index];
                }
            } elseif (in_array($token, $existing, true)) {
                $gallery[] = $token;
            }
        }

        // Never orphan an upload the arrangement failed to mention.
        return array_values(array_unique(array_merge($gallery, array_diff($uploaded, $gallery))));
    }

    /** @param array<int, string> $paths */
    private function deleteImages(array $paths): void
    {
        foreach (array_filter($paths) as $path) {
            Storage::disk(self::IMAGE_DISK)->delete($path);
        }
    }

    private function authorizeOwnership(Location $location): void
    {
        abort_unless($location->user_id === auth()->id(), 403);
    }
}