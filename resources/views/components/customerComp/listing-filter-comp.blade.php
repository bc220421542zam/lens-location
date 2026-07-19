{{-- FILTERS --}}
    @if ($errors->any())
        <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="GET" action="{{ route('customer.listings') }}"
      class="flex flex-col md:flex-row md:items-center gap-3 card mb-4 justify-between rounded-2xl border border-l-3 border-indigo-400 chart-transition">
        {{--Search--}}
    <div class="flex-1">
        <label for="filter-search"
            class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                d="M17 17l-3.5-3.5M13 8.5a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
            </svg>    
            Search
        </label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Title, city, description ..."
                   class="input text-indigo-800">
    </div>

        {{-- Category --}}
    <div class="w-full md:w-48">
        <label for="filter-category"
            class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                d="M4 6h12M4 10h8M4 14h5"/>
            </svg>    
            Category
        </label>
            <select name="category" class="input text-indigo-800 pr-3">
                <option value="" class="text-indigo-900">All</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat) class="text-indigo-900">{{ $cat }}</option>
                    @endforeach
            </select>
    </div>

        {{--price/hr--}}
    <div class="w-full md:w-40">
        <label for="filter-max-price" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <line x1="10" y1="3" x2="10" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                d="M13 6.5C13 5.12 11.66 4 10 4S7 5.12 7 6.5 8.34 9 10 9s3 1.12 3 2.5S11.66 15 10 15s-3-1.12-3-2.5"/>
            </svg>
            Max price/hr
        </label>
            <input type="number" name="max_price" min="0" step="100"
                value="{{ request('max_price') }}"
                class="input text-indigo-800">
    </div>

        {{--filter buttons--}}
    <div class="flex gap-2 items-end">
        <button type="submit" class="btn w-auto px-4 mt-5 rounded-lg inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                d="M3 5h14M6 10h8M9 15h2"/>
            </svg>    
                Filter
        </button>             
    </div>
        {{-- Reset button --}}
    <div class="flex items-end">
        @if (request()->hasAny(['search', 'category', 'max_price']))
            <a href="{{ route('customer.listings') }}"
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