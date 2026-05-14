<x-layout>
<div class="min-h-screen bg-slate-50 relative overflow-hidden">

    {{-- CENTER WRAPPER --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6">

        {{-- CARD --}}
        <div class="w-full max-w-sm sm:max-w-md lg:max-w-5xl
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
                        <p class="text-sm text-gray-500 text-center mb-5">Fill in your details to get started</p>

                        <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-4">
                            @csrf

             <!-- Role Dropdown -->
            <div class=" grid grid-cols-2 gap-2 text-indigo-900">
                <h1 class="font-bold text-indigo-900 text-center"> Sign Up as... </h1>
                <select id="role" name="role" form="registerForm">
                    <label for="role" class="block mb-1"><option value="">User Role</option></label>
                    <option value="photographer" {{ old('role') == 'photographer' ? 'selected' : '' }}>Photographer</option>
                    <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Location Owner</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>

                @error('role')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>


                            {{-- First + Last Name --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name"
                                        class="block text-sm font-medium text-indigo-900 mb-1">First Name</label>
                                    <input type="text" id="first_name" name="first_name"
                                        value="{{ old('first_name') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-indigo-700
                                               {{ $errors->has('first_name') ? 'border-red-400' : 'border-indigo-300' }}">
                                    @error('first_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="last_name"
                                        class="block text-sm font-medium text-indigo-900 mb-1">Last Name</label>
                                    <input type="text" id="last_name" name="last_name"
                                        value="{{ old('last_name') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-indigo-700
                                               {{ $errors->has('last_name') ? 'border-red-400' : 'border-indigo-300' }}">
                                    @error('last_name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email + Phone --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="email"
                                        class="block text-sm font-medium text-indigo-900 mb-1">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="{{ old('email') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-indigo-700
                                               {{ $errors->has('email') ? 'border-red-400' : 'border-indigo-300' }}">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="phone"
                                        class="block text-sm font-medium text-indigo-900 mb-1">Phone</label>
                                    <input type="text" id="phone" name="phone"
                                        value="{{ old('phone') }}"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-indigo-700
                                               {{ $errors->has('phone') ? 'border-red-400' : 'border-indigo-300' }}">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password + Confirm Password --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-indigo-900 mb-1">Password</label>
                                    <input type="password" id="password" name="password"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-indigo-700
                                               {{ $errors->has('password') ? 'border-red-400' : 'border-indigo-300' }}">
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-indigo-900 mb-1">Confirm Password</label>
                                    <input type="password" id="password_confirmation"
                                        name="password_confirmation"
                                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                                               focus:outline-none focus:ring-2 focus:ring-indigo-700
                                               {{ $errors->has('password_confirmation') ? 'border-red-400' : 'border-indigo-300' }}">
                                    @error('password_confirmation')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
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
</x-layout>