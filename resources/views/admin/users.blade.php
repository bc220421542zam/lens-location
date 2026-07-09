<x-layouts.admin>
<div x-data="{
    showModal: false,
    selectedUser: null,
    openUser(user) {
        this.selectedUser = user;
        this.showModal = true;
    }
}">
    {{--Top bar--}}
    <x-topbar 
        title="Users Management"
        description="Manage all registered users">
    </x-topbar>
    {{--Filters--}}
    <x-adminComponents.filters-users />

    {{-- Users Table --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
    <x-success class="mb-4" />
    <x-error class="mb-4" />
    
        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Users</h2>
        </div>

        <div class="overflow-x-auto">
            <div class="bg-white rounded-xl shadow-sm border border-r-3 border-indigo-400 overflow-hidden">

    {{-- HEADER --}}
      <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[720px]">
            <thead>
                <tr class="text-[11px] uppercase tracking-wide text-indigo-900 border-b border-indigo-100 bg-indigo-50/60">
                    <th class="py-3 px-4 font-medium">Sr. No.</th>

                    @php
                        $currentSort = request('sort');
                        $currentDirection = request('direction', 'asc');

                        $sortLink = function($column) use ($currentSort, $currentDirection) {
                            $nextDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection]);
                        };
                    @endphp

                    <th class="px-2 font-medium">
                        <a href="{{ $sortLink('first_name') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                            Name
                            @if($currentSort === 'first_name')
                                <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                            @else
                                <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                            @endif
                        </a>
                    </th>

                    <th class="px-2 font-medium">
                        <a href="{{ $sortLink('email') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                            Email
                            @if($currentSort === 'email')
                                <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                            @else
                                <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                            @endif
                        </a>
                    </th>

                    <th class="px-2 font-medium">
                        <a href="{{ $sortLink('role') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                            Role
                            @if($currentSort === 'role')
                                <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                            @else
                                <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                            @endif
                        </a>
                    </th>

                    <th class="px-2 font-medium">
                        <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1 hover:text-indigo-600">
                            Status
                            @if($currentSort === 'status')
                                <i class="fa-solid fa-sort-{{ $currentDirection === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                            @else
                                <i class="fa-solid fa-sort text-[10px] text-indigo-300"></i>
                            @endif
                        </a>
                    </th>

                    <th class="px-2 font-medium text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-t border-indigo-50 {{ $loop->even ? 'bg-indigo-50/40' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                    <td class="py-3 px-4 text-sm text-indigo-700">
                        {{ $users->firstItem() + $loop->index }}
                    </td>
                    <td class="py-3 px-4 text-sm font-medium text-indigo-900">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </td>
                    <td class="px-2 text-sm text-indigo-700">{{ $user->email }}</td>
                    <td class="px-2 text-sm text-indigo-700">{{ $user->role?->value ?? 'N/A' }}</td>
                    <td class="px-2 text-sm">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $user->status?->value === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->status?->value === 'active' ? 'bg-green-600' : 'bg-red-600' }}"></span>
                            {{ ucfirst($user->status?->value ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="px-2 py-3">
                        <div class="flex items-center justify-center gap-2">

                            {{-- VIEW --}}
                            <button @click="openUser({{ $user->toJson() }})"
                                title="View"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </button>

                            {{-- ACTIVATE / DEACTIVATE TOGGLE --}}
                            @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                    @csrf
                                    <button type="submit"
                                            title="{{ $user->status->value === 'active' ? 'Deactivate User' : 'Activate User' }}"
                                            class="w-8 h-8 flex items-center justify-center transition hover:opacity-80">
                                        @if($user->status->value === 'active')
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-7 h-4">
                                                <rect x="0" y="0" width="36" height="20" rx="10" fill="#09913b"/>
                                                <circle cx="26" cy="10" r="7" fill="white"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-7 h-4">
                                                <rect x="0" y="0" width="36" height="20" rx="10" fill="#e73636"/>
                                                <circle cx="10" cy="10" r="7" fill="white"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            @else
                                {{-- CURRENT LOGGED-IN USER --}}
                                <span title="This is your account"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        class="w-4 h-4">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                                    </svg>
                                </span>
                            @endif

                            {{-- DELETE --}}
                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}"
                                onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    title="Delete"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-indigo-400 text-sm">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
        </div>

        <div class="mt-4">{{ $users->appends(request()->query())->links() }}</div>

    </div>

    {{-- MODAL --}}
    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="display:none">

        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        {{-- Pop-up card on view --}}
        <div class="relative card card-transition bg-white rounded-2xl shadow-xl p-6 w-full max-w-md z-10 max-h-[90vh] overflow-y-auto">

            <button @click="showModal = false"
                class="absolute btn-trnasition top-3 right-4 text-indigo-900 text-xl hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-indigo-900 mb-4">User Detail</h2>

            <template x-if="selectedUser">
                <div class="space-y-2">

                    <div class="flex justify-center mb-4">
                        <img :src="selectedUser.profile_picture
                            ? '/storage/' + selectedUser.profile_picture
                            : '/images/default-avatar.png'"
                            class="w-20 h-20 rounded-full object-cover border border-indigo-200 card-transition shade">
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">First Name</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedUser.first_name"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Last Name</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedUser.last_name"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Email</span>
                        <span class="text-indigo-900 text-sm font-medium break-all ml-4" x-text="selectedUser.email"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Phone</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedUser.phone ?? 'N/A'"></span>
                    </div>

                    <div class="flex justify-between border-b border-indigo-100 pb-2">
                        <span class="text-indigo-900 text-sm">Role</span>
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedUser.role"></span>
                    </div>

                    <div class="flex justify-between pb-2">
                        <span class="text-indigo-900 text-sm">Status</span>
                        <span class="text-sm font-medium"
                            :class="selectedUser.status === 'active' ? 'text-green-500' : 'text-red-400'"
                            x-text="selectedUser.status ?? 'N/A'">
                        </span>
                    </div>

                </div>
            </template>

            <div class="mt-5 text-right">
                <button @click="showModal = false"
                    class="bg-indigo-900 btn-transition text-white px-5 py-2 rounded-lg text-sm hover:bg-[#2C3399]">
                    Close
                </button>
            </div>

        </div>
    </div>

</div>
</x-layouts.admin>