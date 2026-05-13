<x-layouts.owner>

<div class="page">

    <x-topbar 
        title="Owner Dashboard"
        description="Welcome back, {{ auth()->user()->first_name }}!">
    </x-topbar>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
        <div class="card">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Total Listings</p>
                <i class="ti ti-map-pin text-yellow-500 text-xl"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-yellow-500 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Approved</p>
                <i class="ti ti-circle-check text-green-600 text-xl"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-green-600 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Pending</p>
                <i class="ti ti-clock text-yellow-600 text-xl"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <p class="label text-xs md:text-sm">Total Bookings</p>
                <i class="ti ti-calendar text-indigo-600 text-xl"></i>
            </div>
            <p class="text-xl md:text-2xl font-semibold text-indigo-600 mt-1">{{ $stats['bookings'] }}</p>
        </div>
    </div>

    {{-- CHARTS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6">

    {{-- LINE CHART: Overview --}}
    <div class="shade bg-[#EEEFF7] p-4 md:p-5 rounded-2xl">
        <h2 class="font-bold text-indigo-900 text-sm md:text-base mb-3 md:mb-4">Overview</h2>
        <div class="relative w-full" style="height:240px;">
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const chartData = @json($chartData);

    const colors = {
        listings: '#F5A623',
        bookings: '#34C17B',
        approved: '#7C6FE0',
        pending:  '#F05C5C',
    };

    const isMobile = window.innerWidth < 640;

    const makeGradient = (ctx, color) => {
        const g = ctx.createLinearGradient(0, 0, 0, 240);
        g.addColorStop(0, color + '33');
        g.addColorStop(1, color + '00');
        return g;
    };

    // LINE CHART
    const lineCtx = document.getElementById('overviewChart').getContext('2d');

    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Listings',
                    data: chartData.listings,
                    borderColor: colors.listings,
                    backgroundColor: makeGradient(lineCtx, colors.listings),
                    borderWidth: 2,
                    pointBackgroundColor: colors.listings,
                    pointRadius: isMobile ? 2 : 4,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Bookings',
                    data: chartData.bookings,
                    borderColor: colors.bookings,
                    backgroundColor: makeGradient(lineCtx, colors.bookings),
                    borderWidth: 2,
                    pointBackgroundColor: colors.bookings,
                    pointRadius: isMobile ? 2 : 4,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Approved',
                    data: chartData.approved,
                    borderColor: colors.approved,
                    backgroundColor: makeGradient(lineCtx, colors.approved),
                    borderWidth: 2,
                    pointBackgroundColor: colors.approved,
                    pointRadius: isMobile ? 2 : 4,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Pending',
                    data: chartData.pending,
                    borderColor: colors.pending,
                    backgroundColor: makeGradient(lineCtx, colors.pending),
                    borderWidth: 2,
                    pointBackgroundColor: colors.pending,
                    pointRadius: isMobile ? 2 : 4,
                    tension: 0.3,
                    fill: true
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: isMobile ? 6 : 8,
                        boxHeight: isMobile ? 6 : 8,
                        borderRadius: 4,
                        useBorderRadius: true,
                        font: { size: isMobile ? 9 : 11 },
                        color: '#312e81',
                        padding: isMobile ? 8 : 12
                    }
                },
                tooltip: {
                    backgroundColor: '#fff', titleColor: '#312e81', bodyColor: '#6366f1',
                    borderColor: '#c7d2fe', borderWidth: 1, padding: 8, cornerRadius: 10
                }
            },
            scales: {
                x: {
                    grid: { color: '#e0e7ff55' },
                    ticks: { color: '#6366f1', font: { size: isMobile ? 9 : 11 }, maxRotation: isMobile ? 45 : 0, maxTicksLimit: isMobile ? 4 : undefined },
                    border: { display: false }
                },
                y: {
                    grid: { color: '#e0e7ff55' },
                    ticks: { color: '#6366f1', font: { size: isMobile ? 9 : 11 }, stepSize: 1 },
                    border: { display: false },
                    beginAtZero: true
                }
            }
        }
    });

    // DONUT CHART
    const totals = {
        listings: {{ $stats['total'] }},
        bookings: {{ $stats['bookings'] }},
        approved: {{ $stats['active'] }},
        pending:  {{ $stats['pending'] }},
    };

    const grandTotal = Object.values(totals).reduce((a, b) => a + b, 0);
    document.getElementById('donutTotal').textContent = grandTotal;

    new Chart(document.getElementById('distributionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Listings', 'Bookings', 'Approved', 'Pending'],
            datasets: [{
                data: [totals.listings, totals.bookings, totals.approved, totals.pending],
                backgroundColor: [colors.listings, colors.bookings, colors.approved, colors.pending],
                borderColor: '#EEEFF7',
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff', titleColor: '#312e81', bodyColor: '#6366f1',
                    borderColor: '#c7d2fe', borderWidth: 1, padding: 10, cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => ` ${ctx.raw} (${grandTotal > 0 ? ((ctx.raw / grandTotal) * 100).toFixed(1) : 0}%)`
                    }
                }
            }
        }
    });

    // DONUT LEGEND
    const legendEl   = document.getElementById('donutLegend');
    const legLabels  = ['Listings', 'Bookings', 'Approved', 'Pending'];
    const colorArr   = [colors.listings, colors.bookings, colors.approved, colors.pending];
    const vals       = [totals.listings, totals.bookings, totals.approved, totals.pending];

    legLabels.forEach((label, i) => {
        const pct = grandTotal > 0 ? ((vals[i] / grandTotal) * 100).toFixed(1) : 0;
        legendEl.innerHTML += `<li class="flex items-center gap-1.5 whitespace-nowrap">
            <span class="w-2 h-2 rounded-full shrink-0" style="background:${colorArr[i]}"></span>
            <span class="text-indigo-900 font-medium w-16">${label}</span>
            <span class="text-indigo-400">${vals[i]} (${pct}%)</span>
        </li>`;
    });

});
</script>
@endpush

</x-layouts.owner>