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
        $reviews = Review::query()
            ->whereHas('location', fn ($q) => $q->where('user_id', Auth::id()))
            ->with(['customer', 'location'])
            ->where('is_visible', true)
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('owner.reviews', compact('reviews'));
    }
}