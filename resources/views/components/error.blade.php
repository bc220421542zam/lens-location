@if(session('error'))
    <div
     class="fixed top-25 left-1/2 -translate-x-1/2 z-50
                max-w-md w-fit min-w-[250px]
                flex items-center gap-3
                bg-gradient-to-r from-red-500 to-rose-500
                text-white text-sm font-medium
                px-5 py-3 rounded-2xl shadow-xl
                animate-fade border border-red-300/40">

        <!-- Icon -->
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86l-8.4 14.49A2 2 0 003.6 21h16.8a2 2 0 001.71-3.65l-8.4-14.49a2 2 0 00-3.42 0z"/>
        </svg>

        <!-- Message -->
        <span class="break-words text-center">
            {{ session('error') }}
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
 