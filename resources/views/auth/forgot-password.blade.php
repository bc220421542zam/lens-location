<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APPNAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
<div class="min-h-screen bg-[#DDDEEF] relative overflow-hidden">
    {{-- CENTER WRAPPER --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6 md:p-8">
        {{-- CARD --}}
        <div class="w-full max-w-sm sm:max-w-md md:max-w-4xl chart-transition
                    rounded-2xl shadow-xl bg-white border border-indigo-200 overflow-hidden ">

            {{-- MOBILE: Image on top --}}
            <div class="relative h-48 sm:h-56 md:hidden w-full">
                <img src="{{ asset('images/forgot.jpg') }}"
                     alt="forgot password"
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
                    <img src="{{ asset('images/forgot.jpg') }}"
                         alt="forgot password"
                         class="w-full h-full object-cover absolute inset-0">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 z-10 text-white text-center px-8 pb-16">
                        <h2 class="text-3xl font-bold mb-2">Secure Account Recovery</h2>
                        <p class="text-white/80 text-sm leading-relaxed">
                            Securely regain access to your LensLocation account in just a few steps.
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
                    {{-- Divider --}}
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col items-center mb-6">
                
                <h2 class="text-xl sm:text-2xl text-center font-bold text-indigo-900 mb-1">Forgot your password?</h2>
                <p class="text-sm text-gray-500 text-center mb-5 sm:mb-8">
                    Enter your email and we'll send you a reset link
                </p>
            </div>

            <form action="{{ route('reset.password') }}" method="get" class="space-y-3">

                <!-- EMAIL -->
                <div>
                    <label class="text-xs text-indigo-900 block mb-1">Email address</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-200 bg-gray-50 rounded-lg input input-field
                       focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400
                       outline-none transition">
                </div>

                <!-- SUBMIT BUTTON -->
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-indigo-800 text-white text-sm font-medium btn-transition
                   hover:bg-indigo-700 hover:scale-[1.01] transition-all duration-200 mt-1">
                    Send Reset Link
                </button>
            </form>

                    <!-- BACK TO LOGIN -->
                    <p class="text-center mt-4 text-xs text-gray-400">
                        Remembered your password?
                        <a href="/login" class="text-indigo-900 hover:underline font-medium">Back to login</a>
                    </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</body>
</html>
 