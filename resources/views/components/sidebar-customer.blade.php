 {{-- Sidebar --}}
        <aside class="w-64 shrink-0 bg-indigo-900 text-white flex flex-col p-4 gap-1">
            <a href="{{ route('customer.dashboard') }}" 
                class="flex items-center gap-3 py-2 px-3 rounded-lg transition 
                {{ request()->routeIs('customer.dashboard') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
                <i class="fa-solid fa-home w-5 text-center"></i>Dashboard</a>

            <a href="{{ route('customer.listings') }}" 
                class="flex items-center gap-3 py-2 px-3 rounded-lg transition 
                {{ request()->routeIs('customer.listings') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
                <i class="fa-solid fa-location-dot w-5 text-center"></i>Search Listings</a>
                
            <a href="{{ route('customer.bookings') }}" 
                class="flex items-center gap-3 py-2 px-3 rounded-lg transition 
                {{ request()->routeIs('customer.bookings') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
                <i class="fa-solid fa-calendar w-5 text-center"></i>My Bookings</a>
                
            <a href="{{ route('customer.profile') }}"
                class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                {{ request()->routeIs('customer.profile') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
                <i class="fa-solid fa-user w-5 text-center"></i>Profile</a>
        </aside>