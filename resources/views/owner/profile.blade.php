<x-layouts.owner>
<div class="page" x-data="{ tab: 'profile' }">

    {{-- TOP BAR --}}
    <div class="top-bar mb-6">
        <div>
            <h1 class="title text-indigo-900">Profile</h1>
            <p class="text-sm text-gray-500">Manage your profile</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT COLUMN --}}
        <div class="flex flex-col gap-4 w-full lg:w-56 lg:shrink-0">

            {{-- PHOTO UPLOAD --}}
            <form action="{{ route('owner.profile.photo') }}" method="POST" 
                enctype="multipart/form-data">
                @csrf
                <input type="file" id="profile_pic_trigger" name="profile_picture"
                    class="hidden" accept="image/*" onchange="this.form.submit()">

                <div class="card flex flex-col items-center py-6">

                    <div class="relative w-20 h-20 mb-3">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                alt="Profile"
                                class="w-20 h-20 rounded-full object-cover border-2 border-indigo-200">
                        @else
                            <div class="w-20 h-20 rounded-full bg-indigo-100 border-2 border-indigo-200 flex items-center justify-center">
                                <i class="fa-solid fa-user text-3xl text-indigo-400"></i>
                            </div>
                        @endif

                        {{-- Camera overlay --}}
                        <label for="profile_pic_trigger"
                            class="absolute bottom-0 right-0 w-6 h-6 bg-indigo-900 rounded-full flex items-center justify-center cursor-pointer hover:bg-[#2C3399] transition">
                            <i class="fa-solid fa-camera text-white" style="font-size:10px"></i>
                        </label>
                    </div>

                    <p class="font-semibold text-indigo-900 text-center">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 text-center">
                        {{ auth()->user()->email }}
                    </p>
                </div>
            </form>

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
        <div class="flex-1 min-w-0">

            {{-- TABS --}}
            <div class="flex gap-2 mb-4 flex-wrap">
                <button @click="tab = 'profile'"
                    :class="tab === 'profile' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                    class="px-10 py-2 rounded-lg text-sm transition">
                    Profile
                </button>
                <button @click="tab = 'payment'"
                    :class="tab === 'payment' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                    class="px-10 py-2 rounded-lg text-sm transition">
                    Payment
                </button>
                <button @click="tab = 'settings'"
                    :class="tab === 'settings' ? 'bg-indigo-900 text-white' : 'bg-[#EEEFF7] text-indigo-900 border border-indigo-200'"
                    class="px-10 py-2 rounded-lg text-sm transition">
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
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="text" name="first_name"
                                    value="{{ old('first_name', auth()->user()->first_name) }}"
                                    placeholder="First name"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('first_name') border-red-400 @enderror">
                                <input type="text" name="last_name"
                                    value="{{ old('last_name', auth()->user()->last_name) }}"
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
                                value="{{ old('business_name', auth()->user()->business_name ?? '') }}"
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
                                value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                placeholder="Your phone number"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                    </div>

                    <div class="flex flex-wrap gap-3 mt-6 justify-end">
                        <a href="{{ route('owner.listings') }}"
                            class="px-6 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-900 text-white rounded-lg text-sm hover:bg-indigo-800 transition">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            {{-- PAYMENT TAB --}}
            <div x-show="tab === 'payment'" class="card space-y-6">
                <h3 class="font-semibold text-indigo-900">Payment Information</h3>

                <form action="{{ route('owner.profile.payment') }}" method="POST">
                    @csrf

                    {{-- Bank Account --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Bank Account</p>
                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Account Holder Name</label>
                                <input type="text" name="account_holder"
                                    value="{{ old('account_holder', $payment->account_holder ?? '') }}"
                                    placeholder="Full name on account"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-1 block">Bank Name</label>
                                    <input type="text" name="bank_name"
                                        value="{{ old('bank_name', $payment->bank_name ?? '') }}"
                                        placeholder="e.g. HBL, Meezan"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                </div>
                                <div class="flex-1">
                                    <label class="text-xs text-gray-500 mb-1 block">Account Number</label>
                                    <input type="text" name="account_number"
                                        value="{{ old('account_number', $payment->account_number ?? '') }}"
                                        placeholder="IBAN or account number"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-4">

                    {{-- Mobile Wallet --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Mobile Wallet</p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Wallet Type</label>
                                <select name="wallet_type"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    <option value="">Select wallet</option>
                                    <option {{ old('wallet_type', $payment->wallet_type ?? '') == 'JazzCash'  ? 'selected' : '' }}>JazzCash</option>
                                    <option {{ old('wallet_type', $payment->wallet_type ?? '') == 'EasyPaisa' ? 'selected' : '' }}>EasyPaisa</option>
                                    <option {{ old('wallet_type', $payment->wallet_type ?? '') == 'NayaPay'   ? 'selected' : '' }}>NayaPay</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">Wallet Number</label>
                                <input type="text" name="wallet_number"
                                    value="{{ old('wallet_number', $payment->wallet_number ?? '') }}"
                                    placeholder="03xx-xxxxxxx"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-900 text-white rounded-lg text-sm hover:bg-indigo-800 transition">
                            Save Payment Info
                        </button>
                    </div>

                </form>
            </div>

            {{-- SETTINGS TAB --}}
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
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Push Notifications</p>
                                <p class="text-xs text-gray-400">Receive booking alerts in app</p>
                            </div>
                            <button @click="notifications = !notifications"
                                :class="notifications ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="notifications ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Email Alerts</p>
                                <p class="text-xs text-gray-400">Get booking confirmations via email</p>
                            </div>
                            <button @click="emailAlerts = !emailAlerts"
                                :class="emailAlerts ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="emailAlerts ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">SMS Alerts</p>
                                <p class="text-xs text-gray-400">Receive SMS for new bookings</p>
                            </div>
                            <button @click="smsAlerts = !smsAlerts"
                                :class="smsAlerts ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
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
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
        {{--change password--}}
    <div class="card bg-[#EEEFF7]">
        <x-change-password
            title="Change Password"
            subtitle="Update your password to keep your account secure"
            action="{{ route('owner.profile.password') }}"
            prefix="owner"
        />
    </div>

                {{-- DANGER ZONE --}}
                <div class="card border border-red-100">
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

            </div>
        </div>
    </div>
</div>
</x-layouts.owner>