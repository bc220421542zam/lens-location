{{--
    Centered section header used across the marketing pages:

    <x-section-heading id="how-it-works-heading" eyebrow="How it works"
                       title="From search to shoot in three steps"
                       subtitle="Optional supporting line" />
--}}
@props(['eyebrow' => null, 'title', 'subtitle' => null, 'id' => null])

<div class="max-w-2xl mx-auto text-center">
    @if ($eyebrow)
        <p class="text-xs sm:text-sm font-semibold uppercase tracking-widest text-indigo-600">{{ $eyebrow }}</p>
    @endif

    <h2 @if ($id) id="{{ $id }}" @endif
        class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-indigo-950">
        {{ $title }}
    </h2>

    @if ($subtitle)
        <p class="mt-4 text-gray-600 leading-relaxed">{{ $subtitle }}</p>
    @endif
</div>
