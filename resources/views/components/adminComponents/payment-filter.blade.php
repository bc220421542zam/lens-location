<form method="GET" action="{{ route('admin.payments') }}"
      class="flex flex-col md:flex-row md:items-center gap-3 card chart-transition mb-4 justify-between rounded-2xl border-l-3 border-indigo-400">

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
              placeholder="Search by ref, listing, customer ..."
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
        <select name="status" id="filter-status" class="input text-indigo-800">
            <option value="">All</option>
            <option value="paid"    @selected(request('status') === 'paid')>Paid</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="failed"  @selected(request('status') === 'failed')>Failed</option>
        </select>
    </div>

    {{-- Gateway --}}
    <div class="w-full md:w-48">
        <label for="filter-gateway" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                d="M3 10h14M10 3v14"/>
            </svg>
            Gateway
        </label>
        <select name="gateway" id="filter-gateway" class="input text-indigo-800">
            <option value="">All</option>
            <option value="jazzcash"  @selected(request('gateway') === 'jazzcash')>JazzCash</option>
            <option value="2checkout" @selected(request('gateway') === '2checkout')>2Checkout</option>
        </select>
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
        @if (request()->hasAny(['search', 'status', 'gateway']))
            <a href="{{ route('admin.payments') }}"
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