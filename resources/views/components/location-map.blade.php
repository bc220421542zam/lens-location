@props([
    'latitude' => null,
    'longitude' => null,
    'title' => 'Map Location',
    'address' => null,
    'bare' => false,
    'class' => '',
])

@php
    // A stored (0,0) means the row predates coordinates - not a pin.
    $hasPin = is_numeric($latitude) && is_numeric($longitude)
        && ((float) $latitude !== 0.0 || (float) $longitude !== 0.0);
@endphp

@if (! $bare)
    {{-- Read-only map showing a listing's pinned location. --}}
    <div class="card chart-transition mb-4 rounded-2xl border border-l-3 border-indigo-400 p-6">
        <h3 class="font-semibold text-indigo-900 mb-1">{{ $title }}</h3>
        @if ($address)
            <p class="text-sm text-gray-400 mb-3">{{ $address }}</p>
        @endif

        @if ($hasPin)
            <div class="location-map w-full h-72 rounded-xl border border-indigo-200 overflow-hidden z-0"
                 data-lat="{{ (float) $latitude }}"
                 data-lng="{{ (float) $longitude }}"></div>
        @else
            <p class="text-sm text-gray-400">This listing has no map location set yet.</p>
        @endif
    </div>
@else
    {{-- Bare map: no title, no subtitle, no card - just the map itself.
         The parent controls the size (wrap it in a sized container). --}}
    @if ($hasPin)
        <div class="location-map w-full h-full rounded-lg overflow-hidden z-0 border border-indigo-100 {{ $class }}"
             data-lat="{{ (float) $latitude }}"
             data-lng="{{ (float) $longitude }}"></div>
    @else
        <div class="w-full h-full rounded-lg bg-gray-100 border border-indigo-100 flex items-center justify-center {{ $class }}">
            <i class="fa-solid fa-map-location-dot text-2xl text-gray-300" aria-hidden="true"></i>
        </div>
    @endif
@endif

@once
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    if (typeof L === 'undefined') return;

    const initMap = (el) => {
        if (el.dataset.ready) return;
        el.dataset.ready = '1';

        const lat = parseFloat(el.dataset.lat);
        const lng = parseFloat(el.dataset.lng);

        const map = L.map(el, { scrollWheelZoom: false });
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
        L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 15);

        // Cards size these maps with flexbox - make sure Leaflet
        // recomputes once the container has its real dimensions.
        requestAnimationFrame(() => map.invalidateSize());
        if ('ResizeObserver' in window) {
            new ResizeObserver(() => map.invalidateSize()).observe(el);
        }
    };

    document.querySelectorAll('.location-map[data-lat][data-lng]').forEach((el) => {
        // Pages with many maps only initialise the ones near the viewport.
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        initMap(el);
                        io.disconnect();
                    }
                });
            }, { rootMargin: '200px' });
            io.observe(el);
        } else {
            initMap(el);
        }
    });
})();
</script>
@endpush
@endonce
