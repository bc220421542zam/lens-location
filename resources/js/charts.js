/**
 * Chart.js, bundled locally and tree-shaken.
 *
 * The dashboards draw line, doughnut and (ledger trend) stacked bar charts,
 * so registering just those controllers/elements ships a fraction of the full
 * UMD build the pages used to pull from a CDN. Loaded as its own Vite entry so
 * the ~200 kB of charting code stays off every non-dashboard page.
 */
import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    DoughnutController,
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Filler,   // datasets use `fill: true`
    Legend,
    Tooltip,
);

// The dashboard views construct charts inside DOMContentLoaded handlers, which
// run after module scripts execute, so this global is always ready in time.
window.Chart = Chart;
