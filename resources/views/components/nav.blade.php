{{-- TOP NAVBAR --}}
@php
    $userRole = auth()->user()->role?->value ?? '';
    $isAdmin  = $userRole === 'admin';
    $isOwner  = $userRole === 'owner';
    $isCustomer = $userRole === 'customer';

    if ($isAdmin) {
        $unreadRoute  = route('admin.notifications.unread');
        $readAllRoute = route('admin.notifications.read-all');
        $readRoute    = route('admin.notifications.read', ['id' => '__ID__']);
        $allRoute     = route('admin.notifications');
    } elseif ($isOwner) {
        $unreadRoute  = route('owner.notifications.unread');
        $readAllRoute = route('owner.notifications.read-all');
        $readRoute    = route('owner.notifications.read', ['id' => '__ID__']);
        $allRoute     = route('owner.notifications');
    } elseif ($isCustomer) {
        $unreadRoute  = route('customer.notifications.unread');
        $readAllRoute = route('customer.notifications.read-all');
        $readRoute    = route('customer.notifications.read', ['id' => '__ID__']);
        $allRoute     = route('customer.notifications');
    } else {
        $unreadRoute  = null;
        $readAllRoute = null;
        $readRoute    = null;
        $allRoute     = null;
    }
@endphp
<nav class="shrink-0 border border-indigo-300 bg-[#EEEFF7] shade flex justify-between items-center px-3 md:px-6 py-2.5 z-10 hover:shadow-lg transition-shadow duration-200">

    <div class="flex items-center gap-2 md:gap-3">
        {{-- Logo --}}
        <img src="/images/Logo.png" alt="Logo" class="h-7 md:h-10">

        {{-- HAMBURGER only on mobile devices --}}
        <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden text-indigo-900 hover:text-indigo-700 p-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
    </div>

    <div class="flex items-center gap-1 md:gap-4">

        {{-- Preferences --}}
        <x-profileComponents.preferences
            :unreadRoute="$unreadRoute"
            :readAllRoute="$readAllRoute"
            :readRoute="$readRoute"
            :allRoute="$allRoute"
        />

        {{-- PROFILE DROPDOWN --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center gap-2 p-1 md:p-1.5 rounded-lg hover:bg-indigo-100 transition-colors">

                @if(auth()->user()->profile_picture)
                    <img src="{{ Storage::url(auth()->user()->profile_picture) }}"
                         class="w-8 h-8 md:w-9 md:h-9 rounded-full object-cover border border-indigo-200 shrink-0"
                         alt="Profile Picture">
                @else
                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-user text-xs md:text-sm text-indigo-900"></i>
                    </div>
                @endif

                {{-- Name + Role, hidden on very small screens --}}
                <div class="hidden sm:flex flex-col items-start leading-tight">
                    <span class="text-sm font-semibold text-indigo-900 truncate max-w-[140px]">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </span>
                    <span class="text-xs text-indigo-500 capitalize truncate max-w-[140px]">
                        {{ $userRole ?: 'User' }}
                    </span>
                </div>

                <i class="fa-solid fa-chevron-down text-[10px] text-indigo-500 shrink-0 transition-transform"
                   :class="open ? 'rotate-180' : ''"></i>
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
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-indigo-100 py-2 z-50"
                style="display:none;">

                {{-- User Info --}}
                <div class="px-4 py-2.5 flex items-center gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-indigo-900 truncate">
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        </p>
                        <p class="text-xs text-[#2C3399] truncate mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="px-4 pb-2">
                    <span class="inline-flex items-center text-[11px] font-medium text-indigo-700 capitalize">
                        {{ $userRole ?: 'User' }}
                    </span>
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