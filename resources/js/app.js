import './bootstrap';

import Alpine from 'alpinejs';

/**
 * Alpine ships in the bundle instead of coming from a CDN: one less origin to
 * resolve and connect to before the UI becomes interactive, and it is cached
 * alongside the rest of the app's assets.
 */
window.Alpine = Alpine;
Alpine.start();

/**
 * Broadcasting is opt-in. Echo and Pusher are a dynamic import so pages that
 * never subscribe to a channel neither download them nor open a WebSocket.
 * A page that needs live updates calls:
 *
 *     const echo = await window.startEcho();
 *     echo.private('admin').listen('NewListing', ...);
 */
window.startEcho = () => import('./echo').then((module) => module.startEcho());
