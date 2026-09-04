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

/**
 * Sign users out when they leave the site without logging out.
 *
 * The session cookie survives a closed tab (SESSION_EXPIRE_ON_CLOSE only
 * clears it when the whole browser quits), so someone who closes the tab and
 * comes back would still be signed in. A "return visit" is detected
 * client-side: sessionStorage is per-tab, so it survives refreshes and
 * same-tab navigation but dies with the tab — a brand-new tab has no marker,
 * while F5 and internal links keep it. When an authenticated page loads in a
 * fresh tab that arrived from outside the site (typed URL, bookmark, external
 * link) and no other tab of the site is open, the session is logged out and
 * the page reloads as a guest. Open tabs answer a BroadcastChannel ping so
 * opening a second tab never signs a user out by surprise.
 */
const TAB_MARKER = 'll-tab-open';
const TAB_CHANNEL = 'lenslocation-tabs';
const TAB_PING = 'll-tab-ping';
const TAB_PONG = 'll-tab-pong';

function tabStorageGet(key) {
    try {
        return window.sessionStorage.getItem(key);
    } catch {
        // Storage blocked/unavailable: reads fall through as "no marker".
        return null;
    }
}

function tabStorageSet(key, value) {
    try {
        window.sessionStorage.setItem(key, value);
    } catch {
        // Storage blocked/unavailable: nothing to do.
    }
}

// One channel object per page handles both roles: answering pings from other
// tabs and listening for the pong of our own check. A single object never
// receives its own postMessage, so it cannot answer itself. Null on browsers
// without BroadcastChannel (Safari < 15.4); those simply skip the guard.
const tabPresence = 'BroadcastChannel' in window
    ? new BroadcastChannel(TAB_CHANNEL)
    : null;

let pongListener = null;

if (tabPresence) {
    tabPresence.onmessage = (event) => {
        if (event.data === TAB_PING) {
            tabPresence.postMessage(TAB_PONG);
        } else if (event.data === TAB_PONG && pongListener) {
            pongListener();
        }
    };
}

function isFreshVisit() {
    if (tabStorageGet(TAB_MARKER)) {
        return false; // Refresh or same-tab navigation.
    }

    // First load in this tab's lifetime. A link from the site itself (opened
    // in a new tab, say) is still the same visit.
    if (document.referrer) {
        try {
            if (new URL(document.referrer).origin === window.location.origin) {
                return false;
            }
        } catch {
            // Malformed referrer: treat as an external entry point.
        }
    }

    return true;
}

function anyOtherTabOpen() {
    if (!tabPresence) {
        return Promise.resolve(false);
    }

    return new Promise((resolve) => {
        let settled = false;

        const finish = (answer) => {
            if (!settled) {
                settled = true;
                pongListener = null;
                clearTimeout(timeout);
                resolve(answer);
            }
        };

        const timeout = setTimeout(() => finish(false), 300);
        pongListener = () => finish(true);
        tabPresence.postMessage(TAB_PING);
    });
}

async function signOutOnReturn() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) {
        return;
    }

    try {
        await fetch('/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
            },
        });
    } catch {
        // Network hiccup: fall through and reload; the session cookie still
        // dies with the browser.
    }

    window.location.reload();
}

(async () => {
    if (!isFreshVisit()) {
        return;
    }

    // Mark before the async wait below so a refresh during the wait does not
    // read as a new visit and trigger a second logout.
    tabStorageSet(TAB_MARKER, '1');

    const authenticated =
        document.querySelector('meta[name="authenticated"]')?.content === '1';

    if (!authenticated || (await anyOtherTabOpen())) {
        return;
    }

    await signOutOnReturn();
})();
