@props([
    'title' => '',
    'description' => '',
    'actions' => '',
    'btn' => ''
])

{{-- TOP BAR: heading on the first line, sub-heading below it, actions on the right. --}}
<div class="top-bar flex flex-wrap items-center justify-between gap-3">

    <div>
        <h1 class="title mb-0 text-indigo-900">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-1 text-sm text-gray-500">
                {{ $description }}
            </p>
        @endif
    </div>

    <div>
        {{ $actions }}
    </div>

</div>