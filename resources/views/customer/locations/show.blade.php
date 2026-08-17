<x-layouts.customer>
<div class="page max-w-3xl">

    <div class="flex items-center justify-between mb-6">
        <h1 class="title text-indigo-900">{{ $location->title }}</h1>
        <a href="{{ route('customer.listings') }}" class="text-sm text-indigo-700 hover:underline">
            &larr; Back to listings
        </a>
    </div>

    <div class="card p-0 overflow-hidden">
        {{-- GALLERY — thumbnails swap the image above them, all in place --}}
        @php($gallery = $location->gallery())
        @if (count($gallery))
            <div x-data="{ active: '{{ asset('storage/'.$gallery[0]) }}' }">
                <img :src="active"
                     src="{{ asset('storage/'.$gallery[0]) }}"
                     alt="{{ $location->title }}"
                     class="w-full h-72 object-cover">

                @if (count($gallery) > 1)
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 p-3 border-b border-gray-100">
                        @foreach ($gallery as $path)
                            @php($url = asset('storage/'.$path))
                            <button type="button"
                                @click="active = '{{ $url }}'"
                                class="rounded-lg overflow-hidden border-2 transition-colors"
                                :class="active === '{{ $url }}' ? 'border-indigo-600' : 'border-transparent hover:border-indigo-300'"
                                aria-label="Show photo {{ $loop->iteration }}">
                                <img src="{{ $url }}"
                                     alt="{{ $location->title }} photo {{ $loop->iteration }}"
                                     class="w-full h-16 object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
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

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('customer.listings.book', $location->id) }}"
                   class="btn w-full sm:w-auto md:px-6 inline-block text-center">
                    Book this location
                </a>

                <form action="{{ route('customer.listings.message', $location->id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="action-btn shade w-full sm:w-auto md:px-6 py-3">
                        <i class="fa-solid fa-message mr-1"></i> Message Owner
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.customer>