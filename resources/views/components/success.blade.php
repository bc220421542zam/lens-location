@if(session('success'))
    <div id="flash-message" class="fixed top-25 left-170 z-50 flex items-center gap-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-sm font-medium px-5 py-3 rounded-2xl shadow-xl animate-fade border border-green-300/40">

        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>

        <span class="tracking-wide">
            {{ session('success') }}
        </span>

    </div>
@endif


<script>
    setTimeout(() => {
        let msg = document.getElementById('flash-message');
        if (msg) {
            msg.style.transition = "opacity 0.5s ease";
            msg.style.opacity = "0";
 
            setTimeout(() => msg.remove(), 500);
        }
    }, 1000);
</script>