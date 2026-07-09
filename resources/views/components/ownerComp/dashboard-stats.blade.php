{{-- STATS --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
        <div class="shade card card-transition bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border border-l-3 border-indigo-700">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Listings</h3>
                <i class="fa-solid fa-location-dot text-indigo-700 shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-indigo-700">{{ $stats['total'] }}</p>
        </div>
        <div class="shade card card-transition bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border border-l-3 border-[#14b8a6]">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Approved</p>
            <svg viewBox="0 0 24 24" fill="currentColor" class="size-5 text-[#14b8a6]">
                <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
            </svg>

            </div>
            <p class="text-xl md:text-2xl font-semibold text-[#14b8a6] mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="shade card card-transition bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border border-l-3 border-yellow-600">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Pending</p>
                <i class="fa-solid fa-hourglass-half text-yellow-600"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="shade card card-transition bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border border-l-3 border-[#ec4899]">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Total Bookings</p>
                <i class="fa-solid fa-calendar-days text-[#ec4899] shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-[#ec4899] mt-1">{{ $stats['bookings'] }}</p>
        </div>
    </div>