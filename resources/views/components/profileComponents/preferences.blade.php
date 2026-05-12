 {{--preferences--}}
            <div class="flex items-center gap-4">

            {{-- Theme --}}
            <div class="relative group">
                <button class="flex items-center gap-2 text-sm text-indigo-900">
                    <i class="fa-solid fa-palette"></i>
                    Theme
                </button>

                <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 w-40 p-2 z-50">
                    <a href="{{ route('theme.set', 'light') }}"
                    class="block px-3 py-2 rounded hover:bg-gray-100 text-sm">
                        Light Mode
                    </a>

                    <a href="{{ route('theme.set', 'dark') }}"
                    class="block px-3 py-2 rounded hover:bg-gray-100 text-sm">
                        Dark Mode
                    </a>
                </div>
            </div>

            {{-- Language --}}
            <div class="relative group">
                <button class="flex items-center gap-2 text-sm text-indigo-900">
                    <i class="fa-solid fa-language"></i>
                    Language
                </button>

                <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 w-40 p-2 z-50">
                    <a href="{{ route('lang.set', 'en') }}"
                    class="block px-3 py-2 rounded hover:bg-gray-100 text-sm">
                        English
                    </a>

                    <a href="{{ route('lang.set', 'ur') }}"
                    class="block px-3 py-2 rounded hover:bg-gray-100 text-sm">
                        Urdu
                    </a>
                </div>
            </div>

        </div>