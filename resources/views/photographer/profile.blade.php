<x-layouts.photographer>
@php $activeTab = session('tab', 'personal'); @endphp
<div class="page" x-data="{ tab: '{{ $activeTab }}' }">

    <h1 class="title text-indigo-900">Profile</h1>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    {{-- PHOTO --}}
    <div class="card mb-4 flex items-center gap-4">
        <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 shrink-0">
            @if (auth()->user()->profile_picture)
                <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}"
                     alt="" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                    <i class="fa-solid fa-user text-2xl text-indigo-500"></i>
                </div>
            @endif
        </div>
        <div class="flex-1">
            <p class="font-semibold text-indigo-900">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('photographer.profile.photo') }}"
                  enctype="multipart/form-data" class="mt-2 flex items-center gap-2">
                @csrf
                <input type="file" name="profile_picture" accept="image/*"
                       class="text-xs" required>
                <button type="submit" class="action-btn publish">Update</button>
                @error('profile_picture') <p class="error">{{ $message }}</p> @enderror
            </form>
        </div>
    </div>

    {{-- TABS --}}
    <div class="flex gap-2 mb-4">
        <button @click="tab = 'personal'"
                :class="tab === 'personal' ? 'bg-indigo-900 text-white' : ''"
                class="action-btn">Personal Info</button>
        <button @click="tab = 'password'"
                :class="tab === 'password' ? 'bg-indigo-900 text-white' : ''"
                class="action-btn">Password</button>
    </div>

    {{-- PERSONAL --}}
    <div x-show="tab === 'personal'">
        <form method="POST" action="{{ route('photographer.profile.update') }}" class="card flex flex-col gap-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">First Name</label>
                    <input type="text" name="first_name"
                           value="{{ old('first_name', auth()->user()->first_name) }}"
                           class="input @error('first_name') border-red-400 @enderror">
                    @error('first_name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Last Name</label>
                    <input type="text" name="last_name"
                           value="{{ old('last_name', auth()->user()->last_name) }}"
                           class="input @error('last_name') border-red-400 @enderror">
                    @error('last_name') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Phone</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', auth()->user()->phone) }}"
                           class="input @error('phone') border-red-400 @enderror">
                    @error('phone') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled
                           class="input bg-gray-100">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn w-auto px-6">Save Changes</button>
            </div>
        </form>
    </div>

    {{-- PASSWORD --}}
    <div x-show="tab === 'password'">
        <x-change-password route="photographer.profile.password" />
    </div>

</div>
</x-layouts.photographer>
