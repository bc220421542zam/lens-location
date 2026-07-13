<x-layouts.customer>
<div class="page max-w-2xl">

    <div class="flex items-center justify-between mb-6">
        <h1 class="title text-indigo-900">Leave a Review</h1>
        <a href="{{ route('customer.bookings') }}" class="text-sm text-indigo-700 hover:underline">
            &larr; Back to bookings
        </a>
    </div>

    <div class="card chart-transition" x-data="{ rating: {{ $userReview->rating ?? 0 }} }">

        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-indigo-100">
            <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-camera text-indigo-400"></i>
            </div>
            <div>
                <p class="font-semibold text-indigo-900">{{ $booking->location->title }}</p>
                <p class="text-xs text-gray-500">{{ $booking->location->city }} · Booked {{ $booking->created_at->format('M j, Y') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('customer.bookings.review.store', $booking->id) }}">
            @csrf

            <label class="text-xs text-indigo-900 mb-2 block">Your rating</label>
            <div class="flex gap-1 mb-4">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button"
                        @click="rating = {{ $i }}"
                        class="text-3xl transition"
                        :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'">
                        <i class="fa-solid fa-star"></i>
                    </button>
                @endfor
            </div>
            <input type="hidden" name="rating" x-model="rating">
            @error('rating')
                <p class="text-red-500 text-xs -mt-3 mb-3">{{ $message }}</p>
            @enderror

            <label class="text-xs text-indigo-900 mb-1 block">Comment (optional)</label>
            <textarea
                name="comment"
                rows="4"
                placeholder="Share details of your experience..."
                class="input-field shade w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                       @error('comment') border-red-400 @enderror">{{ old('comment', $userReview->comment ?? '') }}</textarea>
            @error('comment')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            <div class="flex justify-end mt-6">
                <button type="submit"
                    x-bind:disabled="rating === 0"
                    :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    class="px-6 py-2 rounded-lg bg-indigo-900 text-white text-sm hover:bg-indigo-800 transition-colors btn-transition">
                    {{ isset($userReview) ? 'Update review' : 'Submit review' }}
                </button>
            </div>
        </form>
    </div>

</div>
</x-layouts.customer>