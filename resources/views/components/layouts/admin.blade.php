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

    <x-nav />
    {{-- sidebar --}}
    <div class="flex flex-1 overflow-hidden">
    <x-sidebar-admin /> 
        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>
    </div>

</body>
</html>