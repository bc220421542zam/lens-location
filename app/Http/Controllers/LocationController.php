<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    // Show the Add Location form
    public function create()
    {
        return view('owner.locations.create');
    }

    // Save new location to DB
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'category'       => 'required|string',
            'price_per_hour' => 'required|numeric|min:0',
            'description'    => 'required|string',
            'address'        => 'required|string',
            'city'           => 'required|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('locations', 'public');
        }

        Location::create([
            'user_id'        => auth()->id(),
            'title'          => $request->title,
            'category'       => $request->category,
            'price_per_hour' => $request->price_per_hour,
            'description'    => $request->description,
            'address'        => $request->address,
            'city'           => $request->city,
            'image'          => $imagePath,
            'status'         => 'pending',
        ]);

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location submitted! Waiting for admin approval.');
    }

    // Show edit form
    public function edit($id)
    {
        $location = Location::where('user_id', auth()->id())->findOrFail($id);
        return view('owner.locations.edit', compact('location'));
    }

    // Update location in DB
    public function update(Request $request, $id)
    {
        $location = Location::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'title'          => 'required|string|max:255',
            'category'       => 'required|string',
            'price_per_hour' => 'required|numeric|min:0',
            'description'    => 'required|string',
            'address'        => 'required|string',
            'city'           => 'required|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $imagePath = $location->image; // keep old image by default
        if ($request->hasFile('image')) {
            // Delete old image
            if ($location->image) {
                Storage::disk('public')->delete($location->image);
            }
            $imagePath = $request->file('image')->store('locations', 'public');
        }

        $location->update([
            'title'          => $request->title,
            'category'       => $request->category,
            'price_per_hour' => $request->price_per_hour,
            'description'    => $request->description,
            'address'        => $request->address,
            'city'           => $request->city,
            'image'          => $imagePath,
        ]);

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location updated successfully!');
    }

    // Delete location
    public function destroy($id)
    {
        $location = Location::where('user_id', auth()->id())->findOrFail($id);

        if ($location->image) {
            Storage::disk('public')->delete($location->image);
        }

        $location->delete();

        return redirect()
            ->route('owner.listings')
            ->with('success', 'Location deleted successfully.');
    }
}