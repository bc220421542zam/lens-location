<x-layouts.admin>
<div x-data="{
    showModal: false,
    selectedListing: null,
    openListing(listing) {
        this.selectedListing = listing;
        this.showModal = true;
    }
}">

    <p class="title text-indigo-900">Listings Management</p>

    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Listings</h2>

            <form method="GET" action="{{ route('admin.listings') }}"
                  class="flex flex-col sm:flex-row sm:items-center gap-2">

                <div class="relative flex items-center flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-800 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by title, city, or owner"
                           class="border border-indigo-900 pl-4 pr-8 py-1 rounded-xl shade outline-none w-full text-sm">
                </div>

                <select name="status"
                        class="border border-indigo-900 px-3 py-1 rounded-xl shade outline-none text-sm">
                    <option value="">All statuses</option>
                    <option value="pending"  @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>

                <select name="category"
                        class="border border-indigo-900 px-3 py-1 rounded-xl shade outline-none text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-indigo-900 text-white text-sm px-4 py-1 rounded-xl hover:bg-[#2C3399]">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('admin.listings') }}"
                       class="text-sm text-indigo-700 px-3 py-1 rounded-xl border border-indigo-300 hover:bg-indigo-50">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[560px]">
                <thead>
                    <tr class="border-b border-indigo-400 text-indigo-900">
                        <th class="py-2 pl-2 text-sm">Title</th>
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
                        <td class="pl-2 text-sm text-indigo-900">
                            {{ $listing->owner->first_name ?? 'N/A' }} {{ $listing->owner->last_name ?? '' }}
                        </td>
                        <td class="pl-2 text-sm text-indigo-900">Rs. {{ $listing->price_per_hour }}</td>
                        <td class="pl-2 text-sm {{ $listing->status->value === 'approved' ? 'text-green-800' : ($listing->status->value === 'rejected' ? 'text-red-500' : 'text-yellow-600') }}">
                            {{ ucfirst($listing->status->value) }}
                        </td>
                        <td class="flex items-center gap-3 py-2 pl-2">

                            <button @click="openListing({{ $listing->toJson() }})" class="text-indigo-900 hover:text-indigo-700">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </button>

                            <form action="{{ route('admin.listings.toggle', $listing->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ $listing->status->value === 'approved' ? 'Pending' : 'Approve' }}">
                                    <i class="fa-solid {{ $listing->status->value === 'approved' ? 'fa-toggle-on' : 'fa-toggle-off' }} text-green-800 text-lg"></i>
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

    {{-- MODAL --}}
    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="display:none">

        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-md z-10 max-h-[90vh] overflow-y-auto">

            <button @click="showModal = false"
                class="absolute top-3 right-4 text-indigo-800 hover:text-indigo-900 text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-indigo-900 mb-4">Listing Detail</h2>

            <template x-if="selectedListing">
                <div class="space-y-3">

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Title</span>
                        <span class="text-indigo-900 text-sm text-right ml-4" x-text="selectedListing.title"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm shrink-0 pr-4">Description</span>
                        <span class="text-indigo-900 text-sm text-right" x-text="selectedListing.description"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">City</span>
                        <span class="text-indigo-900 text-sm" x-text="selectedListing.city"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Address</span>
                        <span class="text-indigo-900 text-sm text-right ml-4" x-text="selectedListing.address"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Category</span>
                        <span class="text-indigo-900 text-sm" x-text="selectedListing.category"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Price/hr</span>
                        <span class="text-indigo-900 text-sm">Rs. <span x-text="selectedListing.price_per_hour"></span></span>
                    </div>

                    <div class="flex justify-between pb-2">
                        <span class="text-indigo-900 text-sm">Status</span>
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