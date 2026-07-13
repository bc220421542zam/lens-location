{{-- REVIEW FORM --}}
<div class="card chart-transition mt-4" x-data="{ rating: {{ $userReview->rating ?? 0 }} }">
    <h3 class="font-semibold text-indigo-900 mb-4">
        {{ isset($userReview) ? 'Update your review' : 'Leave a review' }}
    </h3>

    <form method="POST" action="{{ route('customer.listings.reviews.store', $location->id) }}">
        @csrf

        {{-- Star rating picker --}}
        <label class="text-xs text-indigo-900 mb-2 block">Your rating</label>
        <div class="flex gap-1 mb-4">
            @for ($i = 1; $i <= 5; $i++)
                <button type="button"
                    @click="rating = {{ $i }}"
                    class="text-2xl transition"
                    :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'">
                    <i class="fa-solid fa-star"></i>
                </button>
            @endfor
        </div>
        <input type="hidden" name="rating" x-model="rating">
        @error('rating')
            <p class="text-red-500 text-xs -mt-3 mb-3">{{ $message }}</p>
        @enderror

        {{-- Comment --}}
        <label class="text-xs text-indigo-900 mb-1 block">Comment (optional)</label>
        <textarea
            name="comment"
            rows="3"
            placeholder="Share details of your experience..."
            class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                   @error('comment') border-red-400 @enderror">{{ old('comment', $userReview->comment ?? '') }}</textarea>
        @error('comment')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror

        <div class="flex justify-end mt-4">
            <button type="submit"
                x-bind:disabled="rating === 0"
                :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                class="px-6 py-2 rounded-lg bg-indigo-900 text-white text-sm hover:bg-indigo-800 transition-colors btn-transition">
                {{ isset($userReview) ? 'Update review' : 'Submit review' }}
            </button>
        </div>
    </form>
</div>

{{-- EXISTING REVIEWS LIST --}}
<div class="card chart-transition mt-4">
    <h3 class="font-semibold text-indigo-900 mb-4">Reviews</h3>

    <div class="flex flex-col gap-3">
        @forelse($location->reviews()->where('is_visible', true)->with('customer')->latest()->get() as $review)
        <div class="bg-white rounded-xl p-3 flex justify-between items-start">
            <div>
                <p class="text-sm font-medium text-indigo-900">{{ $review->customer->first_name ?? 'Customer' }}</p>
                <p class="text-xs text-gray-500 mt-1 max-w-md">{{ $review->comment ?: 'No comment left.' }}</p>
            </div>
            <div class="flex gap-0.5 shrink-0">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                @endfor
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">No reviews yet. Be the first!</p>
        @endforelse
    </div>
</div>