@props([
    'latitude' => 0,
    'longitude' => 0,
])

{{-- Map Location --}}
<div class="card chart-transition mb-4 rounded-2xl border border-l-3 border-indigo-400 p-6"
     x-data="listingMapPicker({
         latitude: {{ (float) $latitude }},
         longitude: {{ (float) $longitude }},
     })"
     x-on:listing-map-geocode.window="geocode()">
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-indigo-900">Map Location</h3>
        <span x-text="pinStatus" class="text-xs text-gray-400"></span>
    </div>
    <p class="text-sm text-gray-400 mb-3">
        Type the street address and city — we'll try to find it automatically.
        Drag the pin (or click the map) to correct the exact spot.
    </p>

    {{-- The values submitted with the form --}}
    <input type="hidden" name="latitude" x-model="latitude">
    <input type="hidden" name="longitude" x-model="longitude">

    <div id="listing-map"
         class="w-full h-72 rounded-xl border border-indigo-200 overflow-hidden z-0"></div>

    <p class="text-xs text-gray-400 mt-2">
        Coordinates: <span x-text="latitude.toFixed(6)"></span>, <span x-text="longitude.toFixed(6)"></span>
    </p>
    <p x-show="geocodeError" x-cloak class="text-xs text-red-500 mt-1" x-text="geocodeError"></p>

    @error('latitude')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    @error('longitude')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    // Pakistan as the default viewport until a pin is placed.
    const DEFAULT_CENTER = [30.3753, 69.3451];
    const DEFAULT_ZOOM   = 5;
    const PIN_ZOOM       = 15;

    window.listingMapPicker = function (config) {
        return {
            latitude: config.latitude,
            longitude: config.longitude,
            pinStatus: 'No pin yet',
            geocodeError: '',
            map: null,
            marker: null,

            init() {
                if (typeof L === 'undefined') {
                    this.geocodeError = 'The map could not be loaded. Enter coordinates manually.';
                    return;
                }

                this.map = L.map('listing-map', { scrollWheelZoom: false });

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                }).addTo(this.map);

                // A stored (0,0) means the row predates coordinates - not a pin.
                if (this.latitude !== 0 || this.longitude !== 0) {
                    this.placePin(this.latitude, this.longitude, true);
                } else {
                    this.map.setView(DEFAULT_CENTER, DEFAULT_ZOOM);
                }

                // Click anywhere moves the pin there.
                this.map.on('click', (event) => this.placePin(event.latlng.lat, event.latlng.lng));
            },

            placePin(lat, lng, centerOnly = false) {
                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                } else {
                    this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                    this.marker.on('dragend', (event) => {
                        const pos = event.target.getLatLng();
                        this.setCoordinates(pos.lat, pos.lng);
                    });
                }

                this.setCoordinates(lat, lng);

                if (centerOnly) {
                    this.map.setView([lat, lng], PIN_ZOOM);
                } else {
                    this.map.panTo([lat, lng]);
                }
            },

            setCoordinates(lat, lng) {
                this.latitude  = +lat.toFixed(7);
                this.longitude = +lng.toFixed(7);
                this.pinStatus = 'Pin placed';
            },

            async geocode() {
                const address = document.querySelector('input[name="address"]')?.value.trim() || '';
                const city    = document.querySelector('input[name="city"]')?.value.trim() || '';

                if (! address || ! city) {
                    return;
                }

                this.pinStatus = 'Looking up address...';
                this.geocodeError = '';

                try {
                    const query = `${address}, ${city}, Pakistan`;
                    const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(query);

                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    const results  = await response.json();

                    if (! results.length) {
                        this.geocodeError = 'Could not find that address. Click the map to place the pin yourself.';
                        this.pinStatus = 'Not found';
                        return;
                    }

                    this.placePin(parseFloat(results[0].lat), parseFloat(results[0].lon), true);
                } catch (error) {
                    this.geocodeError = 'Address lookup failed. Click the map to place the pin yourself.';
                    this.pinStatus = 'Lookup failed';
                }
            },
        };
    };

    // The address/city inputs live outside the picker's Alpine scope, so their
    // debounced input is bridged in via a window event.
    const debounce = (fn, wait) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), wait);
        };
    };

    ['input[name="address"]', 'input[name="city"]'].forEach((selector) => {
        const input = document.querySelector(selector);
        if (input) {
            input.addEventListener('input', debounce(() => {
                window.dispatchEvent(new CustomEvent('listing-map-geocode'));
            }, 600));
        }
    });
})();
</script>
@endpush
