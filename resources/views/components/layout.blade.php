@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/Lenslocation-Logo.png') }}">
    <title>{{ $title ? $title.' · '.config('app.name') : config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-[#DDDEEF]">

    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-indigo-900 focus:text-white">
        Skip to main content
    </a>

    <header class="sticky top-0 z-40">
        <nav x-data="{ open: false }"
             @keydown.escape.window="open = false"
             class="relative bg-[#EEEFF7]/95 backdrop-blur border-b border-indigo-200 shade
                    flex items-center justify-between gap-3 px-3 md:px-6 py-2.5">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="shrink-0" aria-label="LensLocation home">
                <img src="{{ asset('images/Logo.png') }}" alt="LensLocation" class="h-8 sm:h-10">
            </a>

            {{-- Desktop: site links + auth actions, all on the right --}}
            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('home') }}"
                   @if (request()->routeIs('home')) aria-current="page" @endif
                   class="text-sm font-medium px-3 py-1.5 text-indigo-900 hover:text-indigo-700 transition-colors duration-200">
                    Home
                </a>
                <a href="{{ route('about') }}"
                   @if (request()->routeIs('about')) aria-current="page" @endif
                   class="text-sm font-medium px-3 py-1.5 text-indigo-900 hover:text-indigo-700 transition-colors duration-200">
                    About
                </a>
                @guest
                    <a href="{{ route('login') }}"
                       @if (request()->routeIs('login')) aria-current="page" @endif
                       class="text-sm font-medium px-3 py-1.5 text-indigo-900 hover:text-indigo-700 transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       @if (request()->routeIs('register')) aria-current="page" @endif
                       class="text-sm px-4 py-2 rounded-lg bg-indigo-900 text-white shadow-sm
                              hover:bg-indigo-700 transition-colors duration-200">
                        Register
                    </a>
                @endguest

                @auth
                    {{-- Browsing is an account feature: guests get the search
                         preview, logged-in customers the full browse page. --}}
                    @if (auth()->user()->role === \App\Enums\Role::Customer)
                        <a href="{{ route('customer.listings') }}"
                           @if (request()->routeIs('customer.listings*')) aria-current="page" @endif
                           class="text-sm font-medium px-3 py-1.5 text-indigo-900 hover:text-indigo-700 transition-colors duration-200">
                            Browse Listings
                        </a>
                    @endif

                    <a href="{{ route(auth()->user()->role->dashboardRoute()) }}"
                       class="text-sm px-4 py-2 rounded-lg bg-indigo-900 text-white shadow-sm
                              hover:bg-indigo-700 transition-colors duration-200">
                        <i class="fa-solid fa-gauge mr-1.5" aria-hidden="true"></i>Dashboard
                    </a>
                @endauth
            </div>

            {{-- Mobile menu toggle --}}
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open ? 'true' : 'false'"
                    aria-controls="site-mobile-menu"
                    aria-label="Toggle navigation menu"
                    class="md:hidden text-indigo-900 hover:text-indigo-700 p-1.5 rounded-lg hover:bg-indigo-100 transition-colors duration-200">
                <i class="fa-solid text-lg" :class="{ 'fa-bars': ! open, 'fa-xmark': open }"></i>
            </button>

            {{-- Mobile menu panel --}}
            <div id="site-mobile-menu"
                 x-cloak
                 x-show="open"
                 x-transition.opacity.duration.200ms
                 @click.outside="open = false"
                 class="absolute inset-x-0 top-full md:hidden flex flex-col gap-2 p-4
                        bg-white border-b border-indigo-200 shadow-xl">
                <a href="{{ route('home') }}"
                   class="px-4 py-2.5 font-medium text-indigo-900 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors duration-200">Home</a>
                <a href="{{ route('about') }}"
                   class="px-4 py-2.5 font-medium text-indigo-900 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors duration-200">About</a>

                @guest
                    <a href="{{ route('login') }}"
                       class="px-4 py-2.5 font-medium text-indigo-900 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors duration-200">Login</a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-indigo-900 text-white px-4 py-2.5 hover:bg-indigo-700 transition-colors duration-200">Register</a>
                @endguest

                @auth
                    @if (auth()->user()->role === \App\Enums\Role::Customer)
                        <a href="{{ route('customer.listings') }}"
                           class="px-4 py-2.5 font-medium text-indigo-900 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors duration-200">Browse Listings</a>
                    @endif

                    <a href="{{ route(auth()->user()->role->dashboardRoute()) }}"
                       class="rounded-lg bg-indigo-900 text-white px-4 py-2.5 hover:bg-indigo-700 transition-colors duration-200">
                        <i class="fa-solid fa-gauge mr-1.5" aria-hidden="true"></i>Dashboard
                    </a>
                @endauth
            </div>

        </nav>
    </header>

    <main id="main" class="w-full flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-indigo-200 bg-[#EEEFF7] shade">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('home') }}" class="flex items-center" aria-label="LensLocation home">
                <img src="{{ asset('images/Logo.png') }}" alt="LensLocation" class="h-7">
            </a>

            <nav aria-label="Footer">
                <ul class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm font-medium text-indigo-900">
                    <li><a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors duration-200">Home</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-indigo-600 transition-colors duration-200">About</a></li>
                    @if (auth()->check() && auth()->user()->role === \App\Enums\Role::Customer)
                        <li><a href="{{ route('customer.listings') }}" class="hover:text-indigo-600 transition-colors duration-200">Browse Listings</a></li>
                    @endif
                    <li><a href="{{ route('login') }}" class="hover:text-indigo-600 transition-colors duration-200">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-indigo-600 transition-colors duration-200">Register</a></li>
                </ul>
            </nav>

            <p class="text-xs text-indigo-900/60 text-center sm:text-right">&copy; {{ date('Y') }} LensLocation. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
