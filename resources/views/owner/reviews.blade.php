<x-layouts.owner>
<div>
    <x-topbar 
        title="Reviews"
        description="What customers are saying about your listings">
    </x-topbar>

    <div class="card chart-transition mb-4 rounded-2xl border-l-3 border-indigo-400 p-4 bg-[#EEEFF7]">
        <div class="flex flex-col gap-3">
            @forelse($reviews as $review)
            <div class="bg-white rounded-xl p-4 flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-indigo-900">
                        {{ $review->customer->first_name ?? 'Customer' }}
                        <span class="text-gray-500 font-normal">for</span>
                        @if($review->location)
                            <a href="{{ route('owner.locations.show', $review->location->id) }}" class="text-indigo-400 font-normal hover:underline">
                                {{ $review->location->title }}
                            </a>
                        @else
                            <span class="text-indigo-400 font-normal">Deleted listing</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-2 max-w-md">{{ $review->comment ?: 'No comment left.' }}</p>
                </div>
                <div class="flex gap-0.5 shrink-0">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                    @endfor
                </div>
            </div>
            @empty
            <p class="text-sm text-indigo-400 text-center py-6">No reviews yet.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</div>
</x-layouts.owner>