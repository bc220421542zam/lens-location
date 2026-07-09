<x-layouts.owner>
<div x-data="{ tab: '{{ request('status', 'all') }}' }">

    {{-- TOP BAR --}}
    <x-topbar 
    title="Manage Bookings"
    description="Manage incoming booking requests">
    </x-topbar>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- STATS --}}
    <x-ownerComp.booking-stats :stats="$stats" />

    {{-- FILTER TABS --}}
    <x-ownerComp.booking-filter />
    {{-- BOOKINGS LIST --}}
    <div class="flex flex-col gap-3">

        @forelse($bookings as $booking)
        <div class="card chart-transition rounded-2xl border border-l-3 border-indigo-400 flex items-center gap-4">

            {{-- AVATAR --}}
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center font-semibold text-indigo-700 shrink-0">
                {{ strtoupper(substr($booking->customer->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($booking->customer->last_name ?? '', 0, 1)) }}
            </div>

            {{-- INFO --}}
            <div class="flex-1">
                <p class="font-semibold text-indigo-900">
                    {{ $booking->customer->first_name ?? 'Unknown' }}
                    {{ $booking->customer->last_name ?? '' }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $booking->location->title ?? 'N/A' }}
                    &middot; {{ $booking->hours }} hrs
                    @if($booking->shoot_type) &middot; {{ $booking->shoot_type }} @endif
                </p>
            </div>

            {{-- DATE & PRICE --}}
            <div class="text-right shrink-0">
                <p class="text-sm text-gray-400">
                    {{ $booking->booking_date->format('M d, Y') }}
                    &middot;
                    {{ $booking->booking_date->format('g:i A') }}
                </p>
                <p class="font-semibold text-indigo-900">
                    PKR {{ number_format($booking->total_price) }}
                </p>

                {{-- STATUS BADGE --}}
                @if($booking->status->value === 'pending')
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                @elseif($booking->status->value === 'confirmed')
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">Confirmed</span>
                @elseif($booking->status->value === 'completed')
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">Completed</span>
                @elseif($booking->status->value === 'cancelled')
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-red-100 text-red-600">Cancelled</span>
                @endif
            </div>

            {{-- ACTIONS (only for pending) --}}
            @if($booking->status->value === 'pending')
            <div class="flex flex-col gap-1 shrink-0">
                <form action="{{ route('owner.bookings.accept', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="action-btn publish w-full">Accept</button>
                </form>
                <form action="{{ route('owner.bookings.reject', $booking->id) }}" method="POST"
                      onsubmit="return confirm('Reject this booking?')">
                    @csrf
                    <button type="submit" class="action-btn danger w-full">Reject</button>
                </form>
            </div>
            @endif

        </div>
        @empty
        <div class="card text-center py-10 text-gray-400">
            <i class="fa-solid fa-calendar"></i>
            <p class="font-medium">No bookings found</p>
            <p class="text-sm mt-1">
                {{ request('status') ? 'No ' . request('status') . ' bookings.' : 'You have no bookings yet.' }}
            </p>
        </div>
        @endforelse

    </div>
</div>
</x-layouts.owner>