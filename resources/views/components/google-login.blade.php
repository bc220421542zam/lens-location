{{-- Login with Google --}}
<a href="{{ route('auth.google') }}"
    class="w-full flex items-center justify-center gap-2 border border-gray-300 rounded-lg py-2.5 mb-4
    bg-gray-200 hover:bg-gray-100 transition-colors duration-200">
    <img src="{{ asset('images/search.png') }}" alt="Google" class="h-5">
    <span class="text-sm text-gray-700">Login with Google</span>
</a>
