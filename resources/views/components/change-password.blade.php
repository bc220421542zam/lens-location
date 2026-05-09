{{-- Change Password --}}
    <div class="card bg-[#EEEFF7]">
        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Change Password</p>
        <p class="text-xs text-gray-400 mb-4">Update your password to keep your account secure</p>

        <form action="{{ route('owner.profile.password') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

                {{-- Current Password --}}
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Current Password</label>
                    <div class="relative text-gray-400">
                    <input type="password" name="current_password" id="s_current_pw"
                        placeholder="Current password"
                        class="w-full border rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                        {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-200' }}">
                            <button type="button" onclick="togglePw('s_current_pw','s_eye1')"
                                class="absolute right-3 top-2.5 text-indigo-400 hover:text-indigo-700">
                                <i id="s_eye1" class="fa-regular fa-eye text-sm"></i>
                            </button>
                    </div>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">New Password</label>
                        <div class="relative text-gray-400">
                            <input type="password" name="new_password" id="s_new_pw"
                                placeholder="New password"
                                oninput="checkStrength(this.value)"
                                class="w-full border rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                {{ $errors->has('new_password') ? 'border-red-400' : 'border-gray-200' }}">
                            <button type="button" onclick="togglePw('s_new_pw','s_eye2')"
                                class="absolute right-3 top-2.5 text-indigo-400 hover:text-indigo-700">
                                <i id="s_eye2" class="fa-regular fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p id="s_strength_text" class="text-xs mt-1 text-gray-400">Enter a password</p>
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Confirm New Password</label>
                            <div class="relative text-gray-400">
                                <input type="password" name="new_password_confirmation" id="s_confirm_pw"
                                    placeholder="Confirm new password"
                                    oninput="checkMatch()"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <button type="button" onclick="togglePw('s_confirm_pw','s_eye3')"
                                    class="absolute right-3 top-2.5 text-indigo-400 hover:text-indigo-700">
                                    <i id="s_eye3" class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </div>
                            <p id="s_match_text" class="text-xs mt-1" style="color:transparent">Passwords match</p>
                        </div>

                    </div>

                    <x-password-rules/>

                    <div class="flex justify-end gap-3">
                        <button type="reset"
                            class="px-5 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                            Clear
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-900 text-white rounded-lg text-sm hover:bg-indigo-800 transition">
                            Update Password
                        </button>
                    </div>

                </form>
            </div>