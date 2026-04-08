<x-layout>
        
    <img src="/images/Logo.png" alt="logo" class="mx-auto">
    <h1 class="font-bold text-indigo-900 text-center">Welcome back</h1>
    <div class="mx-auto max-w-screen-sm card flex items-center justify-center bg-slate-100 p-8 rounded-lg shadow-lg mt-8">
        <form action="{{ route('login') }}" method="POST" class="space-y-4 w-full max-w-md">
            @csrf
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-indigo-900 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700
                    {{ $errors->has('email') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
 
            <!-- Password Input -->
            <div>
                <label for="password" class="block text-indigo-900 mb-1">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700
                    {{ $errors->has('password') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>

             @error('failed')
                    <p class="error">{{ $message }}</p>
            @enderror

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-indigo-900 text-white py-2 px-4 rounded-md hover:bg-indigo-600 transition-colors font-semibold">
                Login</button>
        </form>
    </div>
</x-layout>