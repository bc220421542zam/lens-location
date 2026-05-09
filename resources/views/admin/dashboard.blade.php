<x-layouts.admin>
    <p class="title text-indigo-900">Dashboard</p>

    {{-- CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Total Users</h3>
                <i class="fa-solid fa-users text-indigo-900 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-900">{{ $stats['users'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Total Listings</h3>
                <i class="fa-solid fa-location-dot text-indigo-900 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-900">{{ $stats['listings'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Total Bookings</h3>
                <i class="fa-solid fa-calendar-days text-indigo-900 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-900">{{ $stats['bookings'] }}</p>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">Recent Listings</h2>
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-900 text-sm"></i>
                <input type="text" placeholder="Search..."
                    class="border border-indigo-900 pl-4 pr-8 py-1 rounded-xl shade outline-none w-full sm:w-auto text-sm">
            </div>
        </div>

        {{-- Scrollable table wrapper --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[480px]">
                <thead>
                    <tr class="border-b border-indigo-400 text-indigo-900">
                        <th class="py-2 text-sm">Title</th>
                        <th class="text-sm">Owner</th>
                        <th class="text-sm">Status</th>
                        <th class="text-sm">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentListings as $listing)
                    <tr class="border-b border-indigo-400">

                        <td class="py-2 text-sm text-indigo-900">{{ $listing->title }}</td>

                        <td class="text-sm text-indigo-900">
                            {{ $listing->owner->first_name ?? 'N/A' }} {{ $listing->owner->last_name ?? '' }}
                        </td>

                        <td>
                            @if($listing->status === 'approved')
                                <span class="text-green-600 text-sm">Approved</span>
                            @elseif($listing->status === 'pending')
                                <span class="text-yellow-500 text-sm">Pending</span>
                            @elseif($listing->status === 'rejected')
                                <span class="text-red-500 text-sm">Rejected</span>
                            @endif
                        </td>

                        <td class="flex items-center gap-3 py-2">
                            <form action="{{ route('admin.listings.toggle', $listing->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ $listing->status === 'approved' ? 'Reject' : 'Approve' }}">
                                    <i class="fa-solid {{ $listing->status === 'approved' ? 'fa-toggle-on' : 'fa-toggle-off' }} text-green-800 text-lg"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.listings.show', $listing->id) }}">
                                <i class="fa-regular fa-eye text-indigo-900 text-sm"></i>
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400 text-sm">No listings yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-layouts.admin>