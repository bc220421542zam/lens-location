<x-layouts.admin>
    <x-topbar 
        title="Dashboard"
        description="Welcome back, {{ auth()->user()->first_name }}">
    </x-topbar>


    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">

        <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl ">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Users</h3>
                <i class="fa-solid fa-users text-indigo-900 shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-indigo-900">{{ $stats['users'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Listings</h3>
                <i class="fa-solid fa-location-dot text-yellow-600 shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-yellow-600">{{ $stats['listings'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Total Bookings</h3>
                <i class="fa-solid fa-calendar-days text-green-700 shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-green-700">{{ $stats['bookings'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-xs md:text-sm text-indigo-900 mb-2 md:mb-3">Rejected Listings</h3>
                <i class="fa-solid fa-ban text-red-500 shrink-0 ml-2 text-sm md:text-base"></i>
            </div>
            <p class="text-xl md:text-2xl font-bold text-red-500">{{ $stats['rejected_listings'] }}</p>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6">

        {{-- LINE CHART: Overview --}}
        <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <h2 class="font-bold text-indigo-900 text-sm md:text-base mb-3 md:mb-4">Overview</h2>
            <div class="relative w-full" style="height: 200px; md:height: 240px;">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>

        {{-- DONUT CHART: Distribution --}}
        <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
            <h2 class="font-bold text-indigo-900 text-sm md:text-base mb-3 md:mb-4">Distribution</h2>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 md:gap-6" style="min-height:200px;">
                <div class="relative shrink-0" style="width:160px;height:160px;">
                    <canvas id="distributionChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xl md:text-2xl font-bold text-indigo-900" id="donutTotal">0</span>
                    </div>
                </div>
                <ul class="space-y-1.5 md:space-y-2 text-xs md:text-sm w-full sm:w-auto" id="donutLegend"></ul>
            </div>
        </div>

    </div>

<x-adminComponents.Chart :chartData="$chartData"/>

</x-layouts.admin>