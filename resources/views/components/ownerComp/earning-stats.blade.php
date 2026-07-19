{{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">

        <div class="card chart-transition shade bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-700">
            <div class="flex justify-between items-start">
                <p class="label text-xs md:text-sm">Total Earned</p>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-700 shrink-0 ml-2">
                    <i class="fa-solid fa-sack-dollar text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-indigo-700 mt-1">Rs. {{ number_format($stats['total_earned'], 2) }}</p>
        </div>

        <div class="card chart-transition shade bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-yellow-600/60">
            <div class="flex justify-between items-start">
                <p class="label text-xs md:text-sm">Pending Payout</p>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-50 text-yellow-600 shrink-0 ml-2">
                    <i class="fa-solid fa-hourglass-half text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-yellow-600 mt-1">Rs. {{ number_format($stats['pending_payout'], 2) }}</p>
        </div>

        <div class="card chart-transition shade bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-green-600/60">
            <div class="flex justify-between items-start">
                <p class="label text-xs md:text-sm">Already Paid Out</p>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-50 text-green-600 shrink-0 ml-2">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
            </div>
            <p class="text-xl font-bold text-green-600 mt-1">Rs. {{ number_format($stats['paid_out'], 2) }}</p>
        </div>

    </div>