<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ session('theme') == 'dark' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-slate-900' }}">

    <header class="shadow-lg">
        <nav class="flex justify-between items-center px-4 sm:px-6 lg:px-10 py-3 bg-[#EEEFF7]">

            {{-- Logo --}}
            <img src="/images/Logo.png" alt="Logo" class="h-8 sm:h-10">

            {{-- Nav Links --}}
            @guest
                <div class="flex items-center gap-2 sm:gap-4">
                    @if (Request::is('login'))
                        <a href="{{ route('register') }}"
                           class="text-sm sm:text-base px-3 py-1.5 sm:px-4 sm:py-2
                                  bg-indigo-900 text-white rounded-lg
                                  hover:bg-indigo-700 transition-colors duration-200">
                            Register
                        </a>
                    @endif

                    @if (Request::is('register'))
                        <a href="{{ route('login') }}"
                           class="text-sm sm:text-base px-3 py-1.5 sm:px-4 sm:py-2
                                  bg-indigo-900 text-white rounded-lg
                                  hover:bg-indigo-700 transition-colors duration-200">
                            Login
                        </a>
                    @endif
                </div>
            @endguest

        </nav>
    </header>

    <main class="w-full">
        {{ $slot }}
    </main>
</body>
</html>