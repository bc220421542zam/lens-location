<div class="shade card card-transition bg-[#EEEFF7] rounded-2xl p-4">
    <p class="text-xs font-semibold text-indigo-900 uppercase tracking-wide mb-3">Actions</p>
        <div class="space-y-2">
            <button @click="tab = 'profile'"
                :class="tab === 'profile' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                class="px-10 w-full py-2 rounded-lg text-sm transition btn-transition">
                Profile
            </button>
            <button @click="tab = 'change-password'"
                :class="tab === 'change-password' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                class="px-8 w-full py-2 rounded-lg text-sm transition btn-transition">
                Change Password
            </button>
            <button @click="tab = 'settings'"
                :class="tab === 'settings' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                class="px-10 w-full py-2 rounded-lg text-sm transition btn-transition">
                Settings
            </button>    
        </div>
</div>