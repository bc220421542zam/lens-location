{{-- MOBILE OVERLAY --}}
<div x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click.stop="sidebarOpen = false"
    class="fixed inset-0 bg-black/20 z-20 lg:hidden"
    style="display:none">
</div>

{{-- SIDEBAR --}}
<aside
    class="relative z-30 h-full shrink-0 bg-indigo-900 text-white
           flex flex-col p-2 gap-1 overflow-hidden
           w-16 lg:w-64
           transition-all duration-300 ease-in-out"
    :class="sidebarOpen ? 'w-64' : 'w-16 lg:w-64'">

    @php
        $links = [
            ['route' => 'customer.dashboard', 'icon' => 'fa-house',        'label' => 'Dashboard'],
            ['route' => 'customer.bookings',  'icon' => 'fa-calendar',     'label' => 'My Bookings'],
            ['route' => 'customer.listings',  'icon' => 'fa-location-dot', 'label' => 'Search Listings'],
            ['route' => 'customer.messages', 'icon' => 'fa-message', 'label' => 'Messages'],
            ['route' => 'customer.favorites', 'icon' => 'fa-heart',        'label' => 'Favorites'],
            ['route' => 'customer.profile',   'icon' => 'fa-user',         'label' => 'Profile'],
        ];
    @endphp

    @foreach ($links as $link)
        <a href="{{ route($link['route']) }}"
            class="flex items-center gap-3 py-2 px-2 rounded-lg transition-colors whitespace-nowrap
            {{ request()->routeIs($link['route'])
                ? 'bg-[#2C3399] text-white'
                : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
            <i class="fa-solid {{ $link['icon'] }} w-5 text-center shrink-0 text-base"></i>
            {{-- Desktop: always show label. Mobile: only show when sidebarOpen --}}
            <span class="text-sm font-medium hidden lg:inline">{{ $link['label'] }}</span>
            <span class="text-sm font-medium lg:hidden" x-show="sidebarOpen">{{ $link['label'] }}</span>
        </a>
    @endforeach
</aside>