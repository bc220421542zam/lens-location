import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

/**
 * Laravel Echo, loaded on demand.
 *
 * This module is imported dynamically (see `window.startEcho` in app.js) so
 * that Echo and Pusher — together the bulk of the JS bundle — are fetched only
 * by pages that actually subscribe to a channel, instead of on every request.
 */
let echo = null;

export function startEcho() {
    if (echo) {
        return echo;
    }

    window.Pusher = Pusher;

    echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    window.Echo = echo;

    return echo;
}

export default startEcho;
