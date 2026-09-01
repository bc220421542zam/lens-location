{{--
    Shallow preview card for guests: image, title and city only. Links to
    login — full details stay behind auth.

    <x-location-preview-card title="Rooftop Studio" image="..." city="Karachi" href="..." />
--}}
@props([
    'title',
    'image' => null,
    'city'  => null,
    'href'  => '#',
])

<article class="group flex flex-col overflow-hidden rounded-2xl bg-white border border-indigo-100 shadow-lg card-transition">
    <a href="{{ $href }}" class="relative block aspect-[4/3] overflow-hidden bg-indigo-50"
       aria-label="Log in to view {{ $title }}">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $title }}"
                 loading="lazy" decoding="async"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-camera text-5xl text-indigo-200" aria-hidden="true"></i>
            </div>
        @endif

        <span class="absolute inset-0 flex items-center justify-center bg-indigo-950/0 group-hover:bg-indigo-950/40 transition-colors duration-300" aria-hidden="true">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1.5 text-xs font-medium text-indigo-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <i class="fa-solid fa-lock" aria-hidden="true"></i> Login to view
            </span>
        </span>
    </a>

    <div class="flex flex-col flex-1 p-4">
        <h3 class="font-semibold text-indigo-950 leading-snug">{{ $title }}</h3>
        <p class="mt-1 text-sm text-gray-500">
            <i class="fa-solid fa-location-dot mr-1.5 text-indigo-400" aria-hidden="true"></i>{{ $city }}
        </p>
        <p class="mt-3 pt-3 border-t border-indigo-100 text-xs font-medium text-indigo-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-lock" aria-hidden="true"></i> Log in to view details
        </p>
    </div>
</article>
