<x-layouts.owner>
<div class="page" x-data="{ tab: 'profile' }">

    {{-- TOP BAR --}}
    <div class="top-bar mb-6">
        <div>
            <h1 class="title text-indigo-900">Profile</h1>
            <p class="text-sm text-gray-500">Manage your profile</p>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-6">

        {{-- LEFT COLUMN --}}
        <div class="flex flex-col gap-4 w-56 shrink-0">

            {{-- AVATAR CARD --}}
            <div class="card flex flex-col items-center py-6">
                <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-user text-2xl text-indigo-400"></i>
                </div>
                <p class="font-semibold text-indigo-900">
                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ auth()->user()->email }}</p>
            </div>

            {{-- BUSINESS STATUS CARD --}}
            <div class="card">
                <h3 class="font-semibold text-indigo-900 mb-3">Business Status</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Total Listings</span>
                        <span class="font-medium text-indigo-900">
                            {{ auth()->user()->locations()->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Total Bookings</span>
                        <span class="font-medium text-indigo-900">00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Members</span>
                        <span class="font-medium text-indigo-900">00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Revenue</span>
                        <span class="font-medium text-indigo-900">00</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="flex-1">

            {{-- TABS --}}
            <div class="flex gap-2 mb-4">
                <button @click="tab = 'profile'"
                    :class="tab === 'profile' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                    class="px-5 py-1.5 rounded-lg text-sm transition">
                    Profile
                </button>
                <button @click="tab = 'payment'"
                    :class="tab === 'payment' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                    class="px-5 py-1.5 rounded-lg text-sm transition">
                    Payment
                </button>
                <button @click="tab = 'settings'"
                    :class="tab === 'settings' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                    class="px-5 py-1.5 rounded-lg text-sm transition">
                    Settings
                </button>
            </div>

            {{-- PROFILE TAB --}}
            <div x-show="tab === 'profile'" class="card">
                <h3 class="font-semibold text-indigo-900 mb-4">Profile Information</h3>

                <form action="{{ route('owner.profile.update') }}" method="POST">
                    @csrf
                    <div class="space-y-4">

                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Name</label>
                            <div class="flex gap-3">
                                <input type="text" name="first_name"
                                    value="{{ auth()->user()->first_name }}"
                                    placeholder="First name"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('first_name') border-red-400 @enderror">
                                <input type="text" name="last_name"
                                    value="{{ auth()->user()->last_name }}"
                                    placeholder="Last name"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Business Name</label>
                            <input type="text" name="business_name"
                                value="{{ auth()->user()->business_name ?? '' }}"
                                placeholder="Your business name"
                                 class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Email</label>
                            <input type="email"
                                value="{{ auth()->user()->email }}"
                                disabled
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Phone</label>
                            <input type="text" name="phone"
                                value="{{ auth()->user()->phone ?? '' }}"
                                placeholder="Your phone number"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                    </div>

                    <div class="flex gap-3 mt-6 justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-900 text-white rounded-lg text-sm hover:bg-indigo-800 transition">
                            Update
                        </button>
                        <a href="{{ route('owner.listings') }}"
                            class="px-6 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            {{-- PAYMENT TAB --}}
            <div x-show="tab === 'payment'" class="card space-y-6">
                <h3 class="font-semibold text-indigo-900">Payment Information</h3>

                {{-- Bank Details --}}
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Bank Account</p>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Account Holder Name</label>
                            <input type="text" placeholder="Full name on account"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Bank Name</label>
                                <input type="text" placeholder="e.g. HBL, Meezan"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Account Number</label>
                                <input type="text" placeholder="IBAN or account number"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Mobile Wallet --}}
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Mobile Wallet</p>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500 mb-1 block">Wallet Type</label>
                            <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select wallet</option>
                                <option>JazzCash</option>
                                <option>EasyPaisa</option>
                                <option>NayaPay</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-500 mb-1 block">Wallet Number</label>
                            <input type="text" placeholder="03xx-xxxxxxx"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button class="px-6 py-2 bg-indigo-900 text-white rounded-lg text-sm hover:bg-indigo-800 transition">
                        Save Payment Info
                    </button>
                </div>
            </div>
            {{-- SETTINGS TAB  --}}
            <div x-show="tab === 'settings'" class="space-y-4"
                 x-data="{
                    notifications: true,
                    emailAlerts: true,
                    smsAlerts: false,
                    twoFactor: false,
                    profileVisible: true
                 }">

                {{-- NOTIFICATIONS --}}
                <div class="card">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-4">Notifications</p>
                    <div class="space-y-4">

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Push Notifications</p>
                                <p class="text-xs text-gray-400">Receive booking alerts in app</p>
                            </div>
                            <button @click="notifications = !notifications"
                                :class="notifications ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200">
                                <span :class="notifications ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Email Alerts</p>
                                <p class="text-xs text-gray-400">Get booking confirmations via email</p>
                            </div>
                            <button @click="emailAlerts = !emailAlerts"
                                :class="emailAlerts ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200">
                                <span :class="emailAlerts ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">SMS Alerts</p>
                                <p class="text-xs text-gray-400">Receive SMS for new bookings</p>
                            </div>
                            <button @click="smsAlerts = !smsAlerts"
                                :class="smsAlerts ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200">
                                <span :class="smsAlerts ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- PREFERENCES --}}
                <div class="card">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-4">Preferences</p>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Theme</label>
                            <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option>Light Mode</option>
                                <option>Dark Mode</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Language</label>
                            <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option>English</option>
                                <option>Urdu</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Timezone</label>
                            <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select timezone</option>
                                <option>Asia/Karachi (PKT)</option>
                                <option>Asia/Dubai (GST)</option>
                                <option>Europe/London (GMT)</option>
                                <option>America/New_York (EST)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECURITY --}}
                <div class="card">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-4">Security</p>
                    <div class="space-y-4">

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Two-Factor Verification</p>
                                <p class="text-xs text-gray-400">Add extra security to your account</p>
                            </div>
                            <button @click="twoFactor = !twoFactor"
                                :class="twoFactor ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200">
                                <span :class="twoFactor ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Public Profile Visibility</p>
                                <p class="text-xs text-gray-400">Allow photographers to see your profile</p>
                            </div>
                            <button @click="profileVisible = !profileVisible"
                                :class="profileVisible ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200">
                                <span :class="profileVisible ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- DANGER ZONE --}}
                <div class="card border border-red-100">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-4">Danger Zone</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600">Delete Account</p>
                            <p class="text-xs text-gray-400">Permanently delete your account and all data</p>
                        </div>
                        <button onclick="return confirm('Are you sure? This cannot be undone.')"
                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg text-sm hover:bg-red-50 transition">
                            Delete Account
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</x-layouts.owner>