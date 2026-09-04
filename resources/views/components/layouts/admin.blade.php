<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="authenticated" content="{{ auth()->check() ? '1' : '0' }}">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/Lenslocation-Logo.png') }}">
    <style>[x-cloak]{display:none !important;}</style>
</head>

<body class="bg-[#DDDEEF] flex flex-col h-screen overflow-hidden m-0 p-0"
    x-data="{ sidebarOpen: false }">

    <x-nav />

    <div class="relative flex flex-1 overflow-hidden">
        <x-adminComponents.sidebar-admin />

        {{-- MAIN CONTENT --}}
        <main class="flex-1 min-w-0 overflow-y-auto p-4 md:p-6">
            {{ $slot }}
        </main>
    </div>

</body>
</html>