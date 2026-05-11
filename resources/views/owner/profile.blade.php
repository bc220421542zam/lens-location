<x-layouts.owner>
<div class="page" x-data="{ tab: 'profile' }">

    <x-topbar 
    title="My Profile"
    description="Manage your profile information and account settings"
>
</x-topbar>

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
        <x-notifications/>       
        {{--change password--}}
        <x-change-password/>
        {{--Danger Zone --}}
        <x-danger-zone/>

            </div>
        </div>
    </div>
</div>
</x-layouts.owner>