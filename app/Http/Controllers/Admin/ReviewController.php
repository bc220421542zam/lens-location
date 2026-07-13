<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with(['customer', 'location'])
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->integer('rating')))
            ->when($request->filled('visibility'), function ($q) use ($request) {
                $q->where('is_visible', $request->string('visibility') === 'visible');
            })
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.reviews', compact('reviews'));
    }

    public function toggleVisibility(Review $review): RedirectResponse
    {
        $review->is_visible = ! $review->is_visible;
        $review->save();

        return back()->with('success', $review->is_visible ? 'Review restored.' : 'Review hidden.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}