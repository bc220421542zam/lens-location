<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search'     => 'nullable|string|max:255',
            'status'     => 'nullable|in:paid,pending,failed',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
        ]);

        $transactions = Transaction::with(['booking.location', 'customer', 'owner'])
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('jazzcash_txn_ref', 'like', $term)
                    ->orWhereHas('booking.location', fn ($q) => $q->where('title', 'like', $term))
                    ->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term));
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('min_amount'), fn ($q) => $q->where('amount', '>=', $request->float('min_amount')))
            ->when($request->filled('max_amount'), fn ($q) => $q->where('amount', '<=', $request->float('max_amount')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_revenue'    => Transaction::where('status', 'paid')->sum('amount'),
            'total_commission' => Transaction::where('status', 'paid')->selectRaw('SUM(amount - owner_earning) as c')->value('c') ?? 0,
            'pending_payouts'  => Transaction::where('status', 'paid')->where('payout_status', 'unpaid')->sum('owner_earning'),
            'failed'           => Transaction::where('status', 'failed')->count(),
        ];

        return view('admin.payments', compact('transactions', 'stats'));
    }
}