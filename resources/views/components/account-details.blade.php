 {{-- ACCOUNT DETAILS --}}
            <div class="shade bg-[#EEEFF7] rounded-2xl p-4">
                <p class="text-xs font-semibold text-indigo-900 uppercase tracking-wide mb-3">Account Details</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Status</span>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Active</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Member since</span>
                        <span class="text-xs text-indigo-900 font-medium">
                            {{ auth()->user()->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>