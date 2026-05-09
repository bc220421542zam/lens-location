<x-layouts.admin>
@php $activeTab = session('tab', 'personal'); @endphp
<div x-data="{ tab: '{{ $activeTab }}' }">

    <p class="title text-indigo-900">Profile</p>

    {{-- Success message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- MAIN LAYOUT --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- LEFT COLUMN --}}
        <div class="flex flex-col gap-4 w-full lg:w-56 lg:shrink-0">

            {{-- PHOTO UPLOAD --}}
            <form action="{{ route('admin.profile.photo') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="file" id="admin_pic_trigger" name="profile_picture"
                    class="hidden" accept="image/*" onchange="this.form.submit()">

                <div class="shade bg-[#EEEFF7] rounded-2xl p-5 flex flex-col items-center">

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

                        <label for="admin_pic_trigger"
                            class="absolute bottom-0 right-0 w-6 h-6 bg-indigo-900 rounded-full flex items-center justify-center cursor-pointer hover:bg-[#2C3399] transition">
                            <i class="fa-solid fa-camera text-white" style="font-size:10px"></i>
                        </label>
                    </div>

                    <p class="font-semibold text-indigo-900 text-center text-sm">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 text-center">
                        {{ auth()->user()->email }}
                    </p>
                    <span class="mt-2 text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">
                        Admin
                    </span>
                </div>
            </form>

            {{-- NAV MENU --}}
            <div class="shade bg-[#EEEFF7] rounded-2xl p-3 flex flex-col gap-1">

                <button @click="tab = 'personal'"
                    :class="tab === 'personal' ? 'bg-[#2C3399] text-white' : 'text-indigo-800 hover:bg-indigo-100'"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm transition text-left">
                    <i class="fa-solid fa-user w-4 text-center"></i>
                    Personal Info
                </button>

                <button @click="tab = 'password'"
                    :class="tab === 'password' ? 'bg-[#2C3399] text-white' : 'text-indigo-800 hover:bg-indigo-100'"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm transition text-left">
                    <i class="fa-solid fa-lock w-4 text-center"></i>
                    Change Password
                </button>

                <button @click="tab = 'notifications'"
                    :class="tab === 'notifications' ? 'bg-[#2C3399] text-white' : 'text-indigo-800 hover:bg-indigo-100'"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm transition text-left">
                    <i class="fa-regular fa-bell w-4 text-center"></i>
                    Notifications
                </button>

            </div>

            {{-- ACCOUNT DETAILS --}}
            <div class="shade bg-[#EEEFF7] rounded-2xl p-4">
                <p class="text-xs font-semibold text-indigo-900 uppercase tracking-wide mb-3">Account Details</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Status</span>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Active</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Member since</span>
                        <span class="text-xs text-indigo-900 font-medium">
                            {{ auth()->user()->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="flex-1 min-w-0">

            {{-- PERSONAL INFO TAB --}}
            <div x-show="tab === 'personal'">
                <div class="shade bg-[#EEEFF7] p-6 rounded-2xl">
                    <h3 class="font-semibold text-indigo-900 mb-1">Personal Information</h3>
                    <p class="text-xs text-gray-400 mb-5">Update your personal details</p>

                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                            <div>
                                <label class="label">First Name</label>
                                <input type="text" name="first_name"
                                    value="{{ old('first_name', auth()->user()->first_name) }}"
                                    placeholder="First name"
                                    class="input @error('first_name') border-red-500 @enderror">
                                @error('first_name')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="label">Last Name</label>
                                <input type="text" name="last_name"
                                    value="{{ old('last_name', auth()->user()->last_name) }}"
                                    placeholder="Last name"
                                    class="input">
                            </div>

                            <div>
                                <label class="label">Email</label>
                                <input type="email"
                                    value="{{ auth()->user()->email }}"
                                    disabled
                                    class="input bg-gray-100 text-gray-400 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="label">Phone</label>
                                <input type="text" name="phone"
                                    value="{{ old('phone', auth()->user()->phone) }}"
                                    placeholder="+92 300 0000000"
                                    class="input @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('admin.dashboard') }}"
                                class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg text-sm">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-indigo-900 hover:bg-[#2C3399] text-white px-6 py-2 rounded-lg text-sm">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- CHANGE PASSWORD TAB --}}
            <div x-show="tab === 'password'">
                <div class="shade bg-[#EEEFF7] p-6 rounded-2xl">
                    <h3 class="font-semibold text-indigo-900 mb-1">Change Password</h3>
                    <p class="text-xs text-gray-400 mb-5">Choose a strong password to keep your account secure</p>

                    <x-change-password route="admin.profile.password" />
                </div>
            </div>

            {{-- NOTIFICATIONS TAB --}}
            <div x-show="tab === 'notifications'"
                x-data="{
                    newBooking: true,
                    newUser: true,
                    newListing: true,
                    dispute: false,
                    review: false,
                    saved: false
                }">
                <div class="shade bg-[#EEEFF7] p-6 rounded-2xl">
                    <h3 class="font-semibold text-indigo-900 mb-1">Notification Preferences</h3>
                    <p class="text-xs text-gray-400 mb-5">Choose what you want to be notified about</p>

                    {{-- Success message --}}
                    <div x-show="saved" x-transition
                        class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                        Notification preferences saved.
                    </div>

                    <div class="space-y-4">

                        <div class="flex items-center justify-between gap-4 pb-3 border-b border-indigo-200">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">New Booking Alerts</p>
                                <p class="text-xs text-gray-400">Notify when a new booking is made</p>
                            </div>
                            <button @click="newBooking = !newBooking"
                                :class="newBooking ? 'bg-indigo-900' : 'bg-gray-300'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="newBooking ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between gap-4 pb-3 border-b border-indigo-200">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">New User Registration</p>
                                <p class="text-xs text-gray-400">Notify when a new user registers</p>
                            </div>
                            <button @click="newUser = !newUser"
                                :class="newUser ? 'bg-indigo-900' : 'bg-gray-300'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="newUser ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between gap-4 pb-3 border-b border-indigo-200">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">New Listing Submitted</p>
                                <p class="text-xs text-gray-400">Notify when owner submits a listing</p>
                            </div>
                            <button @click="newListing = !newListing"
                                :class="newListing ? 'bg-indigo-900' : 'bg-gray-300'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="newListing ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between gap-4 pb-3 border-b border-indigo-200">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Dispute Raised</p>
                                <p class="text-xs text-gray-400">Notify when a dispute is reported</p>
                            </div>
                            <button @click="dispute = !dispute"
                                :class="dispute ? 'bg-indigo-900' : 'bg-gray-300'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="dispute ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                            </button>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Review Posted</p>
                                <p class="text-xs text-gray-400">Notify when a new review is submitted</p>
                            </div>
                            <button @click="review = !review"
                                :class="review ? 'bg-indigo-900' : 'bg-gray-300'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="review ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                            </button>
                        </div>

                    </div>

                    <div class="flex justify-end mt-6">
                        <button @click="saved = true; setTimeout(() => saved = false, 3000)"
                            class="bg-indigo-900 hover:bg-[#2C3399] text-white px-6 py-2 rounded-lg text-sm">
                            Save Preferences
                        </button>
                    </div>
                </div>
            </div>

            {{-- ACTIVITY LOG TAB --}}
            <div x-show="tab === 'activity'">
                <div class="shade bg-[#EEEFF7] p-6 rounded-2xl">
                    <h3 class="font-semibold text-indigo-900 mb-1">Recent Activity</h3>
                    <p class="text-xs text-gray-400 mb-5">Your recent actions on the platform</p>

                    <div class="space-y-1">

                        <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-indigo-50 transition">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-user text-indigo-600 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-900">Profile updated</p>
                                <p class="text-xs text-gray-400">Personal information was changed</p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">Today</span>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-indigo-50 transition">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-check text-green-600 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-900">Listing approved</p>
                                <p class="text-xs text-gray-400">Studio Downtown was approved</p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">Today</span>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-indigo-50 transition">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-ban text-red-500 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-900">User blocked</p>
                                <p class="text-xs text-gray-400">abc@gmail.com was blocked</p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">Yesterday</span>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-indigo-50 transition">
                            <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-lock text-yellow-600 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-900">Password changed</p>
                                <p class="text-xs text-gray-400">Account password was updated</p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">3 days ago</span>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-indigo-50 transition">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-right-to-bracket text-blue-600 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-900">Logged in</p>
                                <p class="text-xs text-gray-400">Login from Lahore, Pakistan</p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">3 days ago</span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
