{{-- TOP NAVBAR --}}
<nav class="shrink-0 bg-[#EEEFF7] shadow flex justify-between items-center px-4 md:px-6 py-3 z-10">
    
    {{-- Logo --}}
    <img src="/images/Logo.png" alt="Logo" class="h-8 md:h-10">

    <div class="flex items-center gap-3 md:gap-5">
        
        {{-- Bell icon (hidden on very small screens, visible from xs up) --}}
        <i class="icon fa-regular fa-bell text-base md:text-lg"></i>

        {{-- PROFILE DROPDOWN --}}
        <div class="relative" x-data="{ open: false }">
            
            <button @click="open = !open" class="flex items-center gap-2 hover:text-indigo-700">
                <i class="icon fa-regular fa-user text-base md:text-lg"></i>
                {{-- Hide name on mobile, show from md up --}}
                <span class="hidden md:inline text-sm text-indigo-900">{{ auth()->user()->name }}</span>
                {{-- Dropdown chevron for affordance on mobile --}}
                <i class="fa-solid fa-chevron-down text-xs text-indigo-900 md:hidden"></i>
            </button>

            {{-- Dropdown menu --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-indigo-100 py-2 z-50">

                <div class="px-4 py-2 border-b border-indigo-100">
                    <p class="text-sm font-semibold text-indigo-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#2C3399] truncate">{{ auth()->user()->email }}</p>
                </div>

                <div class="border-indigo-100 mt-1 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</nav>