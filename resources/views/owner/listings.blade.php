<x-layouts.owner>

<div class="page">

    {{-- TOP BAR --}}
    <div class="top-bar">
        <div>
            <h1 class="title text-indigo-900">My Listings</h1>
            <p class="text-sm text-gray-500">Manage your photography locations</p>
        </div>
        <a href="{{ route('owner.locations.create') }}" class="btn w-auto px-4">
            + Add Location
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="label">Total Listings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="card">
            <p class="label">Approved</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="card">
            <p class="label">Pending</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="card">
            <p class="label">Total Bookings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">{{ $stats['bookings'] }}</p>
        </div>
    </div>

    {{-- LISTINGS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @forelse($locations as $location)
        <div class="card p-0 overflow-hidden">

            {{-- IMAGE --}}
            <div class="relative h-40 bg-gray-100">
                @if($location->image)
                    <img src="{{ Storage::url($location->image) }}"
                         alt="{{ $location->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-50">
                        <span class="text-5xl text-gray-300">📷</span>
                    </div>
                @endif

                {{-- STATUS BADGE --}}
                @if($location->status === 'approved')
                    <span class="badge badge-active">Approved</span>
                @elseif($location->status === 'pending')
                    <span class="badge badge-draft">Pending</span>
                @elseif($location->status === 'rejected')
                    <span class="badge" style="background:#fee2e2;color:#dc2626;">Rejected</span>
                @endif
            </div>

            {{-- INFO --}}
            <div class="p-4">
                <h3 class="font-semibold text-indigo-900">{{ $location->title }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $location->city }}
                    &middot;
                    PKR {{ number_format($location->price_per_hour) }}/hr
                    &middot;
                    {{ ucfirst($location->category) }}
                </p>
            </div>

            {{-- ACTION BUTTONS --}}
        <div class="flex gap-2 px-4 pb-4">

                {{-- Edit --}}
                <a href="{{ route('owner.locations.edit', $location->id) }}"
                   class="action-btn text-center">Edit</a>

                {{-- Delete --}}
                <form action="{{ route('owner.locations.destroy', $location->id) }}"
                      method="POST"
                      class="flex-1"
                      onsubmit="return confirm('Are you sure you want to delete this listing?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn danger w-full">Delete</button>
                </form>

            </div>
        </div>

        @empty
        {{-- EMPTY STATE --}}
        <div class="col-span-2 text-center py-20 text-gray-400">
            <p class="text-6xl mb-4">📷</p>
            <p class="text-lg font-medium text-indigo-900">No listings yet</p>
            <p class="text-sm mt-1">Click "+ Add Location" to create your first listing</p>
            <a href="{{ route('owner.locations.create') }}"
               class="btn w-auto px-6 mt-4 inline-block">
                + Add Location
            </a>
        </div>
        @endforelse

    </div>
</div>

</x-layouts.owner>