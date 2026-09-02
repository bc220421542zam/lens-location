<x-layouts.admin>
<div>
    {{-- Top bar --}}
    <x-topbar
        title="Bookings"
        description="All bookings across the platform">
    </x-topbar>

    <x-success class="mb-4" />
    <x-error class="mb-4" />

    {{-- Stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-5 gap-3 md:gap-4 mb-4 md:mb-6">
        @php
            $statCards = [
                ['label' => 'Total Bookings', 'value' => $stats['total'],     'icon' => 'fa-calendar',        'border' => 'border-indigo-800', 'color' => 'text-indigo-800'],
                ['label' => 'Pending',        'value' => $stats['pending'],   'icon' => 'fa-clock',           'border' => 'border-yellow-600/60', 'color' => 'text-yellow-600'],
                ['label' => 'Confirmed',      'value' => $stats['confirmed'], 'icon' => 'fa-circle-check',    'border' => 'border-green-700/60', 'color' => 'text-green-700'],
                ['label' => 'Completed',      'value' => $stats['completed'], 'icon' => 'fa-flag-checkered',  'border' => 'border-indigo-700/60', 'color' => 'text-indigo-700'],
                ['label' => 'Cancelled',      'value' => $stats['cancelled'], 'icon' => 'fa-ban',             'border' => 'border-red-500/60', 'color' => 'text-red-600'],
            ];
        @endphp

        @foreach ($statCards as $card)
        <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 {{ $card['border'] }}">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">{{ $card['label'] }}</h3>
                <div class="w-8 h-8 flex items-center justify-center rounded-full {{ $card['border'] }} {{ $card['color'] }}">
                    <i class="fa-solid {{ $card['icon'] }} text-sm"></i>
                </div>
            </div>
            <p class="text-xl md:text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    @if ($errors->any())
        <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.bookings') }}"
          class="flex flex-col md:flex-row md:items-end gap-3 card chart-transition mb-4 justify-between rounded-2xl border-l-3 border-indigo-400">

        {{-- Search --}}
        <div class="flex-1">
            <label for="filter-search" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M17 17l-3.5-3.5M13 8.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                </svg>
                Search
            </label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by customer, listing ..."
                   class="input text-indigo-800">
        </div>

        {{-- Status --}}
        <div class="w-full md:w-48">
            <label for="filter-status" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M10 5v5l3 3M10 3a7 7 0 100 14A7 7 0 0010 3z"/>
                </svg>
                Status
            </label>
            <div class="relative">
                <select name="status" id="filter-status" class="input text-indigo-800 w-full appearance-none pr-8">
                    <option value="">All</option>
                    <option value="pending"   @selected(request('status') === 'pending')>Pending</option>
                    <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    <option value="expired"   @selected(request('status') === 'expired')>Expired</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2 items-end">
            <button type="submit" class="btn w-auto px-4 mt-5 rounded-lg inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    d="M3 5h14M6 10h8M9 15h2"/>
                </svg>
                Filter
            </button>
        </div>

        <div class="flex items-end">
            @if (request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.bookings') }}"
                   class="px-4 w-auto mt-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        d="M5 5l10 10M15 5L5 15"/>
                    </svg>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Bookings Table --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Bookings</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">

                <table class="table-clean w-full text-left min-w-[900px]">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                            <th class="py-3 px-3 font-medium">Sr. No.</th>
                            <th class="px-2 font-medium">Booking</th>
                            <th class="px-2 font-medium">Customer</th>
                            <th class="px-2 font-medium">Listing</th>
                            <th class="px-2 font-medium">Owner</th>
                            <th class="px-2 font-medium">Date</th>
                            <th class="px-2 font-medium text-right">Total</th>
                            <th class="px-2 font-medium">Status</th>
                            <th class="px-2 font-medium text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                            <td class="py-3 px-4 text-sm text-indigo-700">
                                {{ $bookings->firstItem() + $loop->index }}
                            </td>
                            <td class="px-2 text-sm text-indigo-700">#{{ $booking->id }}</td>
                            <td class="px-2 text-sm text-indigo-700">
                                @if($booking->customer)
                                    <a href="{{ route('admin.users.detail', $booking->customer->id) }}"
                                       class="hover:text-indigo-900 hover:underline">{{ $booking->customer->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-2 text-sm font-medium text-indigo-900">{{ $booking->location->title ?? '—' }}</td>
                            <td class="px-2 text-sm text-indigo-700">
                                @if($booking->location?->owner)
                                    <a href="{{ route('admin.users.detail', $booking->location->owner->id) }}"
                                       class="hover:text-indigo-900 hover:underline">{{ $booking->location->owner->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-2 text-sm text-indigo-700">{{ $booking->booking_date->format('M d, Y · g:i A') }}</td>
                            <td class="px-2 text-sm tabular-nums text-indigo-700 text-right">PKR {{ number_format((float) $booking->total_price) }}</td>
                            <td class="px-2 text-sm">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $booking->status->badgeClasses() }}">
                                    {{ $booking->status->label() }}
                                </span>
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex items-center justify-center">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                       title="View"
                                       class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-indigo-400 text-sm">No bookings found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

        <div class="mt-4">{{ $bookings->appends(request()->query())->links() }}</div>

    </div>
</div>
</x-layouts.admin>
