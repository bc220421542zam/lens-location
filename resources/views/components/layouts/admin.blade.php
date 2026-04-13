<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#DDDEEF] flex flex-col h-screen overflow-hidden m-0 p-0">

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

    {{-- BOTTOM SECTION: sidebar + content --}}
    <div class="flex flex-1 overflow-hidden">

    {{-- SIDEBAR --}}
<aside class="w-64 shrink-0 bg-indigo-900 text-white flex flex-col p-4 gap-1">

    <a href="{{ route('dashboard') }}"
        class="flex items-center gap-3 py-2 px-3 rounded-lg transition
        {{ request()->routeIs('dashboard') ? 'bg-[#2C3399] text-white' : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
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

</aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>

    </div>

</body>
</html>