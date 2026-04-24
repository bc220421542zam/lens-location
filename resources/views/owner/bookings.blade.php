<x-layouts.owner>
<div class="page">
 
    {{-- TOP BAR --}}
    <div class="top-bar">
        <div>
            <h1 class="title text-indigo-900">Bookings</h1>
            <p class="text-sm text-gray-500">Manage incoming booking requests</p>
        </div>
    </div>
    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <p class="label">Total Bookings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">28</p>
        </div>
        <div class="card">
            <p class="label">Pending</p>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">5</p>
        </div>
        <div class="card">
            <p class="label">Confirmed</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">18</p>
        </div>
        <div class="card">
            <p class="label">Completed</p>
            <p class="text-2xl font-semibold text-indigo-800 mt-1">5</p>
        </div>
    </div>
    {{-- FILTER TABS --}}
    <div class="flex gap-2 mb-4">
        <button class="action-btn">All</button>
        <button class="action-btn">Pending</button>
        <button class="action-btn">Confirmed</button>
        <button class="action-btn">Completed</button>
        <button class="action-btn danger">Cancelled</button>
    </div>

    {{-- BOOKINGS LIST --}}
    <div class="flex flex-col gap-3">
 
        {{-- BOOKING ROW - PENDING --}}
        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-indigo-200 flex items-center justify-center font-semibold text-indigo-700 shrink-0">
                AK
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">Ahmed Khan</p>
                <p class="text-sm text-gray-500">Heritage Courtyard &middot; 3 hrs &middot; Wedding shoot</p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-sm text-gray-400">Apr 19, 2026 &middot; 10:00 AM</p>
                <p class="font-semibold text-gray-900">PKR 13,500</p>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
            </div>
            <div class="flex flex-col gap-1 shrink-0">
                <button class="action-btn publish">Accept</button>
                <button class="action-btn danger">Reject</button>
            </div>
        </div>
 
        {{-- BOOKING ROW - PENDING --}}
        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center font-semibold text-purple-700 shrink-0">
                SR
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">Sara Riaz</p>
                <p class="text-sm text-gray-500">Garden Studio &middot; 2 hrs &middot; Portrait session</p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-sm text-gray-400">Apr 20, 2026 &middot; 2:00 PM</p>
                <p class="font-semibold text-gray-900">PKR 6,000</p>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
            </div>
            <div class="flex flex-col gap-1 shrink-0">
                <button class="action-btn publish">Accept</button>
                <button class="action-btn danger">Reject</button>
            </div>
        </div>
 
        {{-- BOOKING ROW - CONFIRMED --}}
        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center font-semibold text-green-700 shrink-0">
                MF
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">Mohammad Farooq</p>
                <p class="text-sm text-gray-500">Rooftop Terrace &middot; 4 hrs &middot; Brand shoot</p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-sm text-gray-400">Apr 22, 2026 &middot; 6:00 PM</p>
                <p class="font-semibold text-gray-900">PKR 22,000</p>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">Confirmed</span>
            </div>
        </div>
 
        {{-- BOOKING ROW - COMPLETED --}}
        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center font-semibold text-indigo-700 shrink-0">
                ZB
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">Zara Butt</p>
                <p class="text-sm text-gray-500">Heritage Courtyard &middot; 2 hrs &middot; Graduation photos</p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-sm text-gray-400">Apr 15, 2026 &middot; 11:00 AM</p>
                <p class="font-semibold text-gray-900">PKR 9,000</p>
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">Completed</span>
            </div>
        </div>
 
    </div>

</div>
</x-layouts.owner>