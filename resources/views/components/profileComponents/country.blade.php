<!--Country-->
<div>
    <label class="text-xs text-gray-500 mb-1 block">Country</label>
    <div class="relative">
        <select name="country"
        class="w-full input input-field appearance-none px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('country') border-red-400 @enderror">
            <option value="">Select Country</option>
                @foreach(config('countries.countries') as $country)
                    <option value="{{ $country }}" {{ old('country', auth()->user()->country ?? '') == $country ? 'selected' : '' }}>
                        {{ $country }}
                    </option>
                @endforeach
        </select>
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    @error('country')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    </div>
</div>