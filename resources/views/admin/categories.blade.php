<x-layouts.admin>
<div x-data="{
    showModal: false,
    mode: 'create',
    form: { id: null, name: '' },
    openCreate() {
        this.mode = 'create';
        this.form = { id: null, name: '' };
        this.showModal = true;
    },
    openEdit(category) {
        this.mode = 'edit';
        this.form = { id: category.id, name: category.name };
        this.showModal = true;
    }
}">
    {{--Top Bar--}}
    <x-topbar 
        title="Categories Management"
        description="Manage listing categories">
    </x-topbar>

    {{-- CATEGORIES GRID --}}
    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <x-success class="mb-4" />
        <x-error class="mb-4" />

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-indigo-900 text-lg">All Categories</h2>
            <button @click="openCreate()"
                class="bg-indigo-900 text-white text-sm px-4 py-2 rounded-lg hover:bg-[#2C3399] transition">
                + Add Category
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($categories as $category)
            <div class="bg-white rounded-xl border border-indigo-100 p-4 flex justify-between items-center"
                x-data="{ menuOpen: false }">
                <div>
                    <p class="text-sm font-medium text-indigo-900">{{ $category->name }}</p>
                    <p class="text-xs text-indigo-400 mt-1">{{ $category->listings_count }} listings</p>
                </div>

                <div class="relative">
                    <button @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                        class="text-indigo-400 hover:text-indigo-700 p-1">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>

                    <div x-show="menuOpen" x-transition
                        class="absolute right-0 mt-1 w-32 bg-white border border-indigo-100 rounded-lg shadow-sm z-10"
                        style="display:none">
                        <button @click="openEdit({{ $category->toJson() }}); menuOpen = false"
                            class="w-full text-left px-3 py-2 text-sm text-indigo-900 hover:bg-indigo-50">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('admin.categories.delete', $category->id) }}"
                            onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-indigo-600 col-span-full text-center py-6">No categories found.</p>
            @endforelse
        </div>
    </div>

    {{-- CREATE / EDIT MODAL --}}
    <div x-show="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        style="display:none">

        <div class="absolute inset-0 bg-black opacity-50" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm z-10">

            <button @click="showModal = false"
                class="absolute top-3 right-4 text-indigo-900 text-xl hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-indigo-900 mb-4" x-text="mode === 'create' ? 'Add Category' : 'Edit Category'"></h2>

            <form :action="mode === 'create' ? '{{ route('admin.categories.store') }}' : `/admin/categories/${form.id}`" method="POST">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <label class="text-sm text-indigo-900 font-medium">Category Name</label>
                <input type="text" name="name" x-model="form.name" required
                    class="w-full mt-1 mb-4 border border-indigo-200 rounded-lg px-3 py-2 text-sm text-indigo-900 focus:outline-none focus:border-indigo-400">

                <div class="text-right">
                    <button type="submit"
                        class="bg-indigo-900 text-white px-5 py-2 rounded-lg text-sm hover:bg-[#2C3399]">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.admin>