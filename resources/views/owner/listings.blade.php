<x-layouts.owner>

  {{-- TOP BAR --}}
<x-topbar 
    title="My Listings"
    description="Manage your photography locations">
    <x-slot:actions>
        <a href="{{ route('owner.locations.create') }}"
           class="btn w-full sm:w-auto px-4 text-center shade btn-transition">
            <i class="fa-solid fa-plus"></i> Add Location
        </a>
    </x-slot:actions>
</x-topbar>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-600 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- LISTINGS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">

        @forelse($locations as $location)
        <div class="card card-transition p-0 overflow-hidden flex flex-col">

            {{-- IMAGE --}}
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

                {{-- STATUS BADGE --}}
                @if($location->status->value === 'approved')
                    <span class="badge badge-active">Approved</span>
                @elseif($location->status->value === 'pending')
                    <span class="badge badge-draft">Pending</span>
                @elseif($location->status->value === 'rejected')
                    <span class="badge badge-rejected">Rejected</span>
                @endif
            </div>

            {{-- INFO --}}
            <div class="p-3 sm:p-4 flex-1">
                <h3 class="font-semibold text-indigo-900">{{ $location->title }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $location->city }}
                    </p>
                    <p class="text-sm font-semibold text-indigo-800 mt-2">
                        PKR {{ number_format((float) $location->price_per_hour) }} /hr
                    </p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex gap-2 px-3 sm:px-4 pb-3 sm:pb-4 mt-auto">

                {{-- Edit --}}
                <a href="{{ route('owner.locations.edit', $location->id) }}"
                   class="action-btn shade text-center flex-1 text-sm py-1.5">
                   Edit
                </a>

                {{-- Delete --}}
                <form action="{{ route('owner.locations.destroy', $location->id) }}"
                      method="POST"
                      class="flex-1"
                      onsubmit="return confirm('Are you sure you want to delete this listing?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="action-btn shade danger w-full text-sm py-1.5">
                        Delete
                    </button>
                </form>

            </div>
        </div>

        @empty
        {{-- EMPTY STATE --}}
        <div class="col-span-full text-center py-16 sm:py-20 text-gray-400 px-4">
            <i class="icon fa-solid fa-camera text-4xl sm:text-5xl mb-3 block"></i>
            <p class="text-base sm:text-lg font-medium text-indigo-900">No listings yet</p>
            <p class="text-xs sm:text-sm mt-1">Click "+ Add Location" to create your first listing</p>
        </div>
        @endforelse

    </div>
</x-layouts.owner>