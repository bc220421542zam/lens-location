<x-layouts.admin>

<div class="max-w-5xl" x-data="{
    showModal: false,
    selectedListing: null,
    openListing(listing) {
        this.selectedListing = listing;
        this.showModal = true;
    }
}">

    <p class="title text-indigo-900">Listings Management</p>

    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        {{-- your search bar and table here --}}

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
                    <td class="text-indigo-900">{{ $listing->owner->first_name ?? 'N/A' }} {{ $listing->owner->last_name ?? '' }}</td>
                    <td class="text-indigo-900">Rs. {{ $listing->price_per_hour }}</td>
                    <td class="{{ $listing->status === 'approved' ? 'text-green-800' : ($listing->status === 'rejected' ? 'text-red-500' : 'text-yellow-600') }}">
                        {{ ucfirst($listing->status) }}
                    </td>
                    <td class="flex items-center gap-3 py-2">

                        {{-- VIEW --}}
                        <button @click="openListing({{ $listing->toJson() }})"
                            class="text-indigo-900 hover:text-indigo-700">
                            <i class="fa-regular fa-eye" style="font-size:15px"></i>
                        </button>

                        {{-- TOGGLE --}}
                        <form method="POST" action="{{ route('admin.listings.toggle', $listing->id) }}">
                            @csrf
                            <button type="submit" class="{{ $listing->status === 'approved' ? 'text-green-800' : 'text-green-800' }} hover:opacity-75">
                                <i class="fa-solid {{ $listing->status === 'approved' ? 'fa-toggle-on' : 'fa-toggle-off' }}"
                                    style="font-size:18px"></i>
                            </button>
                        </form>

                        {{-- DELETE --}}
                        <form method="POST" action="{{ route('admin.listings.delete', $listing->id) }}"
                            onsubmit="return confirm('Are you sure you want to delete this listing?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">
                                <i class="fa-regular fa-trash-can" style="font-size:15px"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-indigo-400">No listings found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $listings->links() }}</div>

    </div>

    {{-- DETAIL POPUP MODAL --}}
    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display:none">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-md z-10">

            {{-- Close --}}
            <button @click="showModal = false"
                class="absolute top-3 right-4 text-indigo-400 hover:text-indigo-900 text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-indigo-900 mb-4">Listing Detail</h2>

            <template x-if="selectedListing">
                <div class="space-y-3">

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-400 text-sm">Title</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedListing.title"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-400 text-sm">Description</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedListing.description"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-400 text-sm">City</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedListing.city"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-400 text-sm">Address</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedListing.address"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-400 text-sm">Category</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedListing.category"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-400 text-sm">Price/hr</span>
                        <span class="text-indigo-900 text-sm font-medium">Rs. <span x-text="selectedListing.price_per_hour"></span></span>
                    </div>

                    <div class="flex justify-between pb-2">
                        <span class="text-indigo-400 text-sm">Status</span>
                        <span class="text-sm font-medium"
                            :class="{
                                'text-green-500' : selectedListing.status === 'approved',
                                'text-yellow-500': selectedListing.status === 'pending',
                                'text-red-400'   : selectedListing.status === 'rejected'
                            }"
                            x-text="selectedListing.status">
                        </span>
                    </div>

                </div>
            </template>

            <div class="mt-5 text-right">
                <button @click="showModal = false"
                    class="bg-indigo-900 text-white px-5 py-2 rounded-lg text-sm hover:bg-[#2C3399]">
                    Close
                </button>
            </div>

        </div>
    </div>

</div>
</x-layouts.admin>