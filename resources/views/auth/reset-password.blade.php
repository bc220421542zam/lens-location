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

<!-- Parent Container -->
<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">
    {{--    <div class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-xl lg:flex-row ">--}}

        <!-- LEFT SIDEBAR / HERO SECTION -->
        <x-auth-hero-section
            title="Reset & Go!"
            subtitle="Your account is waiting —"
            image="images/reset-password.jpg"
        />
    <!-- Right Section -->
    <div class="w-full md:w-1/2 flex justify-center items-center bg-blue-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-8 sm:px-6 md:px-8 border-r-4 border-blue-700">

        

            <!-- ICON + TITLE -->
            <div class="flex flex-col items-center mb-5">
                <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800">Reset your password</h2>
                <p class="text-xs text-gray-400 mt-0.5 text-center">Choose a strong new password for your account</p>
            </div>

            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2 text-xs text-red-500">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-100 px-4 py-3 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <p class="text-xs text-green-600">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('reset.password') }}" method="post" class="space-y-3">

                <!-- NEW PASSWORD -->
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">New password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        class="w-full px-3 py-2 text-sm rounded-xl
                       border {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                       focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                       outline-none transition">
                </div>

                <!-- CONFIRM PASSWORD -->
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Confirm password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="••••••••"
                        required
                        class="w-full px-3 py-2 text-sm rounded-xl
                       border {{ $errors->has('password_confirmation') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                       focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                       outline-none transition">
                </div>

                <!-- SUBMIT BUTTON -->
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                   hover:bg-blue-700 hover:scale-[1.01] transition-all duration-200 mt-1">
                    Reset password
                </button>

            </form>

            <!-- BACK TO LOGIN -->
            <p class="text-center mt-4 text-xs text-gray-400">
                Remembered your password?
                <a href="/login" class="text-blue-600 hover:underline font-medium">Back to login</a>
            </p>

    
    </div>

    </div>

</body>
</html>
 