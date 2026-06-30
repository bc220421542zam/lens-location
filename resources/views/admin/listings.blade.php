<x-layouts.admin>
<div>
    {{--Top Bar--}}
    <x-topbar 
        title="Listings Management"
        description="Manage all listings">
    </x-topbar>

    {{-- FILTERS --}}
    <x-adminComponents.filters-listings :categories="$categories" />

    {{-- LISTINGS TABLE --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl">
        <x-success class="mb-4" />
    <x-error class="mb-4" />
        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Listings</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[560px]">
                <thead>
                    <tr class="border-b border-indigo-400 text-indigo-900">
                        <th class="py-2 pl-2 text-sm">Title</th>
                        <th class="pl-2 text-sm">City</th>
                        <th class="pl-2 text-sm">Category</th>
                        <th class="pl-2 text-sm">Owner</th>
                        <th class="pl-2 text-sm">Price/hr</th>
                        <th class="pl-2 text-sm">Status</th>
                        <th class="pl-2 text-sm">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings as $listing)
                    <tr class="border-b border-indigo-400">
                        <td class="pl-2 py-2 text-sm text-indigo-900">{{ $listing->title }}</td>
                        <td class="pl-2 py-2 text-sm text-indigo-900">{{ $listing->city ?? 'N/A' }}</td>
                        <td class="pl-2 py-2 text-sm text-indigo-900">{{ $listing->category ?? 'N/A' }}</td>
                        <td class="pl-2 text-sm text-indigo-900">
                            {{ $listing->owner->first_name ?? 'N/A' }} {{ $listing->owner->last_name ?? '' }}
                        </td>
                        <td class="pl-2 text-sm text-indigo-900">Rs. {{ $listing->price_per_hour }}</td>
                        <td class="pl-2 text-sm {{ $listing->status->value === 'approved' ? 'text-green-800' : ($listing->status->value === 'rejected' ? 'text-red-500' : 'text-yellow-600') }}">
                            {{ ucfirst($listing->status->value) }}
                        </td>
                        <td class="flex items-center gap-3 py-2 pl-2">

                            <a href="{{ route('admin.listings.show', $listing->id) }}"
                               class="text-indigo-900 hover:text-indigo-700"
                               title="View details">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>

                            <form action="{{ route('admin.listings.toggle', $listing->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                            title="{{ $listing->status->value === 'approved' ? 'Reject Listing' : 'Approve Listing' }}"
                            class="p-1.5 transition hover:opacity-70">

                        @if($listing->status->value === 'approved')
                            {{-- ACTIVE --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-7 h-5">
                                <rect x="0" y="0" width="36" height="20" rx="10" fill="#09913b"/>
                                <circle cx="26" cy="10" r="7" fill="white"/>
                            </svg>
                        @else
                            {{-- INACTIVE --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-7 h-5">
                                <rect x="0" y="0" width="36" height="20" rx="10" fill="#e73636"/>
                                <circle cx="10" cy="10" r="7" fill="white"/>
                            </svg>
                        @endif

                    </button>
                            </form>

                            <form method="POST" action="{{ route('admin.listings.delete', $listing->id) }}"
                                onsubmit="return confirm('Delete this listing?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-indigo-400 text-sm">No listings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $listings->links() }}</div>

    </div>

</div>
</x-layouts.admin>