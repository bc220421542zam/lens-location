<x-layouts.owner>
<div class="page">

  <div class="text-sm text-indigo-700 hover:underline">
    <a href="{{ route('owner.listings', $location->id) }}"
           class="text-sm text-indigo-700 hover:underline">&larr; Listings</a> / Edit
</div>

<x-topbar 
    title="Edit Location"
    description="Update the details of your photography location">
</x-topbar>
    {{-- Success message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('owner.locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="card chart-transition mb-4">
            <h3 class="font-semibold text-indigo-900 mb-4">Basic Information</h3>

            {{-- Title + Category --}}
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <input
                        type="text"
                        name="title"
                        placeholder="Location title"
                        value="{{ old('title', $location->title) }}"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                            @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-44">
                    <select
                        name="category"
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               @error('category') border-red-400 @enderror">
                        <option value="">Category</option>
                        <option value="studio"  {{ old('category', $location->category) == 'studio'  ? 'selected' : '' }}>Studio</option>
                        <option value="outdoor" {{ old('category', $location->category) == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                        <option value="rooftop" {{ old('category', $location->category) == 'rooftop' ? 'selected' : '' }}>Rooftop</option>
                        <option value="indoor"  {{ old('category', $location->category) == 'indoor'  ? 'selected' : '' }}>Indoor</option>
                        <option value="garden"  {{ old('category', $location->category) == 'garden'  ? 'selected' : '' }}>Garden</option>
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
                        class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                               @error('description') border-red-400 @enderror">{{ old('description', $location->description) }}</textarea>
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
                        value="{{ old('price_per_hour', $location->price_per_hour) }}"
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
                        value="{{ old('address', $location->address) }}"
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
                        value="{{ old('city', $location->city) }}"
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

            {{-- Current Image --}}
            @if($location->image)
                <div class="mb-4">
                    <p class="text-xs text-indigo-900 mb-2">Current Image</p>
                    <img src="{{ asset('storage/' . $location->image) }}"
                        alt="Current listing image"
                        class="w-full h-48 object-cover rounded-xl shade">
                    <p class="text-xs text-gray-400 mt-1">Upload a new image below to replace it</p>
                </div>
            @endif

            {{-- New Image Upload --}}
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
                    <p class="text-3xl mb-2"><i class="icon fa-solid fa-upload"></i></p>
                    <p class="text-sm text-gray-400">Click to upload new image (optional)</p>
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
                Update
            </button>
        </div>

    </form>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('image-preview').src = e.target.result;
        document.getElementById('preview-wrap').classList.remove('hidden');
        document.getElementById('upload-placeholder').classList.add('hidden');
    };
    reader.readAsDataURL(file);
}
</script>

</x-layouts.owner>