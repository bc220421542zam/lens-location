<x-layouts.owner>
<div class="page">

<x-topbar 
    title="Add New Location"
    description="Fill in the details of your photography location">
</x-topbar>
    {{-- FORM --}}
    <form action="{{ route('owner.locations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Basic Information --}}
        <div class="card chart-transition mb-4 ">
            <h3 class="font-semibold text-indigo-900 mb-4">Basic Information</h3>

            {{-- Title + Category --}}
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="text-xs text-indigo-900 mb-1 block">Location Title</label>
                    <input
                        type="text"
                        name="title"
                        placeholder="Location title"
                        value="{{ old('title') }}"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                            @error('title') border-red-400 @enderror  ">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-44">
                    <label class="text-xs text-indigo-900 mb-1 block">Categories</label>
                    <select
                        name="category"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
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
                    <label class="text-xs text-indigo-900 mb-1 block">Description</label>
                    <textarea
                        name="description"
                        placeholder="Describe your location..."
                        rows="4"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-44">
                    <label class="text-xs text-indigo-900 mb-1 block">Price per Hour (PKR)</label>
                    <input
                        type="number"
                        name="price_per_hour"
                        placeholder="e.g. 3000"
                        value="{{ old('price_per_hour') }}"
                        min="0"
                        step="100"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               @error('price_per_hour') border-red-400 @enderror">
                    @error('price_per_hour')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location Details --}}
        <div class="card chart-transition mb-4">
            <h3 class="font-semibold text-indigo-900 mb-4">Location Details</h3>
            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="text-xs text-indigo-900 mb-1 block">Street Address</label>
                    <input
                        type="text"
                        name="address"
                        placeholder="e.g. 12 Main Street"
                        value="{{ old('address') }}"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               @error('address') border-red-400 @enderror">
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1">
                    <label class="text-xs text-indigo-900 mb-1 block">City</label>
                    <input
                        type="text"
                        name="city"
                        placeholder="e.g. Lahore"
                        value="{{ old('city') }}"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               @error('city') border-red-400 @enderror">
                    @error('city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Image Upload --}}
        <div class="card chart-transition mb-6">
            <h3 class="font-semibold text-indigo-900 mb-4">Image</h3>
            <div
                class="border-2 border-dashed border-indigo-200 rounded-lg p-8 text-center
                       cursor-pointer hover:border-indigo-400 transition-colors"
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
               class="px-6 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 bg-white hover:bg-gray-100 transition-colors btn-transition">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 rounded-lg bg-indigo-900 text-white text-sm hover:bg-indigo-800 transition-colors btn-transition">
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