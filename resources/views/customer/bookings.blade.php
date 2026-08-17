<x-layouts.customer>
<div>

    {{-- TOP BAR --}}
    <x-topbar 
        title="My Bookings"
        description="Manage your booking requests">
        <x-slot:actions>
        <a href="{{ route('customer.listings') }}"
           class="btn btn-transition w-full sm:w-auto px-4 text-center">
            Find Location
        </a>
    </x-slot:actions>
    </x-topbar>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    {{-- STATS --}}
    <x-customerComp.statsBookings :stats="$stats" />

    {{-- FILTER TABS --}}
    <x-customerComp.booking-filter />

    {{-- BOOKINGS LIST --}}
    <div class="flex flex-col gap-3">
        @forelse ($bookings as $booking)
            <div class="card chart-transition rounded-2xl border border-l-3 border-indigo-400 flex flex-col md:flex-row md:items-start gap-4">
                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                    @if ($booking->location?->image)
                        <img src="{{ asset('storage/'.$booking->location->image) }}"
                             alt="" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fa-solid fa-camera text-gray-300"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-indigo-900">{{ $booking->location?->title ?? 'Deleted location' }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $booking->hours }} hr{{ $booking->hours > 1 ? 's' : '' }}
                        @if ($booking->shoot_type) &middot; {{ $booking->shoot_type }} @endif
                        @if ($booking->location?->owner)
                            &middot; with {{ $booking->location->owner->name }}
                        @endif
                    </p>
                </div>
                <div class="text-left md:text-right shrink-0 flex flex-col gap-2 items-start md:items-end">
                    <p class="text-sm text-gray-400">{{ $booking->booking_date->format('M d, Y · g:i A') }}</p>
                    <p class="font-semibold text-indigo-900">PKR {{ number_format((float) $booking->total_price) }}</p>
                    @php
                        $badge = match ($booking->status->value) {
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-indigo-100 text-indigo-700',
                            'cancelled' => 'bg-red-100 text-red-600',
                        };
                    @endphp
                    <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $badge }}">
                        {{ ucfirst($booking->status->value) }}
                    </span>

                    @if ($booking->status->value === 'confirmed')
                        <a href="{{ route('customer.bookings.pay', $booking->id) }}"
                           class="action-btn shade text-center">
                            Pay with JazzCash
                        </a>
                    @endif
                </div>

                <div class="flex flex-col gap-2 shrink-0">
                    @if ($booking->status->value === 'pending')
                        <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}"
                              onsubmit="return confirm('Cancel this booking?')">
                            @csrf
                            <button type="submit" class="action-btn danger w-full">Cancel</button>
                        </form>
                    @endif

                    @if ($booking->status->value === 'completed')
                        <a href="{{ route('customer.bookings.review', $booking->id) }}"
                           class="action-btn shade text-center">
                            {{ $booking->has_review ? 'Edit Review' : 'Leave a Review' }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="card chart-transition rounded-2xl text-center py-10 text-gray-400">
                <i class="fa-solid fa-calendar text-2xl mb-2"></i>
                <p class="font-medium">No bookings yet</p>
                <a href="{{ route('customer.listings') }}"
                   class="text-sm text-indigo-700 hover:underline mt-2 inline-block">
                    Browse listings
                </a>
            </div>
        @endforelse
    </div>

</div>
</x-layouts.customer>