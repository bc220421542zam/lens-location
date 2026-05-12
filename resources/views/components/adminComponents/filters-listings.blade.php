@props(['categories'])
<form method="GET" action="{{ route('admin.listings') }}"
                  class="flex flex-col sm:flex-row sm:items-center gap-2 grid grid-cols-1 sm:grid-cols-5 mb-4">

                <div class="relative flex items-center flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 text-indigo-800 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by title, city, or owner"
                           class="border border-indigo-900 pl-4 pr-8 py-1 rounded-xl shade outline-none w-full text-sm">
                </div>

                <select name="status"
                        class="border border-indigo-900 px-3 py-1 rounded-xl shade outline-none text-sm">
                    <option value="">All status</option>
                    <option value="pending"  @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>

                <select name="category"
                        class="border border-indigo-900 px-3 py-1 rounded-xl shade outline-none text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-indigo-900 text-white text-sm px-4 py-1 rounded-xl hover:bg-[#2C3399]">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('admin.listings') }}"
                       class="text-sm text-indigo-700 px-3 py-1 rounded-xl border border-indigo-300 hover:bg-indigo-50">
                        Reset
                    </a>
                @endif
            </form>