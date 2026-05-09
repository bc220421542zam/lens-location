<x-layouts.photographer>
<div class="page">

    {{-- TOP BAR --}}
    <div class="top-bar">
        <div>
            <h1 class="title text-indigo-900">Search Listings</h1>
            <p class="text-sm text-gray-500">Find a place for your next shoot</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    {{-- FILTERS --}}
    <form method="GET" action="{{ route('photographer.listings') }}"
          class="card mb-4 flex flex-col md:flex-row gap-3 md:items-end">
        <div class="flex-1">
            <label class="label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Title, city, description"
                   class="input">
        </div>
        <div class="w-full md:w-48">
            <label class="label">Category</label>
            <select name="category" class="input">
                <option value="">All</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-40">
            <label class="label">Max price/hr</label>
            <input type="number" name="max_price" min="0" step="100"
                   value="{{ request('max_price') }}"
                   class="input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn w-auto px-4">Filter</button>
            <a href="{{ route('photographer.listings') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Reset</a>
        </div>
    </form>

    {{-- LISTINGS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($locations as $location)
            <div class="card flex flex-col p-0 overflow-hidden">
                <div class="relative aspect-[4/3] bg-gray-100">
                    @if ($location->image)
                        <img src="{{ asset('storage/'.$location->image) }}"
                             alt="{{ $location->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-50">
                            <i class="fa-solid fa-camera text-5xl text-gray-300"></i>
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
                        <a href="{{ route('photographer.listings.show', $location->id) }}"
                           class="action-btn text-center">View</a>
                        <a href="{{ route('photographer.listings.book', $location->id) }}"
                           class="action-btn publish text-center">Book</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full card text-center py-10 text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-2xl mb-2"></i>
                <p class="font-medium">No listings match your filters</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $locations->links() }}
    </div>

</div>
</x-layouts.photographer>
