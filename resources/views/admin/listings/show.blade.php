<x-layouts.admin>
    @php
        $statusClasses = [
            'approved' => 'bg-green-100 text-green-700',
            'pending'  => 'bg-yellow-100 text-yellow-700',
            'rejected' => 'bg-red-100 text-red-600',
        ];
        $current = $listing->status->value;
    @endphp

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <p class="title text-indigo-900">Listing Details</p>
            <a href="{{ route('admin.listings') }}" class="text-sm text-indigo-700 hover:underline">
                &larr; Back to listings
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="shade card card-transition bg-[#EEEFF7] p-6 rounded-2xl">
            {{-- GALLERY --}}
            @php($gallery = $listing->gallery())
            @if (count($gallery))
                {{-- Thumbnails swap the image above them, all in place --}}
                <div class="mb-6" x-data="{ active: '{{ asset('storage/'.$gallery[0]) }}' }">
                    <img :src="active"
                         src="{{ asset('storage/'.$gallery[0]) }}"
                         alt="{{ $listing->title }}"
                         class="w-full h-64 sm:h-80 object-cover rounded-xl">

                    @if (count($gallery) > 1)
                        <div class="flex gap-2 mt-2 overflow-x-auto pb-1">
                            @foreach ($gallery as $path)
                                @php($url = asset('storage/'.$path))
                                <button type="button"
                                    @click="active = '{{ $url }}'"
                                    class="shrink-0 rounded-lg overflow-hidden border-2 transition-colors"
                                    :class="active === '{{ $url }}' ? 'border-indigo-600' : 'border-transparent hover:border-indigo-300'"
                                    aria-label="Show photo {{ $loop->iteration }}">
                                    <img src="{{ $url }}"
                                         alt="{{ $listing->title }} photo {{ $loop->iteration }}"
                                         class="h-16 w-24 object-cover">
                                </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ count($gallery) }} photos &middot; first is the cover
                        </p>
                    @endif
                </div>
            @endif

            <div class="flex items-start justify-between gap-4 mb-2">
                <h2 class="text-2xl font-bold text-indigo-900">{{ $listing->title }}</h2>
                <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusClasses[$current] ?? '' }}">
                    {{ ucfirst($current) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mb-4">{{ $listing->category }} &middot; {{ $listing->city }}</p>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Owner</dt>
                    <dd class="font-medium text-indigo-900">{{ $listing->owner->name }}</dd>
                    <dd class="text-xs text-gray-500">{{ $listing->owner->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-900">Price / hour</dt>
                    <dd class="font-medium text-indigo-900">PKR {{ number_format((float) $listing->price_per_hour) }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs uppercase underline text-indigo-900">Address</dt>
                    <dd class="font-medium text-indigo-900">{{ $listing->address }}</dd>
                </div>
            </dl>

            <div class="mb-6">
                <dt class="text-xs uppercase underline text-indigo-900 mb-1">Description</dt>
                <dd class="text-gray-500 whitespace-pre-line">{{ $listing->description }}</dd>
            </div>

            <div class="flex flex-wrap gap-2 pt-4 border-t border-indigo-100">
                @if ($current === 'pending')
                    <form method="POST" action="{{ route('admin.listings.approve', $listing->id) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 btn-transition bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                            <i class="fa-solid fa-check mr-1"></i> Approve
                        </button>
                    </form>
                @endif
                @if ($current !== 'rejected')
                    <form method="POST" action="{{ route('admin.listings.reject', $listing->id) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 btn-transition bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                            <i class="fa-solid fa-xmark mr-1"></i> Reject
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.listings.delete', $listing->id) }}"
                      onsubmit="return confirm('Permanently delete this listing?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 btn-transition bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                        <i class="fa-regular fa-trash-can mr-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
