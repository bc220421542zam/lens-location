@props([
    'title' => '',
    'description' => '',
    'actions' => '',
    'btn' => ''
])

{{-- TOP BAR --}}
<div class="top-bar flex flex-wrap items-center justify-between gap-3">

    <div>
        <h1 class="title text-indigo-900">
            {{ $title }}
        </h1>

        <p class="text-sm text-gray-500">
            {{ $description }}
        </p>
    </div>

    <div>
        {{ $actions }}
    </div>

</div>