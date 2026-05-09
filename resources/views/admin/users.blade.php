<x-layouts.admin>
<div x-data="{
    showModal: false,
    selectedUser: null,
    openUser(user) {
        this.selectedUser = user;
        this.showModal = true;
    }
}">

    <p class="title text-indigo-900">User Management</p>

    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        <div class="flex flex-col gap-3 mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Users</h2>

            <form method="GET" action="{{ route('admin.users') }}"
                  class="flex flex-col sm:flex-row sm:items-center gap-2">

                <div class="relative flex items-center flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-800 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or email"
                           class="border border-indigo-900 pl-4 pr-8 py-1 rounded-xl shade outline-none w-full text-sm">
                </div>

                <select name="role"
                        class="border border-indigo-900 px-3 py-1 rounded-xl shade outline-none text-sm">
                    <option value="">All roles</option>
                    <option value="admin"        @selected(request('role') === 'admin')>Admin</option>
                    <option value="owner"        @selected(request('role') === 'owner')>Owner</option>
                    <option value="photographer" @selected(request('role') === 'photographer')>Photographer</option>
                </select>

                <select name="status"
                        class="border border-indigo-900 px-3 py-1 rounded-xl shade outline-none text-sm">
                    <option value="">All statuses</option>
                    <option value="active"  @selected(request('status') === 'active')>Active</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                </select>

                <button type="submit"
                        class="bg-indigo-900 text-white text-sm px-4 py-1 rounded-xl hover:bg-[#2C3399]">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users') }}"
                       class="text-sm text-indigo-700 px-3 py-1 rounded-xl border border-indigo-300 hover:bg-indigo-50">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[520px]">
                <thead>
                    <tr class="border-b border-indigo-400 text-indigo-900">
                        <th class="py-2 px-2 text-sm">Name</th>
                        <th class="px-2 text-sm">Email</th>
                        <th class="px-2 text-sm">Role</th>
                        <th class="px-2 text-sm">Status</th>
                        <th class="px-2 text-sm">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-indigo-400">
                        <td class="py-2 pl-2 text-sm text-indigo-900">{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td class="pl-2 text-sm text-indigo-900">{{ $user->email }}</td>
                        <td class="pl-2 text-sm text-indigo-900">{{ $user->role?->value ?? 'N/A' }}</td>
                        <td class="pl-2 text-sm {{ $user->status?->value === 'active' ? 'text-green-700' : 'text-red-600' }}">
                            {{ ucfirst($user->status?->value ?? 'N/A') }}
                        </td>
                        <td class="flex items-center gap-3 py-2 pl-2">

                            <button @click="openUser({{ $user->toJson() }})" class="text-indigo-900 hover:text-indigo-700">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </button>

                            <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                @csrf
                                <button type="submit" class="text-green-800 hover:opacity-75">
                                    <i class="fa-solid {{ $user->status->value === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }} text-lg"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.users.delete', $user->id) }}"
                                onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-indigo-800 text-sm">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>

    </div>

    {{-- MODAL --}}
    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="display:none">

        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-md z-10 max-h-[90vh] overflow-y-auto">

            <button @click="showModal = false"
                class="absolute top-3 right-4 text-indigo-900 text-xl hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-indigo-900 mb-4">User Detail</h2>

            <template x-if="selectedUser">
                <div class="space-y-3">

                    <div class="flex justify-center mb-4">
                        <img :src="selectedUser.profile_picture
                            ? '/storage/' + selectedUser.profile_picture
                            : '/images/default-avatar.png'"
                            class="w-20 h-20 rounded-full object-cover shade">
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
                    class="bg-indigo-900 text-white px-5 py-2 rounded-lg text-sm hover:bg-[#2C3399]">
                    Close
                </button>
            </div>

        </div>
    </div>

</div>
</x-layouts.admin>