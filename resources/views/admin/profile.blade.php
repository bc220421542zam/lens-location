<x-layouts.admin>

    <div class="max-w-lg">

        <p class="title text-indigo-900">Profile</p>

        {{-- Success message --}}
        @if(session('success'))
            <div class="mb-4 text-green-600 text-sm">{{ session('success') }}</div>
        @endif

        <div class="shade bg-[#EEEFF7] p-6 rounded-2xl">

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf

                {{-- PROFILE PICTURE --}}
                <div class="flex items-center gap-6 mb-6">

                    <img src="{{ auth()->user()->profile_picture
                        ? asset('storage/' . auth()->user()->profile_picture)
                        : asset('images/default-avatar.png') }}"
                        alt="Profile Picture"
                        class="w-28 h-28 rounded-lg object-cover shade">

                    <div>
                        <p class="text-indigo-900 font-semibold mb-3">Profile Picture</p>
                        <div class="flex gap-2">
                            <label for="profile_picture"
                                class="cursor-pointer bg-indigo-700 hover:bg-indigo-900 text-white px-4 py-2 rounded-lg text-sm">
                                Upload
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                        </div>
                        @error('profile_picture')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- NAME fields --}}
                <div>
                    <label class="label">First Name</label>
                    <input type="text" name="first_name" value="{{ auth()->user()->first_name }}"
                        placeholder="First name" class="input">
                </div>

                <div>
                    <label class="label">Last Name</label>
                    <input type="text" name="last_name" value="{{ auth()->user()->last_name }}"
                        placeholder="Last name" class="input">
                </div>

            
                {{-- PHONE --}}
                <div>
                    <label class="label">Phone</label>
                    <input type="text" name="phone" value="{{ auth()->user()->phone }}"
                        placeholder="+555 000 0000"
                        class="input @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                </div>
                {{-- BUTTONS --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-indigo-700 hover:bg-indigo-900 text-white px-6 py-2 rounded-lg text-sm">
                        Update
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg text-sm">
                        Cancel
                    </a>
                </div>

            </form>

        </div>

    </div>

</x-layouts.admin>