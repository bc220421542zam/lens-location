<x-layouts.admin>
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <p class="title text-indigo-900">Listing Details</p>
            <a href="{{ route('admin.listings') }}" class="text-sm text-indigo-700 hover:underline">
                &larr; Back to listings
            </a>
        </div>

        <div class="shade bg-[#EEEFF7] p-6 rounded-2xl">
            @if ($listing->image)
                <img src="{{ asset('storage/'.$listing->image) }}"
                     alt="{{ $listing->title }}"
                     class="w-full h-64 object-cover rounded-xl mb-6">
            @endif

            <h2 class="text-2xl font-bold text-indigo-900 mb-2">{{ $listing->title }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ $listing->category }} &middot; {{ $listing->city }}</p>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <dt class="text-xs uppercase text-gray-500">Owner</dt>
                    <dd class="font-medium">{{ $listing->owner->first_name }} {{ $listing->owner->last_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Status</dt>
                    <dd class="font-medium capitalize">{{ $listing->status->value }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Price / hour</dt>
                    <dd class="font-medium">${{ number_format((float) $listing->price_per_hour, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-gray-500">Address</dt>
                    <dd class="font-medium">{{ $listing->address }}</dd>
                </div>
            </dl>

            <div>
                <dt class="text-xs uppercase text-gray-500 mb-1">Description</dt>
                <dd class="text-gray-700 whitespace-pre-line">{{ $listing->description }}</dd>
            </div>
        </div>
    </div>
</x-layouts.admin>
