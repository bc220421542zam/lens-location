{{-- Login with Facebook --}}
<a href="{{ route('auth.facebook') }}"
    class="w-full flex items-center justify-center gap-2 border border-gray-300 rounded-lg py-2.5 mb-4
    bg-gray-200 hover:bg-gray-100 transition-colors duration-200 hover:shadow-lg ease-in-out">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="h-5 w-5" fill="#1877F2">
        <path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
    </svg>
    <span class="text-sm text-gray-700">Continue with Facebook</span>
</a>
