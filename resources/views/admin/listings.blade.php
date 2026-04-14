<x-layouts.admin>
    <p class="title text-indigo-900">Listing Management</p>

    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Listings</h2>
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-800 text-sm"></i>
                <input type="text" placeholder="Search..."
                    class="border border-indigo-900 pl-4 pr-1 py-1 rounded-xl shade outline-none">
            </div>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-indigo-400 text-indigo-900">
                    <th class="py-2">Title</th>
                    <th>Owner</th>
                    <th>Price/hr</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
                <tbody>
        @forelse($listings as $listing)
        <tr class="border-b border-indigo-400">
            <td class="py-2 text-indigo-900">{{ $listing->title }}</td>
            <td class="text-indigo-900">{{ $listing->owner->name ?? 'N/A' }}</td>
            <td class="text-indigo-900">Rs. {{ $listing->price_per_hour }}</td>
            <td class="{{ $listing->status === 'approved' ? 'text-green-500' : ($listing->status === 'rejected' ? 'text-red-400' : 'text-yellow-500') }}">
                {{ ucfirst($listing->status) }}
            </td>
            <td class="flex items-center gap-3 py-2">
                <button>
                    <i class="fa-solid fa-toggle-on text-green-600" style="font-size:18px"></i>
                </button>
                <button>
                    <i class="fa-regular fa-eye text-indigo-700" style="font-size:15px"></i>
                </button>
                <button>
                    <i class="fa-regular fa-trash-can text-red-600" style="font-size:15px"></i>
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="py-4 text-center text-indigo-800">
                No listings found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $listings->links() }}
        </div>

    </div>

</x-layouts.admin>