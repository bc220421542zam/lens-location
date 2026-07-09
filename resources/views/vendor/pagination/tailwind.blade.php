@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">
            {{-- mobile prev/next unchanged --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-800 bg-[#EEEFF7] cursor-not-allowed leading-5 rounded-lg shadow-sm">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-900 bg-white leading-5 rounded-lg shadow-sm hover:bg-gray-200 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-900 bg-white leading-5 rounded-lg shadow-sm hover:bg-gray-200 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-800 bg-[#EEEFF7] cursor-not-allowed leading-5 rounded-lg shadow-sm">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm text-indigo-800 leading-5">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();

                    // Only show: first page, current page, last page.
                    $pagesToShow = collect([1, $current, $last])
                        ->filter(fn ($p) => $p >= 1 && $p <= $last)
                        ->unique()
                        ->sort()
                        ->values();
                @endphp

                <span class="inline-flex items-center gap-1 rtl:flex-row-reverse bg-[#EEEFF7] rounded-xl p-1">

                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span class="w-9 h-9 flex items-center justify-center text-indigo-800 cursor-not-allowed rounded-lg shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-9 h-9 flex items-center justify-center text-indigo-900 bg-white rounded-lg shadow-sm hover:bg-gray-200 transition ease-in-out duration-150">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Manually built page list with forced ellipsis --}}
                    @php $previousPage = null; @endphp
                    @foreach ($pagesToShow as $page)

                        @if ($previousPage !== null && $page - $previousPage > 1)
                            <span class="w-9 h-9 flex items-center justify-center text-sm font-medium text-indigo-400 cursor-default">...</span>
                        @endif

                        @if ($page == $current)
                            <span class="w-9 h-9 flex items-center justify-center text-sm font-semibold text-white bg-[#2C3399] rounded-lg cursor-default shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="w-9 h-9 flex items-center justify-center text-sm font-medium text-indigo-900 bg-white rounded-lg shadow-sm hover:bg-gray-200 transition ease-in-out duration-150">
                                {{ $page }}
                            </a>
                        @endif

                        @php $previousPage = $page; @endphp
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-9 h-9 flex items-center justify-center text-indigo-900 bg-white rounded-lg shadow-sm hover:bg-gray-200 transition ease-in-out duration-150">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="w-9 h-9 flex items-center justify-center text-indigo-800 cursor-not-allowed rounded-lg shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif

                </span>
            </div>
        </div>
    </nav>
@endif