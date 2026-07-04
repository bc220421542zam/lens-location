
<!-- Gender -->
<div>
    <label class="text-xs text-gray-500 block mb-1">Gender</label>
    <div class="relative">
        <select name="gender"
                class="w-full appearance-none px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white
                focus:outline-none focus:ring-2 focus:ring-indigo-300 input input-field text-indigo-900 transition
                @error('gender') border-red-400 @enderror">
        <option value="">Select Gender</option>
        <option value="male" {{ old('gender', auth()->user()->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
        <option value="female" {{ old('gender', auth()->user()->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
        <option value="non-binary" {{ old('gender', auth()->user()->gender ?? '') == 'non-binary' ? 'selected' : '' }}>Non-binary</option>
        <option value="other" {{ old('gender', auth()->user()->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>

        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
         @error('gender')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
        </div>
</div>