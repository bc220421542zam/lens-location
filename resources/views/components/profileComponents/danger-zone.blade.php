{{-- DANGER ZONE --}}
    <div class="card border border-red-100 bg-red-20 rounded-2xl p-4 chart-transition">
        <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-4">Danger Zone</p>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-red-600">Delete Account</p>
                    <p class="text-xs text-gray-400">Permanently delete your account and all data</p>
                </div>
                <button onclick="return confirm('Are you sure? This cannot be undone.')"
                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm hover:bg-red-50 transition shrink-0">
                    Delete Account
                </button>
            </div>
    </div>