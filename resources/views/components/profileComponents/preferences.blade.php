@props([
    'unreadRoute',
    'readAllRoute',
    'allRoute',
])

<div
    x-data="navbarActions()"
    x-init="init()"
    class="flex items-center gap-0.5 md:gap-1"
>
    {{-- Role badge --}}
    <div class="hidden sm:flex items-center mr-1 md:mr-2">
        <span class="text-xs font-medium text-indigo-900 dark:text-indigo-200
                     bg-indigo-200 dark:bg-indigo-900/60
                     border border-indigo-300 dark:border-indigo-700
                     shadow-sm rounded-xl px-3 py-1 capitalize">
            {{ auth()->user()->role ?? 'User' }}
        </span>
    </div>

    <!-- {{-- Theme toggle --}}
    <button
        @click="toggleTheme()"
        :title="isDark ? 'Switch to light' : 'Switch to dark'"
        class="text-indigo-900 dark:text-indigo-300
               hover:text-indigo-700 dark:hover:text-indigo-100
               p-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800/50
               transition-colors duration-200"
    >
        <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 md:w-6 md:h-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386
                     6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591
                     1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75
                     12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
        </svg>
        <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 md:w-6 md:h-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385
                     0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753
                     9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753
                     9.753 0 0 0 9.002-5.998Z" />
        </svg>
    </button> -->

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
                                    flex items-center justify-center text-white text-sm"
                             :class="{
                                 'bg-emerald-500': n.type === 'listing',
                                 'bg-violet-500':  n.type === 'user',
                                 'bg-blue-500':    n.type === 'listing_status',
                                 'bg-orange-500':  n.type === 'user_status',
                             }">
                            <template x-if="n.type === 'listing' || n.type === 'listing_status'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" class="w-4 h-4">
                                    <path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0
                                             1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0
                                             0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0
                                             0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0
                                             0 1.414-1.414l-7-7Z" />
                                </svg>
                            </template>
                            <template x-if="n.type === 'user' || n.type === 'user_status'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor" class="w-4 h-4">
                                    <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465
                                             14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957
                                             0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002
                                             7.002 0 0 0-13.074.003Z" />
                                </svg>
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

        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },

        // toggleTheme() {
        //     this.isDark = !this.isDark;
        //     if (this.isDark) {
        //         document.documentElement.classList.add('dark');
        //         localStorage.setItem('theme', 'dark');
        //     } else {
        //         document.documentElement.classList.remove('dark');
        //         localStorage.setItem('theme', 'light');
        //     }
        // },

        togglePanel() {
            this.panelOpen = !this.panelOpen;
        },

        markRead(n) {
            n.read = true;
        },

        markAllRead() {
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

        async init() {
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
            try {
                const res = await fetch(this.unreadRouteUrl, {
                    headers: { 'Accept': 'application/json' },
                });
                if (res.ok) {
                    const data = await res.json();
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

            setInterval(async () => {
                try {
                    const res = await fetch(this.unreadRouteUrl, {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const knownIds = new Set(this.notifications.map(n => n.id));
                    data.forEach(n => {
                        if (!knownIds.has(n.id)) {
                            this.addNotification(n.data.type, n.data.title, n.data.body);
                            this.notifications[0].id   = n.id;
                            this.notifications[0].time = n.created_at_human;
                        }
                    });
                } catch (e) {}
            }, 30000);
        },
    };
}
</script>