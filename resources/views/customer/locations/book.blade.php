<x-layouts.customer>
<div class="page max-w-2xl"
     x-data="{
        hours: {{ old('hours', 1) }},
        rate: {{ (float) $location->price_per_hour }},
        get total() { return this.rate * (parseInt(this.hours) || 0); }
     }">

    <div class="flex items-center justify-between mb-6">
        <h1 class="title text-indigo-900">Book Location</h1>
        <a href="{{ route('customer.listings.show', $location->id) }}"
           class="text-sm text-indigo-700 hover:underline">&larr; Back</a>
    </div>

    {{-- LOCATION SUMMARY --}}
    <div class="card mb-4 flex gap-4 items-center">
        <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0">
            @if ($location->image)
                <img src="{{ asset('storage/'.$location->image) }}"
                     alt="" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-camera text-2xl text-gray-300"></i>
                </div>
            @endif
        </div>
        <div class="flex-1">
            <p class="font-semibold text-indigo-900">{{ $location->title }}</p>
            <p class="text-xs text-gray-500">{{ $location->city }} &middot; {{ $location->category }}</p>
            <p class="text-sm font-semibold text-indigo-800 mt-1">
                PKR {{ number_format((float) $location->price_per_hour) }} /hr
            </p>
        </div>
    </div>

    {{-- LOCATION ON MAP --}}
    <x-location-map
        :latitude="$location->latitude"
        :longitude="$location->longitude"
        :address="$location->address . ', ' . $location->city" />

    {{-- BOOKING FORM --}}
    <form method="POST" action="{{ route('customer.listings.book.store', $location->id) }}"
          class="card flex flex-col gap-4">
        @csrf

        <div>
            <label class="label">Booking Date &amp; Time</label>
            <div class="relative">
                <input type="datetime-local" name="booking_date"
                    value="{{ old('booking_date') }}"
                    min="{{ now()->timezone('Asia/Karachi')->format('Y-m-d\TH:i') }}"
                    class="input text-indigo-800 pr-10 @error('booking_date') border-red-400 @enderror"
                    required>
                <i class="fa-regular fa-calendar absolute right-3 top-1/2 -translate-y-1/2 text-indigo-900 pointer-events-none"></i>
            </div>
            @error('booking_date') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Hours</label>
            <input type="number" name="hours" x-model="hours"
                   min="1" max="24" step="1"
                   class="input text-indigo-800 @error('hours') border-red-400 @enderror"
                   required>
            @error('hours') <p class="error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Shoot Type (optional)</label>
            <input type="text" name="shoot_type"
                   value="{{ old('shoot_type') }}"
                   placeholder="e.g. Portrait, Wedding, Brand"
                   class="input text-indigo-800 @error('shoot_type') border-red-400 @enderror">
            @error('shoot_type') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="border-t border-indigo-100 pt-4 flex justify-between items-center">
            <span class="text-sm text-gray-500">Total</span>
            <span class="text-xl font-bold text-indigo-900">
                PKR <span x-text="total.toLocaleString()"></span>
            </span>
        </div>

        <button type="submit" class="btn">Send Booking Request</button>
        <p class="text-xs text-gray-400 text-center">
            The owner will review your request and confirm or decline.
        </p>
    </form>

</div>
</x-layouts.customer>
