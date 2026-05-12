<x-layouts.photographer>
<div class="page">

    {{-- TOP BAR --}}
    <x-topbar 
        title="Dashboard"
        description="welcome back, {{ auth()->user()->first_name }}">
        <x-slot:actions>
        <a href="{{ route('photographer.listings') }}"
           class="btn w-full sm:w-auto px-4 text-center">
            Find Location
        </a>
    </x-slot:actions>
    </x-topbar>
    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="label">Total Bookings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="card">
            <p class="label">Upcoming</p>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['upcoming'] }}</p>
        </div>
        <div class="card">
            <p class="label">Completed</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="card">
            <p class="label">Total Spent</p>
            <p class="text-2xl font-semibold text-indigo-800 mt-1">
                PKR {{ number_format((float) $stats['spent']) }}
            </p>
        </div>
    </div>

    {{-- UPCOMING BOOKINGS --}}
    <div class="flex flex-col gap-3">
        <h2 class="text-indigo-900 font-semibold">Upcoming bookings</h2>

        @forelse ($upcoming as $booking)
            <div class="card flex flex-col md:flex-row md:items-center gap-4">
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
                    <p class="font-semibold text-gray-900">{{ $booking->location?->title }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $booking->hours }} hr{{ $booking->hours > 1 ? 's' : '' }}
                        @if ($booking->shoot_type) &middot; {{ $booking->shoot_type }} @endif
                    </p>
                </div>
                <div class="text-left md:text-right shrink-0">
                    <p class="text-sm text-gray-400">{{ $booking->booking_date->format('M d, Y · g:i A') }}</p>
                    <p class="font-semibold text-gray-900">PKR {{ number_format((float) $booking->total_price) }}</p>
                    @php
                        $badge = $booking->status->value === 'confirmed'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-yellow-100 text-yellow-700';
                    @endphp
                    <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $badge }}">
                        {{ ucfirst($booking->status->value) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="card text-center py-10 text-gray-400">
                <i class="fa-solid fa-calendar text-2xl mb-2"></i>
                <p class="font-medium">No upcoming bookings</p>
                <a href="{{ route('photographer.listings') }}"
                   class="text-sm text-indigo-700 hover:underline mt-2 inline-block">
                    Find a location to book
                </a>
            </div>
        @endforelse
    </div>

</div>
</x-layouts.photographer>
