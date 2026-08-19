<x-layouts.owner>
<div class="page">

<x-topbar 
    title="Add New Location"
    description="Fill in the details of your photography location">
</x-topbar>
    {{-- FORM --}}
    <form id="location-form" action="{{ route('owner.locations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Basic Information --}}
        <div class="card chart-transition mb-4 rounded-2xl border border-l-3 border-indigo-400 p-6"> 
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

                    <div class="relative">
                        <select
                            name="category"
                            class="input-field shade w-full appearance-none border border-gray-200 rounded-lg px-3 py-2 pr-8 text-sm
                                @error('category') border-red-400 @enderror">
                            <option value="">Category</option>
                            <option value="studio"  {{ old('category') == 'studio'  ? 'selected' : '' }}>Studio</option>
                            <option value="outdoor" {{ old('category') == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                            <option value="rooftop" {{ old('category') == 'rooftop' ? 'selected' : '' }}>Rooftop</option>
                            <option value="indoor"  {{ old('category') == 'indoor'  ? 'selected' : '' }}>Indoor</option>
                            <option value="garden"  {{ old('category') == 'garden'  ? 'selected' : '' }}>Garden</option>
                        </select>

                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-indigo-800" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

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
        <div class="card chart-transition mb-4 rounded-2xl border border-l-3 border-indigo-400 p-6">
            <h3 class="font-semibold text-indigo-900 mb-4 ">Location Details</h3>
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
        @php
            $minImages = \App\Http\Requests\Owner\StoreLocationRequest::MIN_IMAGES;
            $maxImages = \App\Http\Requests\Owner\StoreLocationRequest::MAX_IMAGES;
        @endphp
        <div class="card chart-transition mb-6 rounded-2xl border border-l-3 border-indigo-400 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-indigo-900">Images</h3>
                <span id="image-counter" class="text-xs text-gray-400">
                    0 / {{ $maxImages }} selected
                </span>
            </div>

            <div
                id="image-dropzone"
                class="border-2 border-dashed border-indigo-200 rounded-lg p-8 text-center
                       cursor-pointer hover:border-indigo-400 transition-colors">
                <p class="text-3xl mb-2"><i class=" icon fa-solid fa-upload"></i></p>
                <p class="text-sm text-gray-400">Click to upload — or drag &amp; drop</p>
                <p class="text-xs text-gray-300 mt-1">
                    PNG, JPEG, WEBP — max 4MB each · at least {{ $minImages }}, up to {{ $maxImages }} images
                </p>

                <input
                    type="file"
                    id="imageInput"
                    name="images[]"
                    accept=".png,.jpg,.jpeg,.webp"
                    multiple
                    hidden>
            </div>

            {{-- Previews --}}
            <div id="image-previews" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4"></div>
            <p id="image-hint" class="text-xs text-gray-400 mt-2 hidden">
                The first image is used as the cover photo.
            </p>

            <p id="image-client-error" class="text-red-500 text-xs mt-2 hidden"></p>

            @error('images')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            @foreach ($errors->get('images.*') as $messages)
                @foreach ($messages as $message)
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @endforeach
            @endforeach
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

@push('scripts')
<script>
(function () {
    const MIN_IMAGES = {{ $minImages }};
    const MAX_IMAGES = {{ $maxImages }};
    const MAX_BYTES  = 4 * 1024 * 1024;
    const ALLOWED    = ['image/png', 'image/jpeg', 'image/webp'];

    const input     = document.getElementById('imageInput');
    const dropzone  = document.getElementById('image-dropzone');
    const previews  = document.getElementById('image-previews');
    const counter   = document.getElementById('image-counter');
    const hint      = document.getElementById('image-hint');
    const errorBox  = document.getElementById('image-client-error');
    const form      = document.getElementById('location-form');

    // The input's FileList is read-only, so we keep our own list and rebuild it.
    let files = [];

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.toggle('hidden', !message);
    }

    function syncInput() {
        const transfer = new DataTransfer();
        files.forEach(file => transfer.items.add(file));
        input.files = transfer.files;
    }

    function render() {
        previews.innerHTML = '';

        files.forEach((file, index) => {
            const wrap = document.createElement('div');
            wrap.className = 'relative group rounded-lg overflow-hidden border border-gray-200';

            const img = document.createElement('img');
            img.className = 'w-full h-24 object-cover';
            img.alt = file.name;
            img.src = URL.createObjectURL(file);
            img.onload = () => URL.revokeObjectURL(img.src);
            wrap.appendChild(img);

            if (index === 0) {
                const badge = document.createElement('span');
                badge.className = 'absolute top-1 left-1 bg-indigo-900 text-white text-[10px] px-2 py-0.5 rounded-full';
                badge.textContent = 'Cover';
                wrap.appendChild(badge);
            }

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'absolute top-1 right-1 bg-white/90 text-red-500 text-xs w-5 h-5 rounded-full leading-none';
            remove.innerHTML = '&times;';
            remove.title = 'Remove image';
            remove.addEventListener('click', () => {
                files.splice(index, 1);
                syncInput();
                render();
            });
            wrap.appendChild(remove);

            previews.appendChild(wrap);
        });

        previews.classList.toggle('hidden', files.length === 0);
        hint.classList.toggle('hidden', files.length === 0);
        counter.textContent = files.length + ' / ' + MAX_IMAGES + ' selected';
        counter.classList.toggle('text-red-500', files.length > 0 && files.length < MIN_IMAGES);
    }

    function addFiles(incoming) {
        const rejected = [];

        Array.from(incoming).forEach(file => {
            if (files.length >= MAX_IMAGES) {
                rejected.push('You can upload a maximum of ' + MAX_IMAGES + ' images.');
                return;
            }
            if (!ALLOWED.includes(file.type)) {
                rejected.push(file.name + ' is not a PNG, JPG or WEBP image.');
                return;
            }
            if (file.size > MAX_BYTES) {
                rejected.push(file.name + ' is larger than 4MB.');
                return;
            }
            const duplicate = files.some(f => f.name === file.name && f.size === file.size);
            if (duplicate) {
                rejected.push(file.name + ' was already added.');
                return;
            }
            files.push(file);
        });

        syncInput();
        render();
        showError(rejected.length ? rejected[0] : '');
    }

    dropzone.addEventListener('click', () => input.click());

    // Only user picks fire this — assigning input.files in syncInput() does not.
    input.addEventListener('change', event => addFiles(event.target.files));

    ['dragenter', 'dragover'].forEach(name =>
        dropzone.addEventListener(name, event => {
            event.preventDefault();
            dropzone.classList.add('border-indigo-400');
        })
    );

    ['dragleave', 'drop'].forEach(name =>
        dropzone.addEventListener(name, event => {
            event.preventDefault();
            dropzone.classList.remove('border-indigo-400');
        })
    );

    dropzone.addEventListener('drop', event => addFiles(event.dataTransfer.files));

    form.addEventListener('submit', event => {
        if (files.length < MIN_IMAGES) {
            event.preventDefault();
            showError('Please upload at least ' + MIN_IMAGES + ' images of the location.');
            dropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    render();
})();
</script>
@endpush
</x-layouts.owner>