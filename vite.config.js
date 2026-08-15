import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // `charts.js` is a separate entry so the charting library loads only
            // on the dashboards that draw charts.
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/charts.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
