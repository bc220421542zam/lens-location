<x-layouts.admin>
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
                    <td class="{{ ($user->status ?? '') === 'active' ? 'text-green-500' : 'text-red-400' }}">
                        {{ $user->status ?? 'N/A' }}
                    </td>
                    <td class="flex items-center gap-3 py-2">
                        <button>
                            <i class="fa-regular fa-eye text-indigo-700" style="font-size:15px"></i>
                        </button>
                        <button>
                            <i class="fa-regular fa-trash-can text-red-600" style="font-size:15px"></i>
                        </button>
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

</x-layouts.admin>