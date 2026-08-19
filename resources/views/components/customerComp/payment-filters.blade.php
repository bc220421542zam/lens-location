@if ($errors->any())
    <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<form method="GET" action="{{ route('customer.payments') }}"
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
              placeholder="Search by listing or ref ..."
              class="input text-indigo-800">
    </div>

    {{-- Min Amount --}}
    <div class="w-full md:w-32">
        <label for="filter-min-amount" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                d="M4 10h12M4 10l4-4M4 10l4 4"/>
            </svg>
            Min (Rs.)
        </label>
        <input type="number" name="min_amount" id="filter-min-amount" value="{{ request('min_amount') }}"
              placeholder="0" min="0"
              class="input text-indigo-800">
    </div>

    {{-- Max Amount --}}
    <div class="w-full md:w-32">
        <label for="filter-max-amount" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                d="M16 10H4M16 10l-4-4M16 10l-4 4"/>
            </svg>
            Max (Rs.)
        </label>
        <input type="number" name="max_amount" id="filter-max-amount" value="{{ request('max_amount') }}"
              placeholder="Any" min="0"
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
                <option value="paid"    @selected(request('status') === 'paid')>Paid</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="failed"  @selected(request('status') === 'failed')>Failed</option>
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
        @if (request()->hasAny(['search', 'status', 'min_amount', 'max_amount']))
            <a href="{{ route('customer.payments') }}"
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