<x-layouts.admin>
    <topbar 
        title="Dashboard"
        description="welcome back, {{ auth()->user()->first_name }}">
    </topbar>

    {{-- CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Total Users</h3>
                <i class="fa-solid fa-users text-indigo-900 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-900">{{ $stats['users'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Total Listings</h3>
                <i class="fa-solid fa-location-dot text-yellow-600 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['listings'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Total Bookings</h3>
                <i class="fa-solid fa-calendar-days text-green-700 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-green-700">{{ $stats['bookings'] }}</p>
        </div>

        <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
            <div class="flex justify-between items-start">
                <h3 class="text-sm text-indigo-900 mb-3">Rejected Listings</h3>
                <i class="fa-solid fa-ban text-red-500 shrink-0 ml-2"></i>
            </div>
            <p class="text-2xl font-bold text-red-500">{{ $stats['rejected_listings'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

    {{-- LINE CHART: Overview --}}
    <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
        <h2 class="font-bold text-indigo-900 text-base mb-4">Overview</h2>
        <div class="relative w-full" style="height: 240px;">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    {{-- DONUT CHART: Distribution --}}
    <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
        <h2 class="font-bold text-indigo-900 text-base mb-4">Distribution</h2>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6" style="min-height:240px;">
            <div class="relative" style="width:200px;height:200px;">
                <canvas id="distributionChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xs text-indigo-400">Total</span>
                    <span class="text-2xl font-bold text-indigo-900" id="donutTotal">0</span>
                </div>
            </div>
            <ul class="space-y-2 text-sm" id="donutLegend"></ul>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($chartData);

    const colors = {
        users:    '#7C6FE0',
        listings: '#F5A623',
        bookings: '#34C17B',
        rejected: '#F05C5C',
    };

    // Line Chart 
    const lineCtx = document.getElementById('overviewChart').getContext('2d');

    const makeGradient = (ctx, color) => {
        const g = ctx.createLinearGradient(0, 0, 0, 240);
        g.addColorStop(0, color + '33');
        g.addColorStop(1, color + '00');
        return g;
    };

    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                { label: 'Users',    data: chartData.users,    borderColor: colors.users,    backgroundColor: makeGradient(lineCtx, colors.users),    borderWidth: 2, pointBackgroundColor: colors.users,    pointRadius: 4, tension: 0.3, fill: true },
                { label: 'Listings', data: chartData.listings, borderColor: colors.listings, backgroundColor: makeGradient(lineCtx, colors.listings), borderWidth: 2, pointBackgroundColor: colors.listings, pointRadius: 4, tension: 0.3, fill: true },
                { label: 'Bookings', data: chartData.bookings, borderColor: colors.bookings, backgroundColor: makeGradient(lineCtx, colors.bookings), borderWidth: 2, pointBackgroundColor: colors.bookings, pointRadius: 4, tension: 0.3, fill: true },
                { label: 'Rejected', data: chartData.rejected, borderColor: colors.rejected, backgroundColor: makeGradient(lineCtx, colors.rejected), borderWidth: 2, pointBackgroundColor: colors.rejected, pointRadius: 4, tension: 0.3, fill: true },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top', align: 'end',
                    labels: { boxWidth: 8, boxHeight: 8, borderRadius: 4, useBorderRadius: true, font: { size: 11 }, color: '#312e81', padding: 12 }
                },
                tooltip: { backgroundColor: '#fff', titleColor: '#312e81', bodyColor: '#6366f1', borderColor: '#c7d2fe', borderWidth: 1, padding: 10, cornerRadius: 10 }
            },
            scales: {
                x: { grid: { color: '#e0e7ff55' }, ticks: { color: '#6366f1', font: { size: 11 } }, border: { display: false } },
                y: { grid: { color: '#e0e7ff55' }, ticks: { color: '#6366f1', font: { size: 11 }, stepSize: 1 }, border: { display: false }, beginAtZero: true }
            }
        }
    });

    // Donut Chart 
    const totals = {
        users:    chartData.stats.users,
        listings: chartData.stats.listings,
        bookings: chartData.stats.bookings,
        rejected: chartData.stats.rejected_listings,
    };

    const grandTotal = Object.values(totals).reduce((a, b) => a + b, 0);
    document.getElementById('donutTotal').textContent = grandTotal;

    new Chart(document.getElementById('distributionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Users', 'Listings', 'Bookings', 'Rejected'],
            datasets: [{ data: [totals.users, totals.listings, totals.bookings, totals.rejected], backgroundColor: [colors.users, colors.listings, colors.bookings, colors.rejected], borderColor: '#EEEFF7', borderWidth: 3, hoverOffset: 6 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff', titleColor: '#312e81', bodyColor: '#6366f1', borderColor: '#c7d2fe', borderWidth: 1, padding: 10, cornerRadius: 10,
                    callbacks: { label: (ctx) => ` ${ctx.raw} (${grandTotal > 0 ? ((ctx.raw / grandTotal) * 100).toFixed(1) : 0}%)` }
                }
            }
        }
    });

    //  Custom Legend
    const legendEl = document.getElementById('donutLegend');
    const labels   = ['Users', 'Listings', 'Bookings', 'Rejected'];
    const colorArr = [colors.users, colors.listings, colors.bookings, colors.rejected];
    const vals     = [totals.users, totals.listings, totals.bookings, totals.rejected];

    labels.forEach((label, i) => {
        const pct = grandTotal > 0 ? ((vals[i] / grandTotal) * 100).toFixed(1) : 0;
        legendEl.innerHTML += `<li class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full shrink-0" style="background:${colorArr[i]}"></span>
            <span class="text-indigo-900 font-medium">${label}</span>
            <span class="text-indigo-400">${vals[i]} (${pct}%)</span>
        </li>`;
    });
});
</script>
</x-layouts.admin>