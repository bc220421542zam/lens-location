 {{-- SIDEBAR --}}
<aside class="w-64 shrink-0 bg-indigo-900 text-white flex flex-col p-4 gap-1">

    <a href="{{ route('admin.dashboard') }}"
        class="flex items-center gap-3 py-2 px-3 rounded-lg transition
        {{ request()->routeIs('admin.dashboard') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-house w-5 text-center"></i>
        Dashboard
    </a>

    <a href="{{ route('admin.users')}}"
        class="flex items-center gap-3 py-2 px-3 rounded-lg transition
        {{ request()->routeIs('admin.users') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-users w-5 text-center"></i>
        Users
    </a>

    <a href="{{ route('admin.listings') }}"
        class="flex items-center gap-3 py-2 px-3 rounded-lg transition
        {{ request()->routeIs('admin.listings') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-location-dot w-5 text-center"></i>
        Listings
    </a>

    <a href="{{ route('admin.profile') }}"
        class="flex items-center gap-3 py-2 px-3 rounded-lg transition
        {{ request()->routeIs('admin.profile') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
        <i class="fa-solid fa-user w-5 text-center"></i>
        Profile
    </a>

</aside>