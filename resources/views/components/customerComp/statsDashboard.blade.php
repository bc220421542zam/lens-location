{{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Bookings</h3>
                <i class="fa-solid fa-calendar-days text-indigo-600 shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</p>
        </div>

        <div class="shade card bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Upcoming</p>
                <i class="fa-solid fa-calendar-check text-amber-600"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-amber-600 mt-1">{{ $stats['upcoming'] }}</p>
        </div>

        <div class="shade card bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Completed</p>
                <i class="fa-solid fa-check-circle text-green-600"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>

        <div class="shade card bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Total Spent</p>
                <i class="fa-solid fa-sack-dollar text-indigo-800"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-indigo-800 mt-1">
                PKR {{ number_format((float) $stats['spent']) }}
            </p>
        </div>
    </div>