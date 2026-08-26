<x-layout>
<div class="min-h-screen bg-[#DDDEEF] relative overflow-hidden">

    {{-- CENTER WRAPPER --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6">

        {{-- CARD --}}
        <div class="w-full max-w-sm sm:max-w-md lg:max-w-5xl chart-transition
                rounded-2xl shadow-xl bg-white border border-indigo-200 overflow-hidden">

            {{-- MOBILE & TABLET: Image on top --}}
            <div class="relative h-40 sm:h-48 lg:hidden w-full">
                <img src="{{ asset('images/Studio-photography-rental.jpg') }}"
                     alt="Register"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 text-white text-center px-6 pb-4">
                    <h2 class="text-lg sm:text-xl font-bold mb-1">Join LensLocation!</h2>
                    <p class="text-white/80 text-xs leading-relaxed">
                        Create your account and start your journey.
                    </p>
                </div>
            </div>

            {{-- BODY --}}
            <div class="flex flex-col lg:flex-row">

                {{-- LEFT: Image desktop only --}}
                <div class="hidden lg:block lg:w-2/5 relative" style="min-height:580px;">
                    <img src="{{ asset('images/Studio-photography-rental.jpg') }}"
                         alt="Register"
                         class="w-full h-full object-cover absolute inset-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 z-10 text-white text-center px-8 pb-16">
                        <h2 class="text-3xl font-bold mb-2">Join LensLocation!</h2>
                        <p class="text-white/80 text-sm leading-relaxed">
                            Create your account and start booking locations today.
                        </p>
                    </div>
                </div>

                {{-- RIGHT: Form --}}
                <div class="w-full lg:w-3/5 flex items-center justify-center p-6 sm:p-8 lg:p-10">
                    <div class="w-full">

                        {{-- Logo --}}
                        <div class="flex justify-center mb-4">
                            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-8 lg:h-10">
                        </div>

                        <h1 class="text-xl sm:text-2xl font-bold text-center text-indigo-900 mb-1">Create Account</h1>
                        <p class="text-sm text-gray-500 text-center mb-4">Fill in your details to get started</p>

                <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-3" novalidate>
                @csrf
                {{-- First Name--}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name"
                                class="block text-sm font-medium text-indigo-900 mb-1">First Name</label>
                                <input type="text" id="first_name" name="first_name"
                                value="{{ old('first_name') }}"
                                maxlength="255"
                                onblur="validateField(this)"
                                class="field field-transition {{ $errors->has('first_name') ? 'border-red-400' : 'border-indigo-300' }}">
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @else
                                <p class="text-red-500 text-xs mt-1 hidden" data-error="first_name"></p>
                            @enderror
                        </div>
                        {{-- Last Name --}}
                        <div>
                            <label for="last_name"
                                class="block text-sm font-medium text-indigo-900 mb-1">Last Name</label>
                                <input type="text" id="last_name" name="last_name"
                                value="{{ old('last_name') }}"
                                maxlength="255"
                                onblur="validateField(this)"
                                class="field field-transition {{ $errors->has('last_name') ? 'border-red-400' : 'border-indigo-300' }}">
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @else
                                <p class="text-red-500 text-xs mt-1 hidden" data-error="last_name"></p>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-indigo-900 mb-1">Email</label>
                                <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                maxlength="255"
                                onblur="validateField(this)"
                                class="field field-transition {{ $errors->has('email') ? 'border-red-400' : 'border-indigo-300' }}">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @else
                                <p class="text-red-500 text-xs mt-1 hidden" data-error="email"></p>
                            @enderror
                        </div>
                        {{-- Phone --}}
                        <div>
                            <label for="phone"
                                class="block text-sm font-medium text-indigo-900 mb-1">Phone</label>
                                <input type="text" id="phone" name="phone"
                                value="{{ old('phone') }}"
                                class="field field-transition {{ $errors->has('phone') ? 'border-red-400' : 'border-indigo-300' }}">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @else
                                <p class="text-red-500 text-xs mt-1 hidden" data-error="phone"></p>
                            @enderror
                        </div>
                    </div>

                    {{-- Password + Confirm Password --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                           <label for="password"
                                class="block text-sm font-medium text-indigo-900 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                oninput="validateField(document.getElementById('password_confirmation'))"
                                onblur="validateField(this)"
                                class="field field-transition pr-10 {{ $errors->has('password') ? 'border-red-400' : 'border-indigo-300' }}">
                                <button type="button" onclick="togglePassword('password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-700 focus:outline-none">
                                    <svg class="h-4 w-4 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-4 w-4 hidden eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @else
                                <p class="text-red-500 text-xs mt-1 hidden" data-error="password"></p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-indigo-900 mb-1">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation"
                                name="password_confirmation"
                                onblur="validateField(this)"
                                class="field field-transition pr-10 {{ $errors->has('password_confirmation') ? 'border-red-400' : 'border-indigo-300' }}">
                                <button type="button" onclick="togglePassword('password_confirmation', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-700 focus:outline-none">
                                    <svg class="h-4 w-4 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-4 w-4 hidden eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @else
                                <p class="text-red-500 text-xs mt-1 hidden" data-error="password_confirmation"></p>
                            @enderror
                        </div>
                    </div>
              <!-- Role Dropdown -->
           <div class="w-133 mt-5 gap-2 text-indigo-900">
                <label for="role" class="block text-sm font-medium text-indigo-900 mb-1">Role</label>
                <div class="relative">
                    <select id="role" name="role" form="registerForm"
                        onblur="validateField(this)"
                        class="field {{ $errors->has('role') ? 'border-red-400' : 'border-indigo-300' }} field-transition
                            w-full appearance-none pr-8">
                        <option value="">Select Role</option>
                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Location Owner</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @else
                    <p class="text-red-500 text-xs mt-1 hidden" data-error="role"></p>
                @enderror
            </div>

                {{-- Submit --}}
                    <button type="submit"
                        class="w-full bg-indigo-900 text-white py-2.5 rounded-lg font-semibold text-sm
                           hover:bg-indigo-700 transition-colors duration-200">
                        Sign Up
                    </button>

                {{-- Login Link --}}
                    <p class="text-center text-sm text-gray-500">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="text-indigo-800 font-semibold underline hover:text-indigo-900">
                            Login
                        </a>
                    </p>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const eyeOpen = btn.querySelector('.eye-open');
    const eyeClosed = btn.querySelector('.eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

function showError(field, msg) {
    const el = field.closest('div').querySelector('[data-error="' + field.name + '"]');
    field.classList.add('border-red-400');
    if (el) { el.textContent = msg; el.classList.remove('hidden'); }
}

function clearError(field) {
    const el = field.closest('div').querySelector('[data-error="' + field.name + '"]');
    field.classList.remove('border-red-400');
    if (el) { el.textContent = ''; el.classList.add('hidden'); }
}

function getField(field) {
    return document.getElementById(field.name);
}

function updatePhoneCode() {
    const code = document.getElementById('phone_code').value;
    const phone = document.getElementById('phone');
    let num = phone.value.replace(/^\+\d+\s*/, '');
    phone.value = code ? code + num : num;
    validateField(phone);
}

function validateField(field) {
    const val = field.value.trim();
    if (!val && field.type !== 'hidden') {
        showError(field, 'This field is required.');
        return false;
    }

    switch (field.name) {
        case 'first_name':
        case 'last_name':
            if (val.length < 2) { showError(field, 'Must be at least 2 characters.'); return false; }
            if (val.length > 255) { showError(field, 'Must not exceed 255 characters.'); return false; }
            if (!/^[a-zA-Z\s\-]+$/.test(val)) { showError(field, 'Only letters, spaces, and hyphens allowed.'); return false; }
            break;
        case 'email':
            if (val.length > 255) { showError(field, 'Email must not exceed 255 characters.'); return false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { showError(field, 'Please enter a valid email address.'); return false; }
            break;
        case 'phone':
            var stripped = val.replace(/[\s\-\(\)\.]/g, '');
            if (stripped.length > 20) { showError(field, 'Phone number is too long.'); return false; }
            if (stripped.length < 7) { showError(field, 'Phone number is too short.'); return false; }
            if (!/^\+?[0-9]{7,20}$/.test(stripped)) { showError(field, 'Please enter a valid phone number.'); return false; }
            break;
        case 'password':
            if (val.length < 8) { showError(field, 'Password must be at least 8 characters.'); return false; }
            if (!/[a-z]/.test(val)) { showError(field, 'Must contain at least one lowercase letter.'); return false; }
            if (!/[A-Z]/.test(val)) { showError(field, 'Must contain at least one uppercase letter.'); return false; }
            if (!/[0-9]/.test(val)) { showError(field, 'Must contain at least one number.'); return false; }
            if (!/[@$!%*#?&._\-]/.test(val)) { showError(field, 'Must contain at least one special character (@$!%*#?&._-).'); return false; }
            validateField(getField({name: 'password_confirmation'}));
            break;
        case 'password_confirmation':
            var pw = document.getElementById('password').value;
            if (!pw) { showError(field, 'Enter password first.'); return false; }
            if (val !== pw) { showError(field, 'Passwords do not match.'); return false; }
            break;
        case 'role':
            if (!val) { showError(field, 'Please select a role.'); return false; }
            break;
    }
    clearError(field);
    return true;
}

document.getElementById('registerForm').addEventListener('submit', function(e) {
    var valid = true;
    var fields = this.querySelectorAll('input, select');
    for (var i = 0; i < fields.length; i++) {
        if (fields[i].id === 'phone_code') continue;
        if (!validateField(fields[i])) valid = false;
    }
    if (!valid) e.preventDefault();
});
</script>
</x-layout>