{{--
    Auto-saving notification toggles.
    Usage: <x-profileComponents.notifications prefix="admin" />
            <x-profileComponents.notifications prefix="owner" />
            <x-profileComponents.notifications prefix="customer" />
    Requires route: {prefix}.profile.notifications
--}}
@props(['prefix'])

@php
    $routeName = $prefix . '.profile.notifications';
    $notificationsUrl = Route::has($routeName) ? route($routeName) : null;
@endphp

<div
    class="shade card chart-transition bg-[#EEEFF7] rounded-2xl border-l-3 border-indigo-400 p-4"
    x-data="{
        push: {{ auth()->user()->notif_push ? 'true' : 'false' }},
        email: {{ auth()->user()->notif_email ? 'true' : 'false' }},
        sms: {{ auth()->user()->notif_sms ? 'true' : 'false' }},
        saved: false,
        error: false,
        url: @js($notificationsUrl),

        save() {
            if (!this.url) {
                console.error('Notifications route not defined for prefix: {{ $prefix }}');
                return;
            }

            fetch(this.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({
                    notif_push:  this.push  ? 1 : 0,
                    notif_email: this.email ? 1 : 0,
                    notif_sms:   this.sms   ? 1 : 0,
                }),
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                // Reflect the server's persisted state so the UI is always truthful.
                this.push  = !!data.notif_push;
                this.email = !!data.notif_email;
                this.sms   = !!data.notif_sms;
                this.saved = true;
                setTimeout(() => { this.saved = false; }, 2000);
            })
            .catch(err => {
                console.error('Failed to save notifications:', err);
                this.error = true;
                setTimeout(() => { this.error = false; }, 3000);
            });
        }
    }"
>
    <p class="text-xs font-semibold text-indigo-900 uppercase tracking-wide mb-3">Notifications</p>

    <div class="space-y-2">

        <div class="flex items-center justify-between py-2">
            <div>
                <p class="font-medium text-indigo-900">Push Notifications</p>
                <p class="text-xs text-gray-500">Receive booking alerts in app</p>
            </div>
            <button type="button" @click="push = !push; save()"
                :class="push ? 'bg-indigo-900' : 'bg-gray-300'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition">
                <span :class="push ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
            </button>
        </div>

        <div class="flex items-center justify-between py-2">
            <div>
                <p class="font-medium text-indigo-900">Email Alerts</p>
                <p class="text-xs text-gray-500">Get booking confirmations via email</p>
            </div>
            <button type="button" @click="email = !email; save()"
                :class="email ? 'bg-indigo-900' : 'bg-gray-300'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition">
                <span :class="email ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
            </button>
        </div>

        <div class="flex items-center justify-between py-2">
            <div>
                <p class="font-medium text-indigo-900">SMS Alerts</p>
                <p class="text-xs text-gray-500">Receive SMS for new bookings</p>
            </div>
            <button type="button" @click="sms = !sms; save()"
                :class="sms ? 'bg-indigo-900' : 'bg-gray-300'"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition">
                <span :class="sms ? 'translate-x-6' : 'translate-x-1'"
                    class="inline-block h-4 w-4 transform rounded-full bg-white transition"></span>
            </button>
        </div>

        <p x-show="saved" x-transition class="text-xs text-green-600 mt-2">Saved successfully.</p>
        <p x-show="error" x-transition class="text-xs text-red-600 mt-2">Couldn't save, please try again.</p>
    </div>
</div>