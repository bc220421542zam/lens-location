<x-layouts.admin>
    <p class="title text-indigo-900">Dashboard</p>

    <div class="max-w-xl">
        {{-- CARDS --}}
        <div class="grid grid-cols-3 gap-6 mb-6">

            <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <h3 class="text-sm text-indigo-900 mb-3">Total Users</h3>
                    <i class="fa-solid fa-users text-m text-indigo-900 shrink-0 ml-2"></i>
                </div>
                <p class="text-2xl font-bold text-indigo-900">{{ $stats['users'] }}</p>
            </div>

            <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <h3 class="text-sm text-indigo-900 mb-3">Total Listings</h3>
                    <i class="fa-solid fa-location-dot text-m text-indigo-900 shrink-0 ml-2"></i>
                </div>
                <p class="text-2xl font-bold text-indigo-900">{{ $stats['listings'] }}</p>
            </div>

            <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <h3 class="text-sm text-indigo-900 mb-3">Total Bookings</h3>
                    <i class="fa-solid fa-calendar-days text-m text-indigo-900 shrink-0 ml-2"></i>
                </div>
                <p class="text-2xl font-bold text-indigo-900">{{ $stats['bookings'] }}</p>
            </div>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">Recent Listings</h2>
            <div class="relative flex items-center">
                <i class="fa-solid text-indigo-900 fa-magnifying-glass absolute right-3"></i>
                <input type="text" placeholder="Search..."
                    class="border border-indigo-900 pl-4 pr-3 py-1 rounded-xl shade outline-none">
            </div>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-indigo-400 text-indigo-900">
                    <th class="py-2">Title</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($recentListings as $listing)
                <tr class="border-b border-indigo-400">

                    {{-- Title --}}
                    <td class="py-2 text-indigo-900">{{ $listing->title }}</td>

                    {{-- Owner --}}
                    <td class="text-indigo-900">{{ $listing->owner->first_name ?? 'N/A' }} {{ $listing->owner->last_name ?? 'N/A' }}</td>

                    {{-- Status --}} 
                    <td>
                        @if($listing->status === 'approved')
                            <span class="text-green-600">Approved</span>
                        @elseif($listing->status === 'pending')
                            <span class="text-yellow-500">Pending</span>
                        @elseif($listing->status === 'rejected')
                            <span class="text-red-500">Rejected</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="flex items-center gap-3 py-2">

                        {{-- Approve / Reject Toggle --}}
                        <form action="{{ route('admin.listings.toggle', $listing->id) }}" method="POST">
                            @csrf
                            <button type="submit" title="{{ $listing->status === 'approved' ? 'Reject' : 'Approve' }}">
                                <i class="icon fa-solid
                                    {{ $listing->status === 'approved' ? 'fa-toggle-on text-green-800' : 'fa-toggle-off text-green-800' }}"
                                    style="font-size: 18px">
                                </i>
                            </button>
                        </form>

                        {{-- View --}}
                        <a href="{{ route('admin.listings.show', $listing->id) }}">
                            <i class="icon fa-regular fa-eye text-indigo-900" style="font-size: 15px"></i>
                        </a>

                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-400">
                        No listings yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-layouts.admin>