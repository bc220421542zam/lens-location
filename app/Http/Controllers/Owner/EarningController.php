<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = Transaction::where('owner_id', auth()->id())
            ->with('booking.location')
            ->when($request->filled('search'), fn ($q) => $q->whereHas('booking.location', fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payout_status'), fn ($q) => $q->where('payout_status', $request->string('payout_status')))
            ->when($request->filled('min_amount'), fn ($q) => $q->where('amount', '>=', $request->float('min_amount')))
            ->when($request->filled('max_amount'), fn ($q) => $q->where('amount', '<=', $request->float('max_amount')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_earned'   => Transaction::where('owner_id', auth()->id())->where('status', 'paid')->sum('owner_earning'),
            'pending_payout' => Transaction::where('owner_id', auth()->id())->where('status', 'paid')->where('payout_status', 'unpaid')->sum('owner_earning'),
            'paid_out'       => Transaction::where('owner_id', auth()->id())->where('payout_status', 'paid')->sum('owner_earning'),
        ];

        return view('owner.earnings', compact('transactions', 'stats'));
    }
}