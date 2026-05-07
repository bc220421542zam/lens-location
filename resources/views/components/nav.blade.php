  {{-- TOP NAVBAR --}}
<nav class="shrink-0 bg-[#EEEFF7] shadow flex justify-between items-center px-6 py-3 z-10">
    <img src="/images/Logo.png" alt="Logo" class="h-10">

    <div class="flex items-center gap-5">
        <i class="icon fa-regular fa-bell"></i>

        {{-- PROFILE DROPDOWN --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 hover:text-indigo-700">
                <i class="icon fa-regular fa-user"></i>
                <span class="text-sm text-indigo-900">{{ auth()->user()->name }}</span>
            </button>
        {{-- Dropdown menu --}}
            <div x-show="open" @click.outside="open = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-indigo-100 py-2 z-50">

                <div class="px-4 py-2 border-b border-indigo-100">
                    <p class="text-sm font-semibold text-indigo-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#2C3399]">{{ auth()->user()->email }}</p>
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