function togglePw(fieldId, eyeId) {
    const field = document.getElementById(fieldId);
    const eye   = document.getElementById(eyeId);
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function adminStrength(val) {
    const bar  = document.getElementById('a_strength_bar');
    const text = document.getElementById('a_strength_text');
    const rules = {
        length:  val.length >= 8,
        upper:   /[A-Z]/.test(val),
        number:  /[0-9]/.test(val),
        special: /[^A-Za-z0-9]/.test(val),
    };
    document.getElementById('a_rule_len').style.color = rules.length  ? '#16a34a' : '#9ca3af';
    document.getElementById('a_rule_up') .style.color = rules.upper   ? '#16a34a' : '#9ca3af';
    document.getElementById('a_rule_num').style.color = rules.number  ? '#16a34a' : '#9ca3af';
    document.getElementById('a_rule_spe').style.color = rules.special ? '#16a34a' : '#9ca3af';
    const score  = Object.values(rules).filter(Boolean).length;
    if (val.length === 0) { bar.style.width = '0%'; text.textContent = 'Enter a password'; text.style.color = '#9ca3af'; return; }
    const levels = [
        { width: '25%',  color: '#ef4444', label: 'Weak'   },
        { width: '50%',  color: '#f97316', label: 'Fair'   },
        { width: '75%',  color: '#eab308', label: 'Good'   },
        { width: '100%', color: '#16a34a', label: 'Strong' },
    ];
    const level = levels[score - 1] || levels[0];
    bar.style.width      = level.width;
    bar.style.background = level.color;
    text.textContent     = 'Strength: ' + level.label;
    text.style.color     = level.color;
}

function adminMatch() {
    const newPw  = document.getElementById('a_new_pw').value;
    const conPw  = document.getElementById('a_con_pw').value;
    const txt    = document.getElementById('a_match_text');
    if (conPw.length === 0) { txt.style.color = 'transparent'; return; }
    if (newPw === conPw) { txt.textContent = '✓ Passwords match';      txt.style.color = '#16a34a'; }
    else                 { txt.textContent = '✗ Passwords do not match'; txt.style.color = '#ef4444'; }
}
</script>

</x-layouts.admin>