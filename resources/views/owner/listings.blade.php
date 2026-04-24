<x-layouts.owner>

<div class="page">

    {{-- TOP BAR --}}
    <div class="top-bar">
        <div>
            <h1 class="title text-indigo-900">My Listings</h1>
            <p class="text-sm text-gray-500">Manage your photography locations</p>
        </div>
        <button class="btn w-auto px-4">+ Add Location</button>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="label">Total Listings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">4</p>
        </div>
        <div class="card">
            <p class="label">Active</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">3</p>
            <p class="text-xs text-green-600 mt-1">↑ 1 this month</p>
        </div>
        <div class="card">
            <p class="label">Total Bookings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1 ">28</p>
        </div>
    </div>

    {{-- LISTINGS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="card p-0 overflow-hidden">
            <div class="relative h-40 bg-gray-100">
                <img src="/images/card1.jpg" alt="Heritage Courtyard" class="w-full h-full object-cover">
                <span class="badge badge-active">Active</span>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-indigo-900">Heritage Courtyard</h3>
                <p class="text-sm text-gray-500 mt-1">Old City, Lahore &middot; PKR 4,500/hr &middot; 12 bookings</p>
            </div>
            <div class="flex gap-2 px-4 pb-4">
                <button class="action-btn">Edit</button>
                <button class="action-btn danger">Delete</button>
            </div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="relative h-40 bg-gray-100">
                <img src="/images/card2.jpg" alt="Garden Studio" class="w-full h-full object-cover">
                <span class="badge badge-active">Active</span>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-indigo-900">Garden Studio</h3>
                <p class="text-sm text-gray-500 mt-1">DHA Phase 5, Lahore &middot; PKR 3,000/hr &middot; 9 bookings</p>
            </div>
            <div class="flex gap-2 px-4 pb-4">
                <button class="action-btn">Edit</button>
                <button class="action-btn danger">Delete</button>
            </div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="relative h-40 bg-gray-100">
                <img src="/images/card3.jpg" alt="Rooftop Terrace" class="w-full h-full object-cover">
                <span class="badge badge-active">Active</span>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-indigo-900">Rooftop Terrace</h3>
                <p class="text-sm text-gray-500 mt-1">Gulberg III, Lahore &middot; PKR 5,500/hr &middot; 7 bookings</p>
            </div>
            <div class="flex gap-2 px-4 pb-4">
                <button class="action-btn">Edit</button>
                <button class="action-btn danger">Delete</button>
            </div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="relative h-40 bg-gray-50 flex items-center justify-center">
                <span class="text-5xl">...</span>
                <span class="badge badge-draft">Draft</span>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-indigo-900">Indoor Greenhouse</h3>
                <p class="text-sm text-gray-500 mt-1">Johar Town, Lahore &middot; PKR 2,800/hr &middot; 0 bookings</p>
            </div>
            <div class="flex gap-2 px-4 pb-4">
                <button class="action-btn">Edit</button>
                <button class="action-btn publish">Publish</button>
                <button class="action-btn danger">Delete</button>
            </div>
        </div>

    </div>
</div>

</x-layouts.owner>