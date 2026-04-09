<x-layout>
<div class="container">
    <div class="left">   
            <!-- Role Dropdown -->
<div class=" grid grid-cols-2 gap-2 text-indigo-900">
    <h1 class="font-bold text-indigo-900 text-left"> Sign Up as... </h1>
    <select id="role" name="role" form="registerForm">
        <label for="role" class="block mb-1"><option value="">User Role</option></label>
        <option value="photographer" {{ old('role') == 'photographer' ? 'selected' : '' }}>Photographer</option>
        <option value="location_owner" {{ old('role') == 'location_owner' ? 'selected' : '' }}>Location Owner</option>
        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
    </select>

    @error('role')
        <p class="error">{{ $message }}</p>
    @enderror
</div>

        <div class="mx-auto max-w-screen-sm card flex items-center justify-center bg-slate-100 p-8 rounded-lg shadow-lg mt-8">
            <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-4 w-full max-w-md">
            @csrf
            <!-- First + Last Name -->
            <div class="grid grid-cols-2 gap-6 mb-4">

                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-indigo-900 mb-1">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700
                        {{ $errors->has('first_name') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">
                    
                    @error('first_name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-indigo-900 mb-1">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700
                        {{ $errors->has('last_name') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">
                    
                    @error('last_name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

           <!-- Email + Phone -->
            <div class="grid grid-cols-2 gap-6 mb-4">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-indigo-900 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700
                        {{ $errors->has('email') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">
                    
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-indigo-900 mb-1">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700
                        {{ $errors->has('phone') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">
                    
                    @error('phone')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

            </div>
 
            <!-- Password + Confirm Password -->
            <div class="grid grid-cols-2 gap-6 mb-4">

                <!-- Password -->
                <div class="relative">
                    <label for="password" class="block text-indigo-900 mb-1">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700 pr-10
                        {{ $errors->has('password') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">

                    <!-- Eye Icon -->
                    

                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="relative">
                    <label for="password_confirmation" class="block text-indigo-900 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-700 focus:border-indigo-700 pr-10
                        {{ $errors->has('password_confirmation') ? 'border-red-500 ring-red-500' : 'border-gray-300' }}">

                    <!-- Eye Icon -->
                    

                    @error('password_confirmation')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-indigo-900 text-white py-2 px-4 rounded-md hover:bg-indigo-600 transition-colors font-semibold">
                Sign Up</button>
             <label for="msg"><p class="mb-4 text-indigo-900 sm"> Already have an account? 
                <a href="{{ route('register') }}" target="_blank" class="underline"> LogIn </a></p></label> 
            </form>
        </div>
</div>
        <div class="right">
                <!-- Image -->
            <img src="/images/Studio-photography-rental.jpg" alt="image">
        </div>
</x-layout>