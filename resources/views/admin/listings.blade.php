<x-layouts.admin>
<div>
    {{--Top Bar--}}
    <x-topbar 
        title="Listings Management"
        description="Manage all listings">
    </x-topbar>

    {{-- FILTERS --}}
    <x-adminComponents.filters-listings :categories="$categories" />

    {{-- LISTINGS TABLE --}}
    {{-- no `chart-transition` here: its hover:scale transform would make this card the containing
         block for the fixed-positioned row action menus and clip them inside the table --}}
    <div class="shade card bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <x-success class="mb-4" />
        <x-error class="mb-4" />

        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Listings</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="table-clean w-full text-left min-w-[760px]">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                                <th class="py-3 px-4 font-medium">Sr. No.</th>

                                @php
                                    $currentSort = request('sort');
                                    $currentDirection = request('direction', 'asc');

                                    $sortLink = function($column) use ($currentSort, $currentDirection) {
                                        $nextDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                                        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection]);
                                    };

                                    $sortIcon = function($column) use ($currentSort, $currentDirection) {
                                        if ($currentSort === $column) {
                                            return '<i class="fa-solid fa-sort-'.($currentDirection === 'asc' ? 'up' : 'down').' text-[10px]"></i>';
                                        }
                                        return '<i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>';
                                    };
                                @endphp

                                <th class="px-2 font-medium">
                                    <a href="{{ $sortLink('title') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                        Title {!! $sortIcon('title') !!}
                                    </a>
                                </th>

                                <th class="px-2 font-medium">
                                    <a href="{{ $sortLink('city') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                        City {!! $sortIcon('city') !!}
                                    </a>
                                </th>

                                <th class="px-2 font-medium">
                                    <a href="{{ $sortLink('category') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                        Category {!! $sortIcon('category') !!}
                                    </a>
                                </th>

                                <th class="px-2 font-medium">Owner</th>

                                <th class="px-2 font-medium text-right">
                                    <a href="{{ $sortLink('price_per_hour') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                        Price/hr {!! $sortIcon('price_per_hour') !!}
                                    </a>
                                </th>

                                <th class="px-2 font-medium">
                                    <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                                        Status {!! $sortIcon('status') !!}
                                    </a>
                                </th>

                                <th class="px-2 font-medium text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listings as $listing)
                            <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                                <td class="py-3 px-4 text-sm text-indigo-700">
                                    {{ $listings->firstItem() + $loop->index }}
                                </td>
                                <td class="px-2 py-3 text-sm font-medium text-indigo-900 max-w-[220px]">
                                    <span class="block truncate" title="{{ $listing->title }}">
                                        {{ $listing->title }}
                                    </span>
                                </td>
                                <td class="px-2 text-sm text-indigo-700">{{ $listing->city ?? 'N/A' }}</td>
                                <td class="px-2 text-sm text-indigo-700">{{ $listing->category ?? 'N/A' }}</td>
                                <td class="px-2 text-sm text-indigo-700">
                                    {{ $listing->owner->first_name ?? 'N/A' }} {{ $listing->owner->last_name ?? '' }}
                                </td>
                                <td class="px-2 text-sm tabular-nums text-indigo-700 text-right">Rs. {{ $listing->price_per_hour }}</td>
                                <td class="px-2 text-sm">
                                    @php
                                        $statusValue = $listing->status->value;
                                        $badgeClasses = match($statusValue) {
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-600',
                                            default => 'bg-yellow-100 text-yellow-700',
                                        };
                                        $dotClasses = match($statusValue) {
                                            'approved' => 'bg-green-600',
                                            'rejected' => 'bg-red-600',
                                            default => 'bg-yellow-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
                                        {{ ucfirst($statusValue) }}
                                    </span>
                                </td>
                                <td class="px-2 py-3">
                                    {{-- KEBAB (three-dot) OVERFLOW MENU --}}
                                    <div class="flex items-center justify-center"
                                         x-data="{
                                            open: false,
                                            top: 0,
                                            left: 0,
                                            toggle() {
                                                if (this.open) { this.close(); return; }
                                                // place it from the button's on-screen box before showing,
                                                // then refine once the menu has real dimensions
                                                const btn = this.$refs.btn.getBoundingClientRect();
                                                this.top  = btn.bottom + 6;
                                                this.left = Math.max(8, btn.right - 176);
                                                this.open = true;
                                                this.$nextTick(() => {
                                                    const menu = this.$refs.menu;
                                                    if (!menu) return;
                                                    const box = this.$refs.btn.getBoundingClientRect();
                                                    let top = box.bottom + 6;
                                                    if (top + menu.offsetHeight > window.innerHeight - 8) {
                                                        top = Math.max(8, box.top - menu.offsetHeight - 6);
                                                    }
                                                    this.top  = top;
                                                    this.left = Math.max(8, box.right - menu.offsetWidth);
                                                });
                                            },
                                            close() { this.open = false; }
                                         }"
                                         @keydown.escape.window="close()"
                                         @scroll.window.capture="close()"
                                         @resize.window="close()">

                                        <button type="button"
                                            x-ref="btn"
                                            @click="toggle()"
                                            title="Actions"
                                            aria-haspopup="menu"
                                            :aria-expanded="open"
                                            class="w-8 h-8 flex items-center justify-center rounded-full text-indigo-700 hover:bg-indigo-100 transition"
                                            :class="open ? 'bg-indigo-100' : 'bg-indigo-50'">
                                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                        </button>

                                        <div x-ref="menu"
                                             x-show="open"
                                             x-cloak
                                             role="menu"
                                             @click.outside="close()"
                                             :style="`top:${top}px; left:${left}px`"
                                             class="fixed z-50 w-44 py-1 bg-white rounded-xl shadow-lg border border-indigo-100">

                                            {{-- VIEW --}}
                                            <a href="{{ route('admin.listings.show', $listing->id) }}"
                                               role="menuitem"
                                               class="flex items-center gap-2 w-full px-3 py-2 text-sm text-indigo-700 hover:bg-indigo-50 transition">
                                                <i class="fa-regular fa-eye text-xs w-4 text-center"></i>
                                                View details
                                            </a>

                                            {{-- APPROVE: shown only when not already approved and not rejected (rejected is terminal) --}}
                                            @if($listing->status->value === 'pending')
                                                <form action="{{ route('admin.listings.approve', $listing->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" role="menuitem"
                                                        class="flex items-center gap-2 w-full px-3 py-2 text-sm text-green-700 hover:bg-green-50 transition">
                                                        <i class="fa-solid fa-check text-xs w-4 text-center"></i>
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- REJECT: shown for pending and approved (rejected is terminal, no button) --}}
                                            @if(in_array($listing->status->value, ['pending', 'approved']))
                                                <form action="{{ route('admin.listings.reject', $listing->id) }}" method="POST"
                                                    onsubmit="return confirm('Reject this listing? This cannot be undone.')">
                                                    @csrf
                                                    <button type="submit" role="menuitem"
                                                        class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                                        <i class="fa-solid fa-xmark text-xs w-4 text-center"></i>
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif

                                            <div class="my-1 border-t border-indigo-50"></div>

                                            {{-- DELETE --}}
                                            <form method="POST" action="{{ route('admin.listings.delete', $listing->id) }}"
                                                onsubmit="return confirm('Delete this listing?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" role="menuitem"
                                                    class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                                    <i class="fa-regular fa-trash-can text-xs w-4 text-center"></i>
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-indigo-400 text-sm">No listings found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">{{ $listings->appends(request()->query())->links() }}</div>

    </div>

</div>
</x-layouts.admin>