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
                     alt="Login"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 text-white text-center px-6 pb-5">
                    <h2 class="text-xl font-bold mb-1">Hello, Friend!</h2>
                    <p class="text-white/80 text-xs leading-relaxed">
                        Enter your credentials and start your journey with us.
                    </p>
                </div>
            </div>

            {{-- BODY --}}
            <div class="flex flex-col md:flex-row">

                {{-- LEFT: Image desktop only --}}
                <div class="hidden md:block md:w-1/2 relative" style="min-height:520px;">
                    <img src="{{ asset('images/photography.jpg') }}"
                         alt="Login"
                         class="w-full h-full object-cover absolute inset-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 z-10 text-white text-center px-8 pb-16">
                        <h2 class="text-3xl font-bold mb-2">Hello, Friends!</h2>
                        <p class="text-white/80 text-sm leading-relaxed">
                            Enter your Credentials and start your journey with us.
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

                    <h1 class="text-xl sm:text-2xl text-center font-bold text-indigo-900 mb-1">Welcome back</h1>
                    <p class="text-sm text-gray-500 text-center mb-5 sm:mb-8">Sign in to your account to continue</p>

                    {{-- Divider --}}
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    {{-- Email --}}
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-indigo-900 mb-1">Email</label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            class="field {{ $errors->has('email') ? 'border-red-400 ring-1 ring-red-400' : 'border-indigo-300' }} field-transition">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-indigo-900 mb-1">Password</label>
                            <input type="password" id="password" name="password"
                            class="field {{ $errors->has('password') ? 'border-red-400 ring-1 ring-red-400' : 'border-indigo-300' }} field-transition">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        </div>
                        {{-- Forgot Password Link --}}
                        <div class="flex justify-start mb-4">
                            <a href="{{ route('forgot.password') }}"
                                class="text-sm text-indigo-900 hover:text-indigo-900 hover:underline transition-colors duration-200">
                                Forgot your password?
                            </a>
                        </div>

                            <!-- {{-- Remember Me --}}
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="remember" id="remember"
                                    class="w-4 h-4 accent-indigo-700 cursor-pointer">
                                <label for="remember"
                                    class="text-sm text-indigo-900 cursor-pointer">Remember me</label>
                            </div> -->

                            {{-- General Error --}}
                            @error('failed')
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            @enderror

                            {{-- Submit --}}
                            <button type="submit"
                                class="w-full bg-indigo-900 text-white py-2.5 rounded-lg font-semibold text-sm
                                       hover:bg-indigo-700 transition-colors duration-200 hover:shadow-lg ease-in-out">
                                Login
                            </button>
                             {{--login with Google--}}
                            <x-google-login />

                            {{-- Sign Up Link --}}
                            <p class="text-center text-sm text-gray-500">
                                Don't have an account?
                                <a href="{{ route('register') }}"
                                    class="text-indigo-700 font-semibold underline hover:text-indigo-900">
                                    Sign Up
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