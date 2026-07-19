{{-- STATS --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-indigo-800">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Users</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-800 shrink-0 ml-2">
                <i class="fa-solid fa-users text-sm md:text-base"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-indigo-900">{{ $stats['users'] }}</p>
    </div>

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-yellow-600/60">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Listings</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 shrink-0 ml-2">
                <i class="fa-solid fa-location-dot text-sm md:text-base"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['listings'] }}</p>
    </div>

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-green-700/60">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Bookings</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 text-green-700 shrink-0 ml-2">
                <i class="fa-solid fa-calendar-days text-sm md:text-base"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-green-700">{{ $stats['bookings'] }}</p>
    </div>

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-red-500/60">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Rejected Listings</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 text-red-500 shrink-0 ml-2">
                <i class="fa-solid fa-ban text-sm md:text-base"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-red-500">{{ $stats['rejected_listings'] }}</p>
    </div>

</div>