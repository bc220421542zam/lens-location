<x-layouts.admin>
@php
    $commissionPct = (int) round(config('services.stripe.commission_rate') * 100);
    $ownerPct = 100 - $commissionPct;

    // Initial state handed to the registered Alpine component (ledgerPage).
    // @json escapes single quotes and angle brackets, so the single-quoted
    // x-data attribute below can never be terminated early by the data.
    $ledgerInit = [
        'period'  => $period,
        'filters' => [
            'search' => (string) request('search'),
            'from'   => (string) request('from'),
            'to'     => (string) request('to'),
            'status' => (string) request('status'),
        ],
        'summary' => $summary,
        'count'   => $transactions->total(),
        'baseUrl' => route('admin.ledger'),
    ];
@endphp

<div x-data='ledgerPage(@json($ledgerInit))'
     @click="onPanelClick($event)">

    {{-- Top bar --}}
    <x-topbar
        title="Booking & Payment Dashboard"
        description="See all bookings, payments and owner payouts in one place">
    </x-topbar>

    <x-success class="mb-4" />
    <x-error class="mb-4" />

    {{-- AJAX error banner (validation failures, network errors). --}}
    <div x-show="error" x-cloak x-text="error"
         class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700"></div>

    {{--stats --}}
    <x-adminComponents.ledger-states :summary="$summary" :commissionPct="$commissionPct" :ownerPct="$ownerPct" />

    {{-- FILTERS: search, date range, status and period toggle in one row.
         Plain GET form (works without JS); with JS, @submit.prevent routes
         every change through the AJAX refresh instead. --}}
    <form method="GET" action="{{ route('admin.ledger') }}"
          class="flex flex-col md:flex-row md:items-end gap-3 card chart-transition mb-4 justify-between rounded-2xl border-l-3 border-indigo-400"
          @submit.prevent="refresh()">

        {{-- Search --}}
        <div class="flex-1">
            <label for="ledger-search" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M17 17l-3.5-3.5M13 8.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                </svg>
                Search
            </label>
            <input type="text" id="ledger-search" name="search" value="{{ request('search') }}"
                   placeholder="Search by customer name or booking ID"
                   x-model="filters.search" @input.debounce.400ms="refresh()"
                   class="input text-indigo-800">
        </div>

        {{-- Date range --}}
        <div class="w-full md:w-auto">
            <label class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M3 6h14v11H3zM2 9h16M6 3v3M13 3v3"/>
                </svg>
                Date range
            </label>
            <div class="flex items-center gap-2 rounded-lg border border-indigo-200 bg-white shadow-md px-3 py-2">
                <input type="date" name="from" value="{{ request('from') }}" aria-label="From date"
                       x-model="filters.from" @change="refresh()"
                       class="text-sm text-indigo-800 focus:outline-none">
                <span class="text-xs text-indigo-300" aria-hidden="true">&ndash;</span>
                <input type="date" name="to" value="{{ request('to') }}" aria-label="To date"
                       x-model="filters.to" @change="refresh()"
                       class="text-sm text-indigo-800 focus:outline-none">
            </div>
        </div>

        {{-- Status --}}
        <div class="w-full md:w-48">
            <label for="ledger-status" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    d="M10 5v5l3 3M10 3a7 7 0 100 14A7 7 0 0010 3z"/>
                </svg>
                Status
            </label>
            <div class="relative">
                <select name="status" id="ledger-status"
                        x-model="filters.status" @change="refresh()"
                        class="input text-indigo-800 w-full appearance-none pr-8">
                    <option value="">All statuses</option>
                    <option value="paid"     @selected(request('status') === 'paid')>Received</option>
                    <option value="pending"  @selected(request('status') === 'pending')>Pending</option>
                    <option value="failed"   @selected(request('status') === 'failed')>Failed</option>
                    <option value="refunded" @selected(request('status') === 'refunded')>Refunded</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Period toggle --}}
        <div class="flex items-end gap-1">
            <button type="submit" name="period" value="weekly"
                    @click="period = 'weekly'"
                    :class="period === 'weekly' ? 'bg-indigo-900 text-white' : 'text-indigo-700 hover:bg-indigo-100'"
                    class="btn-transition rounded-lg px-3.5 py-2 text-sm font-medium">Weekly</button>
            <button type="submit" name="period" value="monthly"
                    @click="period = 'monthly'"
                    :class="period === 'monthly' ? 'bg-indigo-900 text-white' : 'text-indigo-700 hover:bg-indigo-100'"
                    class="btn-transition rounded-lg px-3.5 py-2 text-sm font-medium">Monthly</button>
        </div>

        <div class="flex items-end">
            <a href="{{ route('admin.ledger', ['period' => $period]) }}" x-show="hasFilters" x-cloak
               @click.prevent="resetFilters()"
               class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    d="M5 5l10 10M15 5L5 15"/>
                </svg>
                Reset
            </a>
        </div>
    </form>

    {{-- BOOKINGS & PAYMENTS table --}}
    <section class="relative mb-4 overflow-hidden shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400"
             :class="{ 'pointer-events-none opacity-50': loading }">

        {{-- Loading overlay while an AJAX refresh is in flight. --}}
        <div x-show="loading" x-cloak x-transition.opacity
             class="absolute inset-0 z-10 flex items-center justify-center bg-white/50">
            <i class="fa-solid fa-circle-notch fa-spin text-xl text-indigo-900" aria-hidden="true"></i>
        </div>

        <div class="flex flex-col gap-3 mb-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-bold text-indigo-900 text-lg">Bookings &amp; payments</h2>
                <p class="text-xs text-indigo-400" x-text="countLabel">{{ $transactions->total() }} {{ Str::plural('booking', $transactions->total()) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">
                <table class="table-clean w-full min-w-[960px] text-left text-sm">
                    <thead>
                        <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                            <th class="py-3 px-2 font-medium whitespace-nowrap">Booking ID</th>
                            <th class="px-2 font-medium whitespace-nowrap">Customer</th>
                            <th class="px-2 font-medium whitespace-nowrap">Booking date</th>
                            <th class="px-2 font-medium whitespace-nowrap text-right">Amount paid</th>
                            <th class="px-2 font-medium whitespace-nowrap">Payment status</th>
                            <th class="px-2 font-medium whitespace-nowrap">Received on</th>
                            <th class="px-2 font-medium whitespace-nowrap text-right">Days held</th>
                            <th class="px-2 font-medium leading-snug text-center">
                                <span class="block">Commission</span>
                                <span class="block text-[10px] font-normal text-indigo-400">{{ $commissionPct }}%</span>
                            </th>
                            <th class="px-2 font-medium leading-snug text-center">
                                <span class="block">Owner Share</span>
                                <span class="block text-[10px] font-normal text-indigo-400">{{ $ownerPct }}%</span>
                            </th>
                            <th class="px-2 font-medium whitespace-nowrap">Transfer status</th>
                            <th class="px-2 font-medium whitespace-nowrap">Transferred on</th>
                        </tr>
                    </thead>
                    @include('admin.ledger.partials.table')
                </table>
            </div>
        </div>

        @include('admin.ledger.partials.pagination')
    </section>

    {{-- REPORT SECTION: Chart.js bar chart left, period totals + export right. --}}
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-[3fr_2fr]">
        <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
            <h3 class="font-bold text-indigo-900 text-lg">Commission &amp; payout trend</h3>
            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-indigo-400">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                <span id="ledgerTrendSubtitle">{{ $trendSubtitle }}</span>
            </p>

            <div class="relative mt-4 h-56">
                <canvas id="ledgerTrendChart" role="img" aria-label="Commission and payout trend chart"></canvas>
            </div>

            <div class="mt-3 flex items-center justify-center gap-6 text-xs text-indigo-900">
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-[#F5A623]" aria-hidden="true"></span>
                    Platform commission
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-[#34C17B]" aria-hidden="true"></span>
                    Owner payout
                </span>
            </div>
        </div>

        @include('admin.ledger.partials.report')
    </section>
</div>
{{-- Alpine component ledgerPage is registered in resources/js/admin/ledger.js
    and bootstrapped in resources/js/admin.js. --}}
    <x-adminComponents.ledger-script :trend="$trend" />
</x-layouts.admin>
