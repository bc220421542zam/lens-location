{{-- TOP NAVBAR --}}
<nav class="shrink-0 bg-[#EEEFF7] shadow flex justify-between items-center px-3 md:px-6 py-2.5 z-10">

    <div class="flex items-center gap-2 md:gap-3">
        {{-- HAMBURGER only on mobile devices --}}
        <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden text-indigo-900 hover:text-indigo-700 p-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        {{-- Logo --}}
        <img src="/images/Logo.png" alt="Logo" class="h-7 md:h-10">
    </div>

    <div class="flex items-center gap-1 md:gap-4">

        {{-- Preferences --}}
        <x-profileComponents.preferences/>

        {{-- PROFILE DROPDOWN --}}
        <div class="relative" x-data="{ open: false }">

            <button @click="open = !open"
                class="flex items-center gap-1.5 md:gap-2 p-1 md:p-1.5 rounded-lg transition-colors">

            {{-- Profile Photo --}}
                @if(auth()->user()->profile_picture)
                    <img src="{{ Storage::url(auth()->user()->profile_picture)}}" class="w-7 h-7 md:w-8 md:h-8 rounded-full object-cover border border-indigo-200 shrink-0" alt="Profile Picture">
                @else
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-user text-xs md:text-sm text-indigo-900"></i>
                    </div>
                @endif

            {{-- Name: hidden on small screens --}}
                <div class="hidden sm:flex flex-col items-center gap-0.5 leading-tight">
                    
                    <span class="text-xs font-medium text-white bg-indigo-500 rounded-lg px-3 py-1 capitalize mt-0.5">
                        {{ auth()->user()->role ?? 'User' }}
                    </span>
                </div>
            </button>

            {{-- DROPDOWN PANEL --}}
            <div x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.outside="open = false"
                class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-indigo-100 py-2 z-50">

                {{-- User Info --}}
                <div class="px-4 py-2.5">
                    <p class="text-sm font-semibold text-indigo-900 truncate">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </p>
                    <p class="text-xs text-[#2C3399] truncate mt-0.5">{{ auth()->user()->email }}</p>
                    {{-- USER ROLE BADGE --}}
                </div>

                <div class="border-t border-indigo-100 mt-1 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>