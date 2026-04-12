<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" 
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-[#DDDEEF] flex flex-col h-screen overflow-hidden m-0 p-0">

    {{-- TOP NAVBAR --}}
<nav class="shrink-0 bg-[#EEEFF7] shadow flex justify-between items-center px-6 py-3 z-10">
    <img src="/images/Logo.png" alt="Logo" class="h-10">

    <div class="flex items-center gap-4">
        <i class="icon fa-regular fa-bell"></i>
        <i class="icon fa-regular fa-user"></i>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="bg-indigo-900 text-white px-3 py-1 rounded hover:bg-[#2C3399]">
                Logout
            </button>
        </form>
    </div>
</nav>

    {{-- BOTTOM SECTION: sidebar + content --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-64 shrink-0 bg-indigo-900 text-white flex flex-col p-4 gap-1">
            <a href="{{ route('dashboard') }}" class="py-2 px-3 hover:bg-[#2C3399] rounded">
            <i class="fa-solid fa-house w-5 mr-2 text-center"></i>    
            Dashboard</a>
            <a href="#" class="py-2 px-3 hover:bg-[#2C3399] rounded">
            <i class="fa-solid fa-users w-5 mr-2 text-center"></i>
            Users</a>
            <a href="#" class="py-2 px-3 hover:bg-[#2C3399] rounded">
            <i class="fa-solid fa-location-dot w-5 mr-2 text-center"></i>
            Listings</a>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>

    </div>

</body>
</html>