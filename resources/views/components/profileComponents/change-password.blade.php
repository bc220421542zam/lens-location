@props(['route' => 'owner.profile.password'])

{{-- Change Password --}}
    <div class="card chart-transition bg-[#EEEFF7] rounded-2xl border-l-3 border-indigo-400 p-6">
        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Change Password</p>
        <p class="text-xs text-gray-400 mb-4">Update your password to keep your account secure</p>

        <form action="{{ route($route) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

                {{-- Current Password --}}
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Current Password</label>
                    <div class="relative text-gray-400">
                    <input type="password" name="current_password" id="s_current_pw"
                        placeholder="Current password"
                        class="w-full border input input-field rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
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
                                class="w-full border input input-field rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
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
                                    class="w-full border input input-field border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <button type="button" onclick="togglePw('s_confirm_pw','s_eye3')"
                                    class="absolute right-3 top-2.5 text-indigo-400 hover:text-indigo-700">
                                    <i id="s_eye3" class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </div>
                            <p id="s_match_text" class="text-xs mt-1" style="color:transparent">Passwords match</p>
                        </div>

                    </div>

                    <x-profileComponents.password-rules/>

                    <div class="flex justify-end gap-3">
                        <button type="reset"
                            class="px-5 py-2 border bg-white btn-transition border-gray-200 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                            Clear
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-900 text-white btn-transition rounded-lg text-sm hover:bg-indigo-800 transition">
                            Update Password
                        </button>
                    </div>

                </form>
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