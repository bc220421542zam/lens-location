<x-layouts.admin>
<div class="page" x-data="{ tab: '{{ session('tab', 'profile') }}' }">

    <x-topbar 
    title="My Profile"
    description="Manage your profile information and account settings"
    ></x-topbar>

    {{--Flash-Message--}}
    <x-profileComponents.flash-alert/>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT COLUMN --}}
        <div class="flex flex-col gap-4 w-full lg:w-56 lg:shrink-0">

            {{-- Profil PHOTO --}}
            <form action="{{ route('admin.profile.photo') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="file" id="profile_pic_trigger" name="profile_picture"
                    class="hidden" accept="image/*" onchange="this.form.submit()">
                <x-profileComponents.profile-photo/>
            </form>
            {{-- ACTION BUTTONS --}}
            <x-profileComponents.profile-action-btns/>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="flex-1 min-w-0">

            {{-- PROFILE Info --}}
            <div x-show="tab === 'profile'" class="card chart-transition bg-[#EEEFF7] rounded-2xl p-6">
                <h3 class="font-semibold text-indigo-900 mb-4">Profile Information</h3>

        <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        <div class="space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- First Name --}}    
            <div>
                <label class="text-xs text-gray-500 mb-1 block">First Name</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="first_name"
                            value="{{ old('first_name', auth()->user()->first_name) }}"
                            placeholder="First name"
                            class="w-full input input-field focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('first_name') border-red-400 @enderror">
                    </div>
                    @error('first_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            {{-- Last Name --}}
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Last Name</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="last_name"
                            value="{{ old('last_name', auth()->user()->last_name) }}"
                            placeholder="Last name"
                            class="w-full input input-field focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('last_name') border-red-400 @enderror">
                    </div>
                    @error('last_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">    
            {{--Business name--}}
                 <div>
                    <label class="text-xs text-gray-500 mb-1 block">Business Name</label>
                        <input type="text" name="business_name"
                            value="{{ old('business_name', auth()->user()->business_name ?? '') }}"
                            placeholder="Your business name"
                            class="w-full input input-field focus:outline-none focus:ring-2 focus:ring-indigo-300">
                </div>
                {{--country--}}
                <x-profileComponents.country/>
        </div>
            {{--Phone--}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Phone</label>
                <input type="text" name="phone"
                    value="{{ old('phone', auth()->user()->phone ?? '') }}"
                    placeholder="Your phone number"
                    class="w-full input input-field focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            {{--gender--}}
                <x-profileComponents.gender/>
        </div>
        {{--Email--}}
            <div>
                <label class="text-xs text-gray-500 mb-1 block">Email</label>
                    <input type="email"
                        value="{{ auth()->user()->email }}"
                        disabled
                        class="w-full input input-field focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-100 cursor-not-allowed">
            </div>
        </div>
            {{--Buttons--}}
                    <div class="flex flex-wrap gap-3 mt-6 justify-end">
                        <a href="{{ route('admin.listings') }}"
                            class="px-6 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-100 transition btn-transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-900 text-white rounded-lg text-sm hover:bg-indigo-800 transition btn-transition">
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
            <x-profileComponents.notifications prefix="admin" />
        {{--Account Details--}}
            <x-profileComponents.account-details/>     
        {{--Danger Zone --}}
            <x-profileComponents.danger-zone/>

            </div>
            {{-- CHANGE PASSWORD TAB --}}
            <div x-show="tab === 'change-password'">
                <x-profileComponents.change-password route="admin.profile.password"/>
            </div>
        </div>
    </div>
</div>

</x-layouts.admin>