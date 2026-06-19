{{--Filters--}}
<form method="GET" action="{{ route('admin.users') }}"
      class="flex flex-col md:flex-row md:items-center gap-3 card mb-4 justify-between rounded-2xl">
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
                  placeholder="Search by name, email ..."
                  class="input text-indigo-800">
    </div>
        {{-- Role --}}
    <div class="w-full md:w-48">
        <label for="filter-role" class="flex items-center gap-1.5 text-indigo-900 mb-1 text-sm">
            <svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                 d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 20c0-4 3.58-7 8-7s8 3 8 7"/>
            </svg>
            Role
        </label>
            <select name="role" id="filter-role" class="input text-indigo-800">
                <option value="">All</option>
                    <option value="admin"        @selected(request('role') === 'admin')>Admin</option>
                    <option value="owner"        @selected(request('role') === 'owner')>Owner</option>
                    <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            </select>
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
                    <option value="active"  @selected(request('status') === 'active')>Active</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                </select>

            </select>
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
        @if (request()->hasAny(['search', 'status', 'category']))
            <a href="{{ route('admin.users') }}"
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
 