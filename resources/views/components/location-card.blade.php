{{--
    Reusable location card. Pass plain values so it works with both Location
    models and placeholder arrays:

    <x-location-card title="Rooftop Studio" image="https://..." city="Karachi"
                     category="Studio" price="2500" rating="4.9" href="..." />
--}}
@props([
    'title',
    'image'    => null,
    'city'     => null,
    'category' => null,
    'price'    => null,
    'rating'   => null,
    'href'     => '#',
])

<article class="group flex flex-col overflow-hidden rounded-2xl bg-white border border-indigo-100
                shadow-lg card-transition">
    <a href="{{ $href }}" class="relative block aspect-[4/3] overflow-hidden bg-indigo-50"
       aria-label="View details for {{ $title }}">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $title }}"
                 loading="lazy" decoding="async"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
            <div class="absolute inset-0 bg-linear-to-t from-indigo-950/60 via-indigo-950/5 to-transparent"
                 aria-hidden="true"></div>
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fa-solid fa-camera text-5xl text-indigo-200" aria-hidden="true"></i>
            </div>
        @endif

        @if ($category)
            <span class="absolute top-3 left-3 rounded-full bg-white/90 backdrop-blur px-3 py-1 text-xs font-medium text-indigo-900 shadow-sm">
                {{ ucfirst($category) }}
            </span>
        @endif

        @if ($rating !== null)
            <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-amber-400 px-2.5 py-1 text-xs font-semibold text-amber-950 shadow-sm"
                  aria-label="Rated {{ $rating }} out of 5">
                <i class="fa-solid fa-star" aria-hidden="true"></i>{{ $rating }}
            </span>
        @endif

        @if ($price !== null)
            <span class="absolute bottom-3 left-3 rounded-full bg-white/90 backdrop-blur px-3 py-1 text-xs font-semibold text-indigo-900 shadow-sm">
                PKR {{ number_format((float) $price) }} <span class="font-normal text-gray-500">/hr</span>
            </span>
        @endif
    </a>

    <div class="flex flex-col flex-1 p-4">
        <h3 class="font-semibold text-indigo-950 leading-snug">{{ $title }}</h3>

        @if ($city)
            <p class="mt-1 text-sm text-gray-500">
                <i class="fa-solid fa-location-dot mr-1.5 text-indigo-400" aria-hidden="true"></i>{{ $city }}
            </p>
        @endif

        <span class="mt-3 pt-3 border-t border-indigo-100 inline-flex items-center justify-between text-sm font-medium text-indigo-700">
            View details
            <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true"></i>
        </span>
    </div>
</article>
