{{--Country--}}
<div>
    <label class="text-xs text-gray-500 mb-1 block">Country</label>
        <select name="country"
        class="w-full input input-field focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('country') border-red-400 @enderror">
            <option value="">Select Country</option>
                @foreach(config('countries.countries') as $country)
                    <option value="{{ $country }}" {{ old('country', auth()->user()->country ?? '') == $country ? 'selected' : '' }}>
                        {{ $country }}
                    </option>
                @endforeach
        </select>
    @error('country')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>