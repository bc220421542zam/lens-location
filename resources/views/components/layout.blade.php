<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name')}}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-slate-900">
    <header class="bg-slate-200 shadow-lg">
        <nav class="flex justify-between items-center p-4">
            <img src="/images/Logo.png" alt="Logo" class="h-10">

            @guest
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="nav-link">Home</a>
                <a href="{{ route('login') }}" class="nav-link">Login</a>

                @if (!Request::is('register'))
                    <a href="{{ route('register') }}" class="nav-link">Register</a>
                @endif
            </div>
            @endguest
        </nav>
    </header>
    <main class="w-full">
        {{$slot}}
    </main>    
</body>
</html>