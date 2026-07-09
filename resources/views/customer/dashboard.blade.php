<x-layouts.customer>
<div>

    {{-- TOP BAR --}}
    <x-topbar 
        title="Dashboard"
        description="welcome back, {{ auth()->user()->first_name }}">
    </x-topbar>
    
    {{-- STATS --}}
    <x-customerComp.statsDashboard :stats="$stats" />

    {{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6">

    {{-- LINE CHART: Booking Activity --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border border-l-3 border-indigo-400">
        <h2 class="font-bold text-indigo-900 text-sm md:text-base mb-3 md:mb-4">Booking Activity</h2>
        <div class="relative w-full" style="height:240px;">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    {{-- DONUT CHART: Status Distribution --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 md:p-5 rounded-2xl border border-l-3 border-indigo-400">
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
