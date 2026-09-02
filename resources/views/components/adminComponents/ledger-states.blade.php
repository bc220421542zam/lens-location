{{-- SUMMARY CARDS: one compact row; values and range labels follow the filters. --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
        <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-indigo-800">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total bookings</h3>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-800">
                    <i class="fa-solid fa-calendar text-sm"></i>
                </div>
            </div>
            <div>
                <p class="text-xl md:text-2xl font-bold text-indigo-900 whitespace-nowrap"
                   x-text="summary.bookings.toLocaleString('en-US')">{{ number_format($summary['bookings']) }}</p>
                <p class="flex items-center gap-1.5 whitespace-nowrap text-xs text-indigo-400 mt-0.5 md:mt-1">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <span x-text="summary.range_label">{{ $summary['range_label'] }}</span>
                </p>
            </div>
        </div>

        <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-green-700/60">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total amount received</h3>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 text-green-700">
                    <i class="fa-solid fa-sack-dollar text-sm"></i>
                </div>
            </div>
            <div>
                <p class="text-xl md:text-2xl font-bold text-green-700 whitespace-nowrap"
                   x-text="rs(summary.received)">Rs {{ number_format($summary['received']) }}</p>
                <p class="flex items-center gap-1.5 whitespace-nowrap text-xs text-indigo-400 mt-0.5 md:mt-1">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <span x-text="summary.range_label">{{ $summary['range_label'] }}</span>
                </p>
            </div>
        </div>

        <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-yellow-600/60">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Platform commission ({{ $commissionPct }}%)</h3>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-50 text-yellow-600">
                    <i class="fa-solid fa-percent text-sm"></i>
                </div>
            </div>
            <div>
                <p class="text-xl md:text-2xl font-bold text-yellow-600 whitespace-nowrap"
                   x-text="rs(summary.commission)">Rs {{ number_format($summary['commission']) }}</p>
                <p class="flex items-center gap-1.5 whitespace-nowrap text-xs text-indigo-400 mt-0.5 md:mt-1">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <span x-text="summary.range_label">{{ $summary['range_label'] }}</span>
                </p>
            </div>
        </div>

        <div class="card card-transition shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border-l-3 border-indigo-700/60">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Paid to owners ({{ $ownerPct }}%)</h3>
                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-700">
                    <i class="fa-solid fa-clock text-sm"></i>
                </div>
            </div>
            <div>
                <p class="text-xl md:text-2xl font-bold text-indigo-700 whitespace-nowrap"
                   x-text="rs(summary.paid_to_owners)">Rs {{ number_format($summary['paid_to_owners']) }}</p>
                <p class="flex items-center gap-1.5 whitespace-nowrap text-xs text-indigo-400 mt-0.5 md:mt-1">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <span x-text="summary.range_label">{{ $summary['range_label'] }}</span>
                </p>
            </div>
        </div>
    </div>