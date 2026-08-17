@vite('resources/js/charts.js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);

        const colors = {
            total:     '#7C6FE0',
            upcoming:  '#F5A623',
            completed: '#34C17B',
            cancelled: '#F05C5C',
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
                    { label: 'Total bookings',     data: chartData.total,     borderColor: colors.total,     backgroundColor: makeGradient(lineCtx, colors.total),     borderWidth: 2, pointBackgroundColor: colors.total,     pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
                    { label: 'Upcoming',  data: chartData.upcoming,  borderColor: colors.upcoming,  backgroundColor: makeGradient(lineCtx, colors.upcoming),  borderWidth: 2, pointBackgroundColor: colors.upcoming,  pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
                    { label: 'Completed', data: chartData.completed, borderColor: colors.completed, backgroundColor: makeGradient(lineCtx, colors.completed), borderWidth: 2, pointBackgroundColor: colors.completed, pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
                    { label: 'Cancelled', data: chartData.cancelled, borderColor: colors.cancelled, backgroundColor: makeGradient(lineCtx, colors.cancelled), borderWidth: 2, pointBackgroundColor: colors.cancelled, pointRadius: isMobile ? 2 : 4, tension: 0.3, fill: true },
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
            total:     chartData.stats.total     ?? 0,
            upcoming:  chartData.stats.upcoming   ?? 0,
            completed: chartData.stats.completed  ?? 0,
            cancelled: chartData.stats.cancelled  ?? 0,
        };

        const grandTotal = Object.values(totals).reduce((a, b) => a + b, 0);
        document.getElementById('donutTotal').textContent = grandTotal > 0 ? grandTotal : 0;

        const donutData = grandTotal > 0
            ? [totals.total, totals.upcoming, totals.completed, totals.cancelled]
            : [1, 1, 1, 1];

        const donutColors = [colors.total, colors.upcoming, colors.completed, colors.cancelled];

        new Chart(document.getElementById('distributionChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Total bookings', 'Upcoming', 'Completed', 'Cancelled'],
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderColor: '#EEEFF7',
                    borderWidth: 3,
                    hoverOffset: grandTotal > 0 ? 6 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: grandTotal > 0,
                        backgroundColor: '#fff', titleColor: '#312e81', bodyColor: '#6366f1',
                        borderColor: '#c7d2fe', borderWidth: 1, padding: 10, cornerRadius: 10,
                        callbacks: {
                            label: (ctx) => ` ${ctx.raw} (${grandTotal > 0 ? ((ctx.raw / grandTotal) * 100).toFixed(1) : 0}%)`
                        }
                    }
                }
            }
        });

        // Custom Legend — always matches the donut data exactly
        const legendEl = document.getElementById('donutLegend');
        const labels   = ['Total', 'Upcoming', 'Completed', 'Cancelled'];
        const colorArr = [colors.total, colors.upcoming, colors.completed, colors.cancelled];
        const vals     = [totals.total, totals.upcoming, totals.completed, totals.cancelled];

        labels.forEach((label, i) => {
            const pct = grandTotal > 0 ? ((vals[i] / grandTotal) * 100).toFixed(1) : 0;
            legendEl.innerHTML += `<li class="flex items-center gap-1.5 whitespace-nowrap">
                <span class="w-2 h-2 rounded-full shrink-0" style="background:${colorArr[i]}"></span>
                <span class="text-indigo-900 font-medium w-20">${label}</span>
                <span class="text-indigo-400">${vals[i]} (${pct}%)</span>
            </li>`;
        });
    });
</script>