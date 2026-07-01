<x-layouts.customer>
<div>

    {{-- TOP BAR --}}
    <x-topbar 
        title="Search Listings"
        description="Find a place for your next shoot">
    </x-topbar>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-600 text-sm">{{ session('success') }}</div>
    @endif

    {{-- FILTERS --}}
    <x-customerComp.listing-filter-comp :categories="$categories" />

    {{-- LISTINGS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($locations as $location)
            <div class="card card-transition rounded-2xl flex flex-col p-0 overflow-hidden">
                <div class="relative h-36 sm:h-40 bg-gray-100 shrink-0">
                    @if($location->image)
                        <img src="{{ Storage::url($location->image) }}"
                            alt="{{ $location->title }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-50">
                            <span class="text-5xl text-gray-300">
                                <i class="fa-solid fa-camera"></i>
                            </span>
                        </div>
                    @endif
                    <span class="badge badge-active">{{ $location->category }}</span>
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-semibold text-indigo-900">{{ $location->title }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $location->city }}
                    </p>
                    <p class="text-sm font-semibold text-indigo-800 mt-2">
                        PKR {{ number_format((float) $location->price_per_hour) }} /hr
                    </p>
                    <div class="flex gap-2 mt-3 pt-3 border-t border-indigo-100">
                        <a href="{{ route('customer.listings.show', $location->id) }}"
                           class="action-btn btn-transition shade text-center">View</a>
                        <a href="{{ route('customer.listings.book', $location->id) }}"
                           class="action-btn btn-transition shade publish text-center">Book</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full card card-transition text-center py-10 text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-2xl mb-2"></i>
                <p class="font-medium">No listings match your filters</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $locations->links() }}
    </div>

</div>
</x-layouts.customer>
