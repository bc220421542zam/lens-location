<x-layout>
<div class="min-h-screen bg-[#DDDEEF] relative overflow-hidden">
    {{-- CENTER WRAPPER --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 md:p-8">
        {{-- CARD --}}
        <div class="w-full max-w-sm sm:max-w-md md:max-w-4xl chart-transition
                    rounded-2xl shadow-xl bg-white border border-indigo-200 overflow-hidden ">

            {{-- MOBILE: Image on top --}}
            <div class="relative h-48 sm:h-56 md:hidden w-full">
                <img src="{{ asset('images/photography.jpg') }}"
                     alt="Reset password"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 text-white text-center px-6 pb-5">
                    <h2 class="text-xl font-bold mb-1">Reset & Go!</h2>
                    <p class="text-white/80 text-xs leading-relaxed">
                        Choose a strong new password and get back to your journey.
                    </p>
                </div>
            </div>

            {{-- BODY --}}
            <div class="flex flex-col md:flex-row">

                {{-- LEFT: Image desktop only --}}
                <div class="hidden md:block md:w-1/2 relative" style="min-height:520px;">
                    <img src="{{ asset('images/photography.jpg') }}"
                         alt="Reset password"
                         class="w-full h-full object-cover absolute inset-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 z-10 text-white text-center px-8 pb-16">
                        <h2 class="text-3xl font-bold mb-2">Reset & Go!</h2>
                        <p class="text-white/80 text-sm leading-relaxed">
                            Choose a strong new password and get back to your journey.
                        </p>
                    </div>
                </div>


            {{-- RIGHT: Form --}}
            <div class="w-full md:w-1/2 flex items-center justify-center p-6 sm:p-8 md:p-10">
                <div class="w-full">
                    {{-- Logo --}}
                    <div class="flex justify-center mb-6">
                        <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-8 md:h-10">
                    </div>

                    <h1 class="text-xl sm:text-2xl text-center font-bold text-indigo-900 mb-1">Reset your password</h1>
                    <p class="text-sm text-gray-500 text-center mb-5 sm:mb-8">Choose a strong new password for your account</p>

                    {{-- Success Message --}}
                    @if (session('status'))
                        <div class="mb-4 rounded-xl bg-green-50 border border-green-100 px-4 py-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                            <p class="text-sm text-green-600">{{ session('status') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        {{-- New Password --}}
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-indigo-900 mb-1">New password</label>
                            <input type="password" id="password" name="password"
                                class="field {{ $errors->has('password') ? 'border-red-400 ring-1 ring-red-400' : 'border-indigo-300' }} field-transition">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-indigo-900 mb-1">Confirm password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="field {{ $errors->has('password_confirmation') ? 'border-red-400 ring-1 ring-red-400' : 'border-indigo-300' }} field-transition">
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email Error (broker failures) --}}
                        @error('email')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full bg-indigo-900 text-white py-2.5 rounded-lg font-semibold text-sm
                                   hover:bg-indigo-700 transition-colors duration-200 hover:shadow-lg ease-in-out">
                            Reset password
                        </button>

                        {{-- Back to Login --}}
                        <p class="text-center text-sm text-gray-500">
                            Remembered your password?
                            <a href="{{ route('login') }}"
                                class="text-indigo-700 font-semibold underline hover:text-indigo-900">
                                Back to login
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
