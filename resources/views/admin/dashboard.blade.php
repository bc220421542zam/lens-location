<x-layouts.admin>
    <p class="title text-indigo-900"> Dashboard </p>
    <div class="max-w-xl">
    {{-- CARDS --}}
    <div class="grid grid-cols-3 gap-6 mb-6">

    <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
        <div class="flex justify-between items-start">
            <h3 class="text-sm text-indigo-900 mb-3">Total Users</h3>
            <i class="fa-solid fa-users text-m text-indigo-900 shrink-0 ml-2"></i>
        </div>
        <p class="text-2xl font-bold text-indigo-900">0</p>
    </div>

    <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
        <div class="flex justify-between items-start">
            <h3 class="text-sm text-indigo-900 mb-3">Total Listings</h3>
            <i class="fa-solid fa-location-dot text-m text-indigo-900 shrink-0 ml-2"></i>
        </div>
        <p class="text-2xl font-bold text-indigo-900">0</p>
    </div>

    <div class="shade bg-[#EEEFF7] p-5 rounded-2xl">
        <div class="flex justify-between items-start">
            <h3 class="text-sm text-indigo-900 mb-3">Total Bookings</h3>
            <i class="fa-solid fa-calendar-days text-m text-indigo-900 shrink-0 ml-2"></i>
        </div>
        <p class="text-2xl font-bold text-indigo-900">0</p>
    </div>

    </div>
</div>


            {{-- TABLE --}}
            <div class="shade bg-[#EEEFF7] p-4 rounded-2xl">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-indigo-900 text-lg">Recent Listings</h2>
                    <div class="relative flex items-center">
                        <i class="fa-solid text-indigo-900 fa-magnifying-glass absolute right-3"></i>
                        <input type="text" placeholder="Search..."
                            class="border border-indigo-900 pl-4 pr-3 py-1 rounded-xl shade outline-none">
                    </div>
                </div>

                <table class="w-full text-left">

                    <thead>
                        <tr class=" border-b border-indigo-400 text-indigo-900">
                            <th class="py-2">Title</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr class=" border-b border-indigo-400">
                            <td class="py-2 text-indigo-900">Item 1</td>
                            <td class="text-indigo-900">Ali</td>
                            <td class="text-yellow-500">Pending</td>
                            <td>
                                <button ><i class="icon fa-solid fa-toggle-off"></i></button>
                                <button ><i class="icon fa-regular fa-eye" style="font-size: 15px"></i></button>
                            </td>
                        </tr>

                        <tr class=" border-b border-indigo-400">
                            <td class="py-2 text-indigo-900">Item 2</td>
                            <td class="text-indigo-900">Ahmed</td>
                            <td class="text-green-600">Approved</td>
                            <td>
                                <button ><i class="icon fa-solid fa-toggle-off"></i></button>
                                <button ><i class="icon fa-regular fa-eye" style="font-size: 15px"></i></button>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>
</x-layouts.admin>