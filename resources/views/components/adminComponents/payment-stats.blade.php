{{-- STATS --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-indigo-800">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Revenue</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-800">
                <i class="fa-solid fa-sack-dollar text-sm"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-indigo-900">Rs. {{ number_format($stats['total_revenue'], 2) }}</p>
    </div>

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-yellow-600/60">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Platform Commission</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-50 text-yellow-600">
                <i class="fa-solid fa-percent text-sm"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-yellow-600">Rs. {{ number_format($stats['total_commission'], 2) }}</p>
    </div>

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-green-700/60">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Pending Payouts</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 text-green-700">
                <i class="fa-solid fa-clock text-sm"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-green-700">Rs. {{ number_format($stats['pending_payouts'], 2) }}</p>
    </div>

    <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-red-500/60">
        <div class="flex justify-between items-start">
            <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Failed Payments</h3>
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 text-red-600">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-bold text-red-500">{{ $stats['failed'] }}</p>
    </div>

</div>