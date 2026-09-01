{{-- Ambient background decoration for the auth pages.
     Purely visual: sits behind the card (z-0, wrapper is z-10) and never
     intercepts pointer events, so the forms are untouched. --}}
<div class="absolute inset-0 z-0 pointer-events-none select-none" aria-hidden="true">
    {{-- Soft gradient blobs --}}
    <div class="absolute -top-28 -left-28 w-96 h-96 rounded-full bg-indigo-400/35 blur-3xl"></div>
    <div class="absolute top-1/4 -right-36 w-[30rem] h-[30rem] rounded-full bg-indigo-300/30 blur-3xl"></div>
    <div class="absolute -bottom-36 left-1/3 w-[26rem] h-[26rem] rounded-full bg-blue-300/25 blur-3xl"></div>

    {{-- Dotted grid --}}
    <div class="absolute inset-0 opacity-50"
         style="background-image: radial-gradient(circle, rgba(49, 46, 129, 0.12) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

    {{-- Concentric rings behind the card --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[36rem] h-[36rem] rounded-full border-2 border-dashed border-indigo-400/25"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[46rem] h-[46rem] rounded-full border border-indigo-300/20"></div>

    {{-- Floating accents --}}
    <i class="fa-solid fa-camera-retro absolute top-[18%] left-[12%] text-4xl text-indigo-400/20 -rotate-15"></i>
    <i class="fa-solid fa-camera absolute bottom-[16%] right-[10%] text-5xl text-indigo-500/15 rotate-12"></i>
    <i class="fa-solid fa-star absolute top-[30%] right-[18%] text-xl text-indigo-400/30"></i>
    <i class="fa-solid fa-star absolute bottom-[28%] left-[16%] text-sm text-indigo-500/25"></i>

    {{-- Floating dots --}}
    <div class="absolute top-[22%] right-[30%] w-3 h-3 rounded-full bg-indigo-500/30"></div>
    <div class="absolute bottom-[30%] left-[28%] w-2 h-2 rounded-full bg-blue-500/30"></div>
    <div class="absolute top-[60%] left-[10%] w-2.5 h-2.5 rounded-full bg-blue-400/30"></div>
</div>
