<x-layouts.customer>
<div>

    {{-- TOP BAR --}}
    <x-topbar 
        title="Dashboard"
        description="welcome back, {{ auth()->user()->first_name }}">
    </x-topbar>
    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card rounded-2xl shade">
            <p class="label">Total Bookings</p>
            <p class="text-2xl font-semibold text-indigo-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="card rounded-2xl shade">
            <p class="label">Upcoming</p>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['upcoming'] }}</p>
        </div>
        <div class="card rounded-2xl shade">
            <p class="label">Completed</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="card rounded-2xl shade">
            <p class="label">Total Spent</p>
            <p class="text-2xl font-semibold text-indigo-800 mt-1">
                PKR {{ number_format((float) $stats['spent']) }}
            </p>
        </div>
    </div>

    {{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6">

    {{-- LINE CHART: Booking Activity --}}
    <div class="shade card bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
        <h2 class="font-bold text-indigo-900 text-sm md:text-base mb-3 md:mb-4">Booking Activity</h2>
        <div class="relative w-full" style="height:240px;">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    {{-- DONUT CHART: Status Distribution --}}
    <div class="shade card bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
        <h2 class="font-bold text-indigo-900 text-sm md:text-base mb-3 md:mb-4">Booking Status</h2>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 md:gap-6" style="min-height:200px;">
            <div class="relative shrink-0" style="width:160px; height:160px;">
                <canvas id="distributionChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl md:text-2xl font-bold text-indigo-900" id="donutTotal">0</span>
                </div>
            </div>
            <ul class="space-y-1.5 md:space-y-2 text-xs md:text-sm w-full sm:w-auto" id="donutLegend"></ul>
        </div>
    </div>
</div>
</div>

<x-customerComp.chart :chart-data="$chartData" />
</x-layouts.customer>
