{{-- Sidebar --}}
{{-- Desktop & Tablet: side rail | Mobile: bottom nav bar --}}
<aside class="
    {{-- Mobile: fixed bottom bar --}}
    fixed bottom-0 left-0 right-0 z-50 flex flex-row items-center justify-around
    bg-indigo-900 text-white px-2 py-2 h-16
    {{-- Tablet (md): left sidebar, icon-only rail --}}
    md:static md:flex-col md:justify-start md:items-stretch md:w-16 md:h-screen
    md:p-2 md:gap-1 md:shrink-0
    {{-- Desktop (lg): full sidebar with labels --}}
    lg:w-64 lg:p-4
">
    {{-- Owner Dashboard --}}
    <a href="{{ route('owner.dashboard') }}"
        class="flex items-center justify-center gap-0 py-2 px-2 rounded-lg transition
            md:flex-col md:gap-1 md:py-3
            lg:flex-row lg:justify-start lg:gap-3 lg:px-3 lg:py-2
            {{ request()->routeIs('owner.dashboard')
                ? 'bg-[#2C3399] text-white'
                : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-home w-5 text-center text-lg md:text-base"></i>
        <span class="text-[10px] leading-tight md:block lg:text-sm lg:text-base">
            <span class="block md:hidden lg:hidden">Dashboard</span>
            <span class="hidden md:block lg:hidden text-[9px]">Dashboard</span>
            <span class="hidden lg:inline">Owner Dashboard</span>
        </span>
    </a>


    {{-- My Listings --}}
    <a href="{{ route('owner.listings') }}"
        class="flex items-center justify-center gap-0 py-2 px-2 rounded-lg transition
            md:flex-col md:gap-1 md:py-3
            lg:flex-row lg:justify-start lg:gap-3 lg:px-3 lg:py-2
            {{ request()->routeIs('owner.listings')
                ? 'bg-[#2C3399] text-white'
                : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-location-dot w-5 text-center text-lg md:text-base"></i>
        <span class="text-[10px] leading-tight md:block lg:text-sm lg:text-base">
            <span class="block md:hidden lg:hidden">Listings</span>
            <span class="hidden md:block lg:hidden text-[9px]">Listings</span>
            <span class="hidden lg:inline">My Listings</span>
        </span>
    </a>

    {{-- Bookings --}}
    <a href="{{ route('owner.bookings') }}"
        class="flex items-center justify-center gap-0 py-2 px-2 rounded-lg transition
            md:flex-col md:gap-1 md:py-3
            lg:flex-row lg:justify-start lg:gap-3 lg:px-3 lg:py-2
            {{ request()->routeIs('owner.bookings')
                ? 'bg-[#2C3399] text-white'
                : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-calendar w-5 text-center text-lg md:text-base"></i>
        <span class="text-[10px] leading-tight md:block lg:text-sm lg:text-base">
            <span class="block md:hidden lg:hidden">Bookings</span>
            <span class="hidden md:block lg:hidden text-[9px]">Bookings</span>
            <span class="hidden lg:inline">Bookings</span>
        </span>
    </a>

    {{-- Profile --}}
    <a href="{{ route('owner.profile') }}"
        class="flex items-center justify-center gap-0 py-2 px-2 rounded-lg transition
            md:flex-col md:gap-1 md:py-3
            lg:flex-row lg:justify-start lg:gap-3 lg:px-3 lg:py-2
            {{ request()->routeIs('owner.profile')
                ? 'bg-[#2C3399] text-white'
                : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-user w-5 text-center text-lg md:text-base"></i>
        <span class="text-[10px] leading-tight md:block lg:text-sm lg:text-base">
            <span class="block md:hidden lg:hidden">Profile</span>
            <span class="hidden md:block lg:hidden text-[9px]">Profile</span>
            <span class="hidden lg:inline">Profile</span>
        </span>
    </a>
</aside>

{{-- Mobile bottom-nav spacer so page content isn't hidden behind the bar --}}
<div class="h-16 md:hidden"></div>