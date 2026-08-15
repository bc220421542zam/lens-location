@vite('resources/js/charts.js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);

        const colors = {
            users:    '#7C6FE0',
            listings: '#F5A623',
            bookings: '#34C17B',
            rejected: '#F05C5C',
        };

        // Detect mobile
        const isMobile = window.innerWidth < 640;

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
                    { label: 'Users',    data: chartData.users,    borderColor: colors.users,    backgroundColor: makeGradient(lineCtx, colors.users),    borderWidth: 2, pointBackgroundColor: colors.users,    pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
                    { label: 'Listings', data: chartData.listings, borderColor: colors.listings, backgroundColor: makeGradient(lineCtx, colors.listings), borderWidth: 2, pointBackgroundColor: colors.listings, pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
                    { label: 'Bookings', data: chartData.bookings, borderColor: colors.bookings, backgroundColor: makeGradient(lineCtx, colors.bookings), borderWidth: 2, pointBackgroundColor: colors.bookings, pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
                    { label: 'Rejected', data: chartData.rejected, borderColor: colors.rejected, backgroundColor: makeGradient(lineCtx, colors.rejected), borderWidth: 2, pointBackgroundColor: colors.rejected, pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
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
                datasets: [{
                    data: [totals.users, totals.listings, totals.bookings, totals.rejected],
                    backgroundColor: [colors.users, colors.listings, colors.bookings, colors.rejected],
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
                        callbacks: { label: (ctx) => ` ${ctx.raw} (${grandTotal > 0 ? ((ctx.raw / grandTotal) * 100).toFixed(1) : 0}%)` }
                    }
                }
            }
        });

        // Custom Legend
        const legendEl = document.getElementById('donutLegend');
        const labels   = ['Users', 'Listings', 'Bookings', 'Rejected'];
        const colorArr = [colors.users, colors.listings, colors.bookings, colors.rejected];
        const vals     = [totals.users, totals.listings, totals.bookings, totals.rejected];

        labels.forEach((label, i) => {
        const pct = grandTotal > 0 ? ((vals[i] / grandTotal) * 100).toFixed(1) : 0;
        legendEl.innerHTML += `<li class="flex items-center gap-1.5 whitespace-nowrap">
            <span class="w-2 h-2 rounded-full shrink-0" style="background:${colorArr[i]}"></span>
            <span class="text-indigo-900 font-medium w-14">${label}</span>
            <span class="text-indigo-400">${vals[i]} (${pct}%)</span>
        </li>`;
    });
    });
</script>