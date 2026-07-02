<x-layouts.customer>
<div class="page max-w-3xl">

    <div class="flex items-center justify-between mb-6">
        <h1 class="title text-indigo-900">{{ $location->title }}</h1>
        <a href="{{ route('customer.listings') }}" class="text-sm text-indigo-700 hover:underline">
            &larr; Back to listings
        </a>
    </div>

    <div class="card p-0 overflow-hidden">
        @if ($location->image)
            <img src="{{ asset('storage/'.$location->image) }}"
                 alt="{{ $location->title }}"
                 class="w-full h-72 object-cover">
        @endif

        <div class="p-6">
            <p class="text-sm text-gray-500 mb-4">
                {{ $location->category }} &middot; {{ $location->city }}
            </p>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Owner</dt>
                    <dd class="font-medium text-indigo-900">{{ $location->owner->name }}</dd>
                    <dd class="text-xs text-gray-500">{{ $location->owner->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Price / hour</dt>
                    <dd class="font-medium text-indigo-900">PKR {{ number_format((float) $location->price_per_hour) }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase underline text-indigo-900">Address</dt>
                    <dd class="font-medium text-indigo-900">{{ $location->address }}</dd>
                </div>
            </dl>

            <div class="mb-6">
                <dt class="text-xs uppercase underline text-indigo-900 mb-1">Description</dt>
                <dd class="text-gray-500 whitespace-pre-line">{{ $location->description }}</dd>
            </div>

            <a href="{{ route('customer.listings.book', $location->id) }}"
               class="btn w-full md:w-auto md:px-6 inline-block text-center">
                Book this location
            </a>
        </div>
    </div>

</div>
</x-layouts.customer>
