<form method="GET" action="{{ route('admin.users') }}"
                  class="flex flex-col sm:flex-row sm:items-center gap-2 grid grid-cols-1 sm:grid-cols-5 mb-4">

                <div class="relative flex items-center flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-800 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or email "
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
                    <option value="">All status</option>
                    <option value="active"  @selected(request('status') === 'active')>Active</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                </select>

                <button type="submit"
                        class="bg-indigo-900 text-white text-sm px-4 py-1 rounded-xl hover:bg-[#2C3399] shadow-2xl shadow-indigo-600/60 hover:scale-105 transition-all duration-300">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users') }}"
                       class="text-sm text-indigo-800 px-2 py-1 rounded border border-indigo-800 hover:bg-indigo-50">
                        Reset
                    </a>
                @endif
            </form>