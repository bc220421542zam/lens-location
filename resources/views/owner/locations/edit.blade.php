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
    <form id="location-form" action="{{ route('owner.locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="card chart-transition mb-4">
            <h3 class="font-semibold text-indigo-900 mb-4">Basic Information</h3>

            {{-- Title + Category --}}
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label for="title" class="text-xs text-indigo-900 mb-1 block">Title</label>
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
                    <label class="text-xs text-indigo-900 mb-1 block">Category</label>

                    <div class="relative">
                        <select
                            name="category"
                            class="input-field shade w-full appearance-none border border-gray-200 rounded-lg px-3 py-2 pr-8 text-sm
                                @error('category') border-red-400 @enderror">
                            <option value="">Category</option>
                            <option value="studio"  {{ old('category', $location->category) == 'studio'  ? 'selected' : '' }}>Studio</option>
                            <option value="outdoor" {{ old('category', $location->category) == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                            <option value="rooftop" {{ old('category', $location->category) == 'rooftop' ? 'selected' : '' }}>Rooftop</option>
                            <option value="indoor"  {{ old('category', $location->category) == 'indoor'  ? 'selected' : '' }}>Indoor</option>
                            <option value="garden"  {{ old('category', $location->category) == 'garden'  ? 'selected' : '' }}>Garden</option>
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
                    <label for="description" class="text-xs text-indigo-900 mb-1 block">Description</label>
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

        {{-- Map Location --}}
        <x-ownerComp.location-map-picker
            :latitude="old('latitude', $location->latitude)"
            :longitude="old('longitude', $location->longitude)" />

        {{-- Images --}}
        @php
            $minImages = \App\Http\Requests\Owner\StoreLocationRequest::MIN_IMAGES;
            $maxImages = \App\Http\Requests\Owner\StoreLocationRequest::MAX_IMAGES;
            $gallery   = $location->gallery();

            // Older listings can hold fewer photos than a new one requires;
            // their owners only have to keep what they already had.
            $minRequired = min($minImages, max(count($gallery), 1));

            // After a failed submit, restore the arrangement the owner had made.
            $current = old('image_order') === null
                ? $gallery
                : array_values(array_filter(
                    (array) old('image_order'),
                    fn($token) => in_array($token, $gallery, true)
                ));
        @endphp

        <div class="card chart-transition mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-indigo-900">Images</h3>
                <span id="image-counter" class="text-xs text-gray-400"></span>
            </div>

            <div
                id="image-dropzone"
                class="border-2 border-dashed border-indigo-200 rounded-lg p-8 text-center
                       cursor-pointer hover:border-indigo-400 transition-colors">
                <p class="text-3xl mb-2"><i class="icon fa-solid fa-upload"></i></p>
                <p class="text-sm text-gray-400">Click to add photos — or drag &amp; drop</p>
                <p class="text-xs text-gray-300 mt-1">
                    PNG, JPEG, WEBP — max 4MB each · at least {{ $minRequired }}, up to {{ $maxImages }} images
                </p>

                <input
                    type="file"
                    id="imageInput"
                    name="images[]"
                    accept=".png,.jpg,.jpeg,.webp"
                    multiple
                    hidden>
            </div>

            {{-- Current gallery and new uploads, in the order they will be saved --}}
            <div id="image-previews" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4"></div>
            <div id="image-order-fields"></div>

            <p id="image-hint" class="text-xs text-gray-400 mt-2 hidden">
                The first image is the cover photo — use &ldquo;Make cover&rdquo; to promote another one,
                or &times; to remove one.
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
                Update
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
(function () {
    const MIN_IMAGES = {{ $minRequired }};
    const MAX_IMAGES = {{ $maxImages }};
    const MAX_BYTES  = 4 * 1024 * 1024;
    const ALLOWED    = ['image/png', 'image/jpeg', 'image/webp'];

    const input     = document.getElementById('imageInput');
    const dropzone  = document.getElementById('image-dropzone');
    const previews  = document.getElementById('image-previews');
    const orderBox  = document.getElementById('image-order-fields');
    const counter   = document.getElementById('image-counter');
    const hint      = document.getElementById('image-hint');
    const errorBox  = document.getElementById('image-client-error');
    const form      = document.getElementById('location-form');

    // One list holds both the photos already on the listing and the new picks,
    // so removing and reordering works the same way for either kind.
    let items = [
        @foreach ($current as $path)
        { kind: 'existing', path: @json($path), url: @json(Storage::url($path)) },
        @endforeach
    ];

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.toggle('hidden', !message);
    }

    // The input's FileList is read-only, so we rebuild it from our own list.
    // Its order decides which `new:<index>` token points at which upload.
    function syncInput() {
        const transfer = new DataTransfer();
        items.filter(item => item.kind === 'new').forEach(item => transfer.items.add(item.file));
        input.files = transfer.files;
    }

    function syncOrderFields() {
        orderBox.innerHTML = '';
        let newIndex = 0;

        items.forEach(item => {
            const field = document.createElement('input');
            field.type  = 'hidden';
            field.name  = 'image_order[]';
            field.value = item.kind === 'existing' ? item.path : 'new:' + (newIndex++);
            orderBox.appendChild(field);
        });
    }

    function render() {
        previews.innerHTML = '';

        items.forEach((item, index) => {
            const wrap = document.createElement('div');
            wrap.className = 'relative group rounded-lg overflow-hidden border border-gray-200';

            const img = document.createElement('img');
            img.className = 'w-full h-24 object-cover';
            img.alt = item.kind === 'existing' ? 'Listing photo' : item.file.name;
            if (item.kind === 'existing') {
                img.src = item.url;
            } else {
                img.src = URL.createObjectURL(item.file);
                img.onload = () => URL.revokeObjectURL(img.src);
            }
            wrap.appendChild(img);

            if (index === 0) {
                const badge = document.createElement('span');
                badge.className = 'absolute top-1 left-1 bg-indigo-900 text-white text-[10px] px-2 py-0.5 rounded-full';
                badge.textContent = 'Cover';
                wrap.appendChild(badge);
            } else {
                const promote = document.createElement('button');
                promote.type = 'button';
                promote.className = 'absolute bottom-1 left-1 bg-white/90 text-indigo-900 text-[10px] px-2 py-0.5 rounded-full';
                promote.textContent = 'Make cover';
                promote.addEventListener('click', () => {
                    items.unshift(items.splice(index, 1)[0]);
                    sync();
                });
                wrap.appendChild(promote);
            }

            if (item.kind === 'new') {
                const tag = document.createElement('span');
                tag.className = 'absolute bottom-1 right-1 bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full';
                tag.textContent = 'New';
                wrap.appendChild(tag);
            }

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'absolute top-1 right-1 bg-white/90 text-red-500 text-xs w-5 h-5 rounded-full leading-none';
            remove.innerHTML = '&times;';
            remove.title = 'Remove image';
            remove.addEventListener('click', () => {
                items.splice(index, 1);
                sync();
            });
            wrap.appendChild(remove);

            previews.appendChild(wrap);
        });

        previews.classList.toggle('hidden', items.length === 0);
        hint.classList.toggle('hidden', items.length === 0);
        counter.textContent = items.length + ' / ' + MAX_IMAGES + ' images';
        counter.classList.toggle('text-red-500', items.length < MIN_IMAGES);
    }

    function sync() {
        syncInput();
        syncOrderFields();
        render();
    }

    function addFiles(incoming) {
        const rejected = [];

        Array.from(incoming).forEach(file => {
            if (items.length >= MAX_IMAGES) {
                rejected.push('You can have a maximum of ' + MAX_IMAGES + ' images.');
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
            const duplicate = items.some(item =>
                item.kind === 'new' && item.file.name === file.name && item.file.size === file.size);
            if (duplicate) {
                rejected.push(file.name + ' was already added.');
                return;
            }
            items.push({ kind: 'new', file: file });
        });

        sync();
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
        if (items.length < MIN_IMAGES) {
            event.preventDefault();
            showError(MIN_IMAGES === 1
                ? 'A listing needs at least one image.'
                : 'Please keep or upload at least ' + MIN_IMAGES + ' images of the location.');
            dropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    sync();
})();
</script>
@endpush
</x-layouts.owner>
