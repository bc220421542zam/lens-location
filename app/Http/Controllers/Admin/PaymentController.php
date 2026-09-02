<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        // Visiting clears the Payments sidebar dot until the next payment.
        $request->user()->markSectionViewed('admin.payments');

        $request->validate([
            'search'     => 'nullable|string|max:255',
            'status'     => 'nullable|in:paid,pending,failed,refunded',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'sort'       => 'nullable|in:gateway_ref,amount,status',
            'direction'  => 'nullable|in:asc,desc',
        ]);

        $transactions = Transaction::with(['booking.location', 'customer', 'owner'])
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('gateway_ref', 'like', $term)
                    ->orWhere('stripe_payment_intent_id', 'like', $term)
                    ->orWhereHas('booking.location', fn ($q) => $q->where('title', 'like', $term))
                    ->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term));
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('min_amount'), fn ($q) => $q->where('amount', '>=', $request->float('min_amount')))
            ->when($request->filled('max_amount'), fn ($q) => $q->where('amount', '<=', $request->float('max_amount')));

        // The header sort links drive ordering; without a sort param the table
        // keeps its newest-first default.
        if ($request->filled('sort')) {
            $transactions->orderBy($request->string('sort'), $request->string('direction', 'asc')->toString());
        } else {
            $transactions->latest();
        }

        $transactions = $transactions->paginate(10)->withQueryString();

        $paid = Transaction::where('status', PaymentStatus::Paid);

        $stats = [
            'total_revenue' => (clone $paid)->sum('amount'),

            // Read straight off platform_fee now that it's stored per row,
            // rather than deriving it as (amount - owner_earning).
            'total_commission' => (clone $paid)->sum('platform_fee'),

            // Destination charges transfer the owner's share automatically, so
            // "pending payouts" is only ever a transient in-flight amount.
            'owner_payouts' => (clone $paid)->sum('owner_earning'),

            'refunded' => Transaction::where('status', PaymentStatus::Refunded)->sum('amount'),
            'failed'   => Transaction::where('status', PaymentStatus::Failed)->count(),
        ];

        return view('admin.payments', compact('transactions', 'stats'));
    }
}
