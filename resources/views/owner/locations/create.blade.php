<x-layouts.owner>
<div class="page">

    {{-- HEADER --}}
    <div class="top-bar mb-4">
        <div>
            <h1 class="title text-indigo-900">Add New Location</h1>
            <p class="text-sm text-gray-400">Fill the details below to submit your location</p>
        </div>
    </div>
    {{-- FORM --}}
    <form action="{{ route('owner.locations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Basic Information --}}
        <div class="card mb-4">
            <h3 class="font-semibold text-indigo-900 mb-4">Basic Information</h3>

            {{-- Title + Category --}}
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <input
                        type="text"
                        name="title"
                        placeholder="Location title"
                        value="{{ old('title') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-300
                               @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-44">
                    <select
                        name="category"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-300
                               @error('category') border-red-400 @enderror">
                        <option value="">Category</option>
                        <option value="studio"  {{ old('category') == 'studio'  ? 'selected' : '' }}>Studio</option>
                        <option value="outdoor" {{ old('category') == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                        <option value="rooftop" {{ old('category') == 'rooftop' ? 'selected' : '' }}>Rooftop</option>
                        <option value="indoor"  {{ old('category') == 'indoor'  ? 'selected' : '' }}>Indoor</option>
                        <option value="garden"  {{ old('category') == 'garden'  ? 'selected' : '' }}>Garden</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description + Price --}}
            <div class="flex gap-4">
                <div class="flex-1">
                    <textarea
                        name="description"
                        placeholder="Describe your location..."
                        rows="4"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-300
                               @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-44">
                    <label class="text-xs text-gray-500 mb-1 block">Price per Hour (PKR)</label>
                    <input
                        type="number"
                        name="price_per_hour"
                        placeholder="e.g. 3000"
                        value="{{ old('price_per_hour') }}"
                        min="0"
                        step="100"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-300
                               @error('price_per_hour') border-red-400 @enderror">
                    @error('price_per_hour')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location Details --}}
        <div class="card mb-4">
            <h3 class="font-semibold text-indigo-900 mb-4">Location Details</h3>
            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="text-xs text-gray-500 mb-1 block">Street Address</label>
                    <input
                        type="text"
                        name="address"
                        placeholder="e.g. 12 Main Street"
                        value="{{ old('address') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-300
                               @error('address') border-red-400 @enderror">
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1">
                    <label class="text-xs text-gray-500 mb-1 block">City</label>
                    <input
                        type="text"
                        name="city"
                        placeholder="e.g. Lahore"
                        value="{{ old('city') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-300
                               @error('city') border-red-400 @enderror">
                    @error('city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Image Upload --}}
        <div class="card mb-6">
            <h3 class="font-semibold text-indigo-900 mb-4">Image</h3>
            <div
                class="border-2 border-dashed border-gray-200 rounded-lg p-8 text-center
                       cursor-pointer hover:border-indigo-300 transition-colors"
                onclick="document.getElementById('imageInput').click()">

                {{-- Preview --}}
                <div id="preview-wrap" class="hidden mb-3">
                    <img id="image-preview" src="#" alt="Preview"
                         class="mx-auto max-h-40 rounded-lg object-cover">
                    <p class="text-xs text-gray-400 mt-2">Click to change image</p>
                </div>

                {{-- Placeholder --}}
                <div id="upload-placeholder">
                    <p class="text-3xl mb-2"><i class=" icon fa-solid fa-upload"></i></p>
                    <p class="text-sm text-gray-400">Click to upload</p>
                    <p class="text-xs text-gray-300 mt-1">PNG, JPEG, WEBP — max 4MB</p>
                </div>

                <input
                    type="file"
                    id="imageInput"
                    name="image"
                    accept=".png,.jpg,.jpeg,.webp"
                    hidden
                    onchange="previewImage(event)">
            </div>
            @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('owner.listings') }}"
               class="px-6 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 rounded-lg bg-indigo-900 text-white text-sm hover:bg-indigo-800 transition-colors">
                Create
            </button>
        </div>

    </form>
</div>

<input
    type="file"
    id="imageInput"
    name="image"
    accept=".png,.jpg,.jpeg,.webp"
    hidden>
</x-layouts.owner>