<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): View
    {
        // Opening the page counts as reviewing everything the owner can see,
        // which clears the sidebar's unread dot. Hidden (admin-moderated)
        // reviews are excluded to match the list below, so they keep their
        // dot until they become visible. toBase() skips the updated_at bump.
        Review::whereHas('location', fn ($q) => $q->where('user_id', Auth::id()))
            ->where('is_visible', true)
            ->whereNull('owner_reviewed_at')
            ->toBase()
            ->update(['owner_reviewed_at' => now()]);

        $reviews = Review::query()
            ->whereHas('location', fn ($q) => $q->where('user_id', Auth::id()))
            ->with(['customer', 'location'])
            ->where('is_visible', true)
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('owner.reviews', compact('reviews'));
    }
}