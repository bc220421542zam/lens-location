@props(['links' => []])

{{--
    Responsive sidebar shared by the admin / owner / customer layouts.

    < lg : off-canvas drawer. Hidden by default, slides in over the content
           (with a backdrop) when the navbar hamburger sets `sidebarOpen`.
    >= lg: a normal in-flow column that is always visible.

    `sidebarOpen` lives on <body> in the layouts; the drawer is positioned
    against the `relative` flex row that sits under the top navbar.
--}}

{{-- BACKDROP (mobile / tablet only) --}}
<div x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="absolute inset-0 bg-black/40 z-20 lg:hidden"
    aria-hidden="true"
    style="display:none">
</div>

{{-- SIDEBAR --}}
<aside
    id="app-sidebar"
    aria-label="Sidebar navigation"
    @keydown.escape.window="sidebarOpen = false"
    @resize.window="if (window.innerWidth >= 1024) sidebarOpen = false"
    class="absolute lg:static inset-y-0 left-0 z-30 h-full shrink-0
           w-64 max-w-[80vw]
           bg-indigo-900 text-white flex flex-col p-2 gap-1
           overflow-y-auto overflow-x-hidden overscroll-contain
           shadow-2xl lg:shadow-none
           -translate-x-full lg:translate-x-0
           transition-transform duration-300 ease-in-out motion-reduce:transition-none"
    :class="{ '-translate-x-full': ! sidebarOpen, 'translate-x-0': sidebarOpen }">

    {{-- Drawer header — only needed while the sidebar floats over the content --}}
    <div class="flex items-center justify-between px-2 py-1 mb-1 lg:hidden">
        <span class="text-xs font-semibold uppercase tracking-wide text-indigo-200">Menu</span>
        <button type="button"
            @click="sidebarOpen = false"
            class="p-1.5 rounded-lg text-indigo-200 hover:bg-[#2C3399] hover:text-white transition-colors"
            aria-label="Close navigation">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    @foreach ($links as $link)
        @php($active = request()->routeIs($link['route']))
        <a href="{{ route($link['route']) }}"
            @click="sidebarOpen = false"
            @if($active) aria-current="page" @endif
            class="flex items-center gap-3 py-2 px-2 rounded-lg transition-colors whitespace-nowrap
            {{ $active
                ? 'bg-[#2C3399] text-white'
                : 'text-[#EEEFF7] hover:bg-[#2C3399] hover:text-white' }}">
            <i class="fa-solid {{ $link['icon'] }} w-5 text-center shrink-0 text-base"></i>
            <span class="text-sm font-medium">{{ $link['label'] }}</span>
        </a>
    @endforeach
</aside>
