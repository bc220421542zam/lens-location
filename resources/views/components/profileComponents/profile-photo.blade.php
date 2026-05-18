
                <div class="card flex flex-col items-center py-6">

                    <div class="relative w-20 h-20 mb-3">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                alt="Profile"
                                class="w-20 h-20 rounded-full object-cover border-2 border-indigo-200">
                        @else
                            <div class="w-20 h-20 rounded-full bg-indigo-100 border-2 border-indigo-200 flex items-center justify-center">
                                <i class="fa-solid fa-user text-3xl text-indigo-400"></i>
                            </div>
                        @endif

                        {{-- Camera overlay --}}
                        <label for="profile_pic_trigger"
                            class="absolute bottom-0 right-0 w-6 h-6 bg-indigo-900 rounded-full flex items-center justify-center cursor-pointer hover:bg-[#2C3399] transition">
                            <i class="fa-solid fa-camera text-white" style="font-size:10px"></i>
                        </label>
                    </div>

                    <p class="font-semibold text-indigo-900 text-center">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 text-center">
                        {{ auth()->user()->email }}
                    </p>
                     <div class="hidden sm:flex flex-col items-center gap-0.5 leading-tight my-2">
                    
                    <span class="text-xs font-medium text-white bg-indigo-600 rounded-lg px-3 py-1 capitalize mt-0.5">
                        {{ auth()->user()->role ?? 'User' }}
                    </span>
                    </div>
                </div>