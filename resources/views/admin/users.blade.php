<x-layouts.admin>
    <div class="max-w-5xl" x-data="{ 
    showModal: false, 
    selectedUser: null,
    openUser(user) {
        this.selectedUser = user;
        this.showModal = true;
    }
}">

    <p class="title text-indigo-900">User Management</p>

    <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Users</h2>
            <div class="relative flex items-center">
                <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-800 text-sm"></i>
                <input type="text" placeholder="Search..."
                    class="border border-indigo-900 pl-4 pr-1 py-1 rounded-xl shade outline-none">
            </div>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-indigo-400 text-indigo-900">
                    <th class="py-2">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-indigo-400">
                    <td class="py-2 text-indigo-900">{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td class="text-indigo-900">{{ $user->email }}</td>
                    <td class="text-indigo-900">{{ $user->role ?? 'N/A' }}</td>
                    <td class="{{ ($user->status ?? '') === 'active' ? 'text-green-700' : 'text-red-600' }}">
                        {{ $user->status ?? 'N/A' }}
                    </td>
                    <td class="flex items-center gap-3 py-2">

                        {{-- VIEW --}}
                        <button @click="openUser({{ $user->toJson() }})"
                            class="text-indigo-900 hover:text-indigo-900">
                            <i class="fa-regular fa-eye" style="font-size:15px"></i>
                        </button>

                        {{-- TOGGLE STATUS — active/blocked --}}
                        <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                            @csrf
                            <button type="submit" class="{{ $user->status === 'active' ? 'text-green-800' : 'text-green-800' }} hover:opacity-75">
                                <i class="fa-solid {{ $user->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}" 
                                style="font-size:18px"></i>
                            </button>
                        </form>

                        {{-- DELETE --}}
                        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}"
                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">
                                <i class="fa-regular fa-trash-can" style="font-size:15px"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-indigo-800">
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

        {{-- POPUP MODAL --}}
    <div x-show="showModal" 
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display:none">

        {{-- Dark background overlay --}}
        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        {{-- Modal box --}}
        <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-md z-10">

            {{-- Close button --}}
            <button @click="showModal = false"
                class="absolute top-3 right-4 text-indigo-900 hover:text-indigo-900 text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            {{-- Modal content --}}
            <h2 class="text-lg font-bold text-indigo-900 mb-4">User Detail</h2>

            <template x-if="selectedUser">
                <div class="space-y-3">

                    {{-- Profile picture --}}
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
                        <span class="text-indigo-900 text-sm font-medium" x-text="selectedUser.email"></span>
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

            {{-- Close button at bottom --}}
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