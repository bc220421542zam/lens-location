<x-layouts.admin>
<div>
    <x-topbar 
        title="Reviews Management"
        description="Moderate customer reviews">
    </x-topbar>

    <div class="shade card chart-transition bg-[#EEEFF7] p-4 rounded-2xl border-l-3 border-indigo-400">
        <x-success class="mb-4" />

        <h2 class="font-bold text-indigo-900 text-lg mb-4">All Reviews</h2>

        <div class="flex flex-col gap-3">
            @forelse($reviews as $review)
            <div class="bg-white rounded-xl p-4 flex justify-between items-start {{ !$review->is_visible ? 'opacity-50' : '' }}">
                <div>
                    <p class="text-sm font-medium text-indigo-900">
                        {{ $review->customer->first_name ?? 'Deleted user' }}
                        <span class="text-indigo-400 font-normal">on {{ $review->location->title ?? 'Deleted listing' }}</span>
                    </p>
                    <p class="text-xs text-indigo-500 mt-2 max-w-md">{{ $review->comment ?: 'No comment left.' }}</p>
                    @if(!$review->is_visible)
                        <span class="badge-inline bg-red-100 text-red-600 mt-2 inline-block">Hidden</span>
                    @endif
                </div>

                <div class="flex flex-col items-end gap-2 shrink-0">
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                        @endfor
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.reviews.toggle', $review->id) }}">
                            @csrf
                            <button type="submit" class="text-xs text-indigo-600 hover:underline">
                                {{ $review->is_visible ? 'Hide' : 'Restore' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.delete', $review->id) }}"
                            onsubmit="return confirm('Delete this review permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-indigo-400 text-center py-6">No reviews found.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</div>
</x-layouts.admin>