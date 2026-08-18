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
    {{-- BOOKINGS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 items-stretch">
        @forelse ($bookings as $booking)
            @php
                $status = $booking->status->value;
                $badge  = match ($status) {
                    'pending'   => 'bg-yellow-100 text-yellow-700',
                    'confirmed' => 'bg-green-100 text-green-700',
                    'completed' => 'bg-indigo-100 text-indigo-700',
                    'cancelled' => 'bg-red-100 text-red-600',
                };
                $canPay    = $status === 'confirmed';
                $canCancel = $status === 'pending';
                $canReview = $status === 'completed';
            @endphp

            {{-- h-full keeps every tile in a row the same height --}}
            <div class="card chart-transition rounded-2xl border border-l-3 border-indigo-400
                        h-full flex flex-col gap-3">

                {{-- COVER --}}
                <div class="w-full h-40 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                    @if ($booking->location?->image)
                        <img src="{{ asset('storage/'.$booking->location->image) }}"
                             alt="" loading="lazy" decoding="async"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fa-solid fa-camera text-3xl text-gray-300"></i>
                        </div>
                    @endif
                </div>

                {{-- LOCATION --}}
                <div class="min-w-0">
                    <p class="font-semibold text-indigo-900 truncate"
                       title="{{ $booking->location?->title }}">
                        {{ $booking->location?->title ?? 'Deleted location' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                        {{ $booking->hours }} hr{{ $booking->hours > 1 ? 's' : '' }}
                        @if ($booking->shoot_type) &middot; {{ $booking->shoot_type }} @endif
                        @if ($booking->location?->owner)
                            &middot; with {{ $booking->location->owner->name }}
                        @endif
                    </p>
                </div>

                {{-- WHEN, PRICE, STATUS --}}
                <div class="flex flex-col gap-1">
                    <p class="text-sm text-gray-400">
                        {{ $booking->booking_date->format('M d, Y · g:i A') }}
                    </p>
                    <p class="font-semibold text-indigo-900">
                        PKR {{ number_format((float) $booking->total_price) }}
                    </p>
                    <span class="w-fit text-xs font-semibold px-2.5 py-1 rounded-full {{ $badge }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                {{-- ACTIONS — mt-auto pins them to the bottom so buttons line up across a row --}}
                @if ($canPay || $canCancel || $canReview)
                    <div class="mt-auto pt-1 flex flex-col gap-2">
                        @if ($canPay)
                            <a href="{{ route('customer.bookings.pay', $booking->id) }}"
                               class="action-btn shade grow-0 basis-auto w-full text-center whitespace-nowrap">
                                Pay with JazzCash
                            </a>
                        @endif

                        @if ($canCancel)
                            <form method="POST" action="{{ route('customer.bookings.cancel', $booking->id) }}"
                                  class="w-full"
                                  onsubmit="return confirm('Cancel this booking?')">
                                @csrf
                                <button type="submit"
                                        class="action-btn danger grow-0 basis-auto w-full whitespace-nowrap">
                                    Cancel
                                </button>
                            </form>
                        @endif

                        @if ($canReview)
                            <a href="{{ route('customer.bookings.review', $booking->id) }}"
                               class="action-btn shade grow-0 basis-auto w-full text-center whitespace-nowrap">
                                {{ $booking->has_review ? 'Edit Review' : 'Leave a Review' }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="card chart-transition rounded-2xl text-center py-10 text-gray-400
                        sm:col-span-2 xl:col-span-3">
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
