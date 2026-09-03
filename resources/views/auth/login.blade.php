<x-layout>
<div class="min-h-screen bg-gradient-to-br from-[#E7E8F6] via-[#DDDEEF] to-[#D6D8EE] relative overflow-hidden">
    {{-- BACKGROUND DECORATION (behind the card) --}}
    <x-auth-decor />

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
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                class="field pr-10 {{ $errors->has('password') ? 'border-red-400 ring-1 ring-red-400' : 'border-indigo-300' }} field-transition">
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
                        @enderror
                        </div>
                        {{-- Forgot Password Link --}}
                        <div class="flex justify-start mb-4">
                            <a href="{{ route('password.request') }}"
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

                            {{-- Login with Facebook --}}
                            <x-facebook-login />

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
</script>
</x-layout>