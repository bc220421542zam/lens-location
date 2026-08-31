<x-layouts.admin>
    <x-topbar
        title="User Details"
        description="Account information and status">
    </x-topbar>

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <p class="title text-indigo-900">User Details</p>
            <a href="{{ route('admin.users') }}" class="text-sm text-indigo-700 hover:underline">
                &larr; Back to users
            </a>
        </div>

        <x-success class="mb-4" />
        <x-error class="mb-4" />

        <div class="shade card card-transition bg-[#EEEFF7] p-6 rounded-2xl">

            {{-- HEADER --}}
            <div class="flex items-center gap-4 mb-6">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/'.$user->profile_picture) }}" alt="Profile"
                         class="w-20 h-20 rounded-full object-cover border-2 border-indigo-200">
                @else
                    <div class="w-20 h-20 rounded-full bg-indigo-100 border-2 border-indigo-200 flex items-center justify-center">
                        <i class="fa-solid fa-user text-3xl text-indigo-400"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-indigo-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            {{-- DETAILS --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Name</dt>
                    <dd class="font-medium text-indigo-900">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Email</dt>
                    <dd class="font-medium text-indigo-900 break-all">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Phone</dt>
                    <dd class="font-medium text-indigo-900">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Role</dt>
                    <dd class="font-medium text-indigo-900">{{ $user->role?->value ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Status</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $user->status->value === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->status->value === 'active' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                            {{ ucfirst($user->status->value) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase underline text-indigo-800">Member Since</dt>
                    <dd class="font-medium text-indigo-900">{{ $user->created_at?->format('d M Y') ?? '—' }}</dd>
                </div>

                @if($user->role === \App\Enums\Role::Owner)
                    <div>
                        <dt class="text-xs uppercase underline text-indigo-800">Stripe Transfers</dt>
                        <dd>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                {{ $user->stripe_transfers_status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->stripe_transfers_status === 'active' ? 'Active' : 'Not connected' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase underline text-indigo-800">Listings</dt>
                        <dd class="font-medium text-indigo-900">{{ $user->locations_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase underline text-indigo-800">Transactions (as owner)</dt>
                        <dd class="font-medium text-indigo-900">{{ $user->transactions_as_owner_count }}</dd>
                    </div>
                @endif
            </dl>

            {{-- BLOCK REASON --}}
            @if($user->isBlocked())
                <dl class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <dt class="text-xs uppercase underline text-red-700 mb-1">Block Reason</dt>
                    <dd class="text-sm text-red-700">{{ $user->block_reason ?? 'No reason provided' }}</dd>
                    <dd class="text-xs text-red-400 mt-1">Blocked on {{ $user->blockedAtDisplay() ?? 'unknown date' }}</dd>
                </dl>
            @endif

        </div>
    </div>
</x-layouts.admin>
