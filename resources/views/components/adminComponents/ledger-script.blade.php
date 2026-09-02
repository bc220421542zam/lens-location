
@vite('resources/js/charts.js')
<script>
    // Registered here (not inlined into x-data) so the component source can
    // never leak into the markup: the attribute only ever holds
    // `ledgerPage({...json...})`.
    //
    // The registration happens on `alpine:init` rather than at the top level:
    // this classic script runs while the document is still parsing, before the
    // deferred app.js module has defined the `Alpine` global, whereas
    // `alpine:init` fires just before Alpine walks the DOM - so the component
    // exists exactly when `x-data` is evaluated.
    document.addEventListener('alpine:init', () => {
        Alpine.data('ledgerPage', (init) => ({
        loading: false,
        error: null,
        period: init.period,
        filters: init.filters,
        summary: init.summary,
        count: init.count,
        baseUrl: init.baseUrl,

        get hasFilters() { return this.filters.search !== "" || this.filters.from !== "" || this.filters.to !== "" || this.filters.status !== ""; },
        get countLabel() { return this.count + (this.count === 1 ? " booking" : " bookings"); },
        rs(v) { return "Rs " + Math.round(v).toLocaleString("en-US"); },

        buildParams() {
            const params = new URLSearchParams();
            for (const [key, value] of Object.entries(this.filters)) {
                if (value !== "" && value !== null) params.set(key, value);
            }
            // Submitter buttons are absent from FormData - state is the source of truth.
            params.set("period", this.period);
            return params;
        },

        async refresh() {
            this.loading = true; this.error = null;
            try {
                const res = await fetch(this.baseUrl + "?" + this.buildParams(), {
                    headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) {
                    if (res.status === 422) {
                        const body = await res.json().catch(() => ({}));
                        this.error = body.message || "Some filter values are invalid.";
                    } else {
                        throw new Error("HTTP " + res.status);
                    }
                    return;
                }
                this.apply(await res.json());
            } catch (err) {
                console.error("Ledger refresh failed:", err);
                this.error = "Could not refresh the ledger. Please try again.";
            } finally {
                this.loading = false;
            }
        },

        apply(data) {
            this.summary = data.summary;
            this.count = data.count;
            this.period = data.period;
            document.getElementById("ledger-tbody").outerHTML = data.html;
            document.getElementById("ledger-pagination").outerHTML = data.pagination;
            document.getElementById("ledger-report").outerHTML = data.report_html;
            const subtitle = document.getElementById("ledgerTrendSubtitle");
            if (subtitle) subtitle.textContent = data.trend_subtitle;
            if (window.updateLedgerChart) window.updateLedgerChart(data.trend);
            history.replaceState(null, "", data.url);
        },

        // Delegated pagination: links inside swapped HTML carry no Alpine bindings.
        onPanelClick(event) {
            const link = event.target.closest("a");
            if (link && link.closest("#ledger-pagination")) {
                event.preventDefault();
                this.fetchUrl(link.href);
            }
        },

        async fetchUrl(url) {
            this.loading = true; this.error = null;
            try {
                const res = await fetch(url, {
                    headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error("HTTP " + res.status);
                this.apply(await res.json());
            } catch (err) {
                console.error("Ledger refresh failed:", err);
                this.error = "Could not refresh the ledger. Please try again.";
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters.search = ""; this.filters.from = ""; this.filters.to = ""; this.filters.status = "";
            this.refresh();
        },
        }));
    });

    document.addEventListener('DOMContentLoaded', function () {
        const trend = @json($trend);
        const series = (key) => trend.map((bucket) => bucket[key]);

        window.ledgerChart = new Chart(document.getElementById('ledgerTrendChart'), {
            type: 'bar',
            data: {
                labels: series('label'),
                datasets: [
                    {
                        label: 'Platform commission',
                        data: series('commission'),
                        backgroundColor: '#F5A623',
                        borderColor: '#EEEFF7',
                        borderWidth: 2,
                        borderRadius: { topLeft: 4, topRight: 4 },
                        stack: 'ledger',
                    },
                    {
                        label: 'Owner payout',
                        data: series('payout'),
                        backgroundColor: '#34C17B',
                        borderColor: '#EEEFF7',
                        borderWidth: 2,
                        stack: 'ledger',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    // The legend is rendered as plain HTML below the chart to
                    // match the design (dot + label), so Chart.js's own legend
                    // stays off.
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#312e81',
                        bodyColor: '#6366f1',
                        borderColor: '#c7d2fe',
                        borderWidth: 1,
                        padding: 8,
                        cornerRadius: 10,
                        callbacks: {
                            label: (ctx) => ' ' + ctx.dataset.label + ': Rs ' + ctx.parsed.y.toLocaleString('en-US'),
                        },
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#6366f1', font: { size: 11 } },
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: '#e0e7ff55' },
                        border: { display: false },
                        ticks: {
                            color: '#6366f1',
                            font: { size: 11 },
                            callback: (value) => 'Rs ' + value.toLocaleString('en-US'),
                        },
                    },
                },
            },
        });

        // Called by the Alpine component after every AJAX refresh: mutate the
        // existing chart instead of destroying/recreating it.
        window.updateLedgerChart = function (trend) {
            const chart = window.ledgerChart;
            chart.data.labels = trend.map((bucket) => bucket.label);
            chart.data.datasets[0].data = trend.map((bucket) => bucket.commission);
            chart.data.datasets[1].data = trend.map((bucket) => bucket.payout);
            chart.update();
        };
    });
</script>