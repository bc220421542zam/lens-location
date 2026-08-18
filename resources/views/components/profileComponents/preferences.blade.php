@props([
    'unreadRoute',
    'readAllRoute',
    'readRoute' => null,
    'allRoute',
])

<div
    x-data="navbarActions()"
    x-init="init()"
    class="flex items-center gap-0.5 md:gap-1"
>
    {{-- Role badge --}}
    <!-- <div class="hidden sm:flex items-center mr-1 md:mr-2">
        <span class="text-xs font-medium text-indigo-900 dark:text-indigo-200
                     bg-indigo-200 dark:bg-indigo-900/60
                     border border-indigo-300 dark:border-indigo-700
                     shadow-sm rounded-xl px-3 py-1 capitalize">
            {{ auth()->user()->role ?? 'User' }}
        </span>
    </div> -->


    {{-- Notifications bell --}}
    <div class="relative">
        <button
            @click="togglePanel()"
            title="Notifications"
            class="relative text-indigo-900 dark:text-indigo-300
                   hover:text-indigo-700 dark:hover:text-indigo-100
                   p-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800/50
                   transition-colors duration-200"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 md:w-6 md:h-6"
                 :class="ringing ? 'animate-[wiggle_0.4s_ease-in-out]' : ''">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967
                         8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967
                         0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714
                         0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>

            <span
                x-show="unreadCount > 0"
                x-text="unreadCount > 99 ? '99+' : unreadCount"
                class="absolute -top-0.5 -right-0.5
                       min-w-[1.1rem] h-[1.1rem] px-0.5
                       flex items-center justify-center
                       text-[10px] font-bold leading-none
                       bg-red-500 text-white rounded-full
                       ring-2 ring-white dark:ring-gray-900
                       pointer-events-none select-none"
            ></span>
        </button>

        {{-- Notification panel --}}
        <div
            x-show="panelOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            @click.outside="panelOpen = false"
            class="absolute right-0 mt-2 w-80 z-50
                   bg-white dark:bg-gray-900
                   border border-gray-200 dark:border-gray-700
                   rounded-2xl shadow-xl overflow-hidden"
            style="display:none;"
        >
            <div class="flex items-center justify-between px-4 py-3
                        border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">
                    Notifications
                </h3>
                <button
                    x-show="unreadCount > 0"
                    @click="markAllRead()"
                    class="text-xs text-indigo-600 dark:text-indigo-400
                           hover:text-indigo-800 font-medium transition-colors"
                >
                    Mark all read
                </button>
            </div>

            <ul class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
                <template x-if="notifications.length === 0">
                    <li class="px-4 py-8 text-center text-sm text-gray-400">
                        No notifications yet
                    </li>
                </template>

                <template x-for="n in notifications" :key="n.id">
                    <li
                        @click="markRead(n)"
                        class="flex items-start gap-3 px-4 py-3 cursor-pointer transition-colors"
                        :class="n.read
                            ? 'bg-white dark:bg-gray-900 hover:bg-gray-50'
                            : 'bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100'"
                    >
                        <div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-full
                                    flex items-center justify-center text-white text-xs"
                             :class="{
                                 'bg-emerald-500': n.type === 'listing',
                                 'bg-violet-500':  n.type === 'user',
                                 'bg-blue-500':    n.type === 'listing_status',
                                 'bg-orange-500':  n.type === 'user_status',
                                 'bg-indigo-500':  n.type === 'booking_status',
                             }">
                            <template x-if="n.type === 'listing' || n.type === 'listing_status'">
                                <i class="fa-solid fa-location-dot"></i>
                            </template>
                            <template x-if="n.type === 'user' || n.type === 'user_status'">
                                <i class="fa-solid fa-user"></i>
                            </template>
                            <template x-if="n.type === 'booking_status'">
                                <i class="fa-solid fa-calendar-check"></i>
                            </template>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-indigo-900 dark:text-indigo-600 leading-snug"
                               x-text="n.title"></p>
                            <p class="text-xs text-indigo-900 dark:text-indigo-600 mt-0.5 truncate"
                               x-text="n.body"></p>
                            <p class="text-[11px] text-indigo-400 mt-1"
                               x-text="n.time"></p>
                        </div>

                        <div x-show="!n.read"
                             class="mt-1.5 w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0">
                        </div>
                    </li>
                </template>
            </ul>

            <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-800 text-center">
                <a href="{{ $allRoute }}"
                   class="text-xs text-indigo-600 dark:text-indigo-400
                          hover:text-indigo-800 font-medium transition-colors">
                    View all notifications →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function navbarActions() {
    return {
        isDark: document.documentElement.classList.contains('dark'),
        panelOpen: false,
        ringing: false,
        notifications: [],
        unreadRouteUrl: '{{ $unreadRoute }}',
        readAllRouteUrl: '{{ $readAllRoute }}',
        readRouteUrl: '{{ $readRoute }}',

        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },

        togglePanel() {
            this.panelOpen = !this.panelOpen;
        },

        markRead(n) {
            if (n.read) return;
            n.read = true;
            // Persist the single read so it stays read after refresh.
            if (this.readRouteUrl) {
                fetch(this.readRouteUrl.replace('__ID__', n.id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                }).catch(() => {});
            }
        },

        markAllRead() {
            // Keep notifications visible; just clear their unread state (YouTube-style).
            this.notifications.forEach(n => n.read = true);
            fetch(this.readAllRouteUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            }).catch(() => {});
        },

        addNotification(type, title, body) {
            this.notifications.unshift({
                id: Date.now(), type, title, body, time: 'Just now', read: false,
            });
            this.ringing = true;
            setTimeout(() => { this.ringing = false; }, 500);
            if (Notification.permission === 'granted') {
                new Notification(title, { body, icon: '/favicon.ico' });
            }
        },

        async fetchNotifications() {
            const res = await fetch(this.unreadRouteUrl, {
                headers: { 'Accept': 'application/json' },
            });

            return res.ok ? res.json() : null;
        },

        /** Pull the latest list and surface anything we have not seen yet. */
        async syncNotifications() {
            try {
                const data = await this.fetchNotifications();
                if (! data) return;

                const knownIds = new Set(this.notifications.map(n => n.id));
                data.forEach(n => {
                    if (! knownIds.has(n.id)) {
                        this.addNotification(n.data.type, n.data.title, n.data.body);
                        this.notifications[0].id   = n.id;
                        this.notifications[0].time = n.created_at_human;
                    }
                });
            } catch (e) {}
        },

        async init() {
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
            try {
                const data = await this.fetchNotifications();
                if (data) {
                    this.notifications = data.map(n => ({
                        id:    n.id,
                        type:  n.data.type,
                        title: n.data.title,
                        body:  n.data.body,
                        time:  n.created_at_human,
                        read:  !!n.read_at,
                    }));
                }
            } catch (e) {}

            // Poll only while the tab is actually visible. A backgrounded tab
            // used to keep hitting this endpoint every 30s forever, costing a
            // database round trip per user per poll for updates nobody could see.
            setInterval(() => {
                if (! document.hidden) {
                    this.syncNotifications();
                }
            }, 30000);

            // Catch up straight away when the user returns to the tab.
            document.addEventListener('visibilitychange', () => {
                if (! document.hidden) {
                    this.syncNotifications();
                }
            });
        },
    };
}
</script>