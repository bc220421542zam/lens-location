<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    /**
     * Ledger of owner payouts. Destination charges transfer the owner's share
     * automatically, so `unpaid` here means the charge.succeeded webhook has
     * not landed yet - the "Mark transferred" action exists to reconcile rows
     * whose webhook was missed.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'search'        => 'nullable|string|max:255',
            'payout_status' => 'nullable|in:unpaid,paid',
        ]);

        $transactions = Transaction::with('owner')
            ->where('status', PaymentStatus::Paid)
            ->when($request->filled('payout_status'), fn ($q) => $q->where('payout_status', $request->string('payout_status')))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('stripe_transfer_id', 'like', $term)
                    ->orWhereHas('owner', fn ($q) => $q
                        ->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term));
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $paid = Transaction::where('status', PaymentStatus::Paid);

        $stats = [
            'total_owner_share' => (clone $paid)->sum('owner_earning'),
            'in_transit'        => (clone $paid)->where('payout_status', PayoutStatus::Unpaid)->sum('owner_earning'),
            'transferred'       => (clone $paid)->where('payout_status', PayoutStatus::Paid)->sum('owner_earning'),
        ];

        return view('admin.payouts', compact('transactions', 'stats'));
    }

    /**
     * Reconcile a paid transaction whose transfer was never recorded (e.g. the
     * webhook didn't arrive). Bookkeeping only - Stripe already moved the money.
     */
    public function mark(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless(
            $transaction->status === PaymentStatus::Paid
            && $transaction->payout_status === PayoutStatus::Unpaid,
            422
        );

        $data = $request->validate([
            'reference' => 'nullable|string|max:255',
        ]);

        $transaction->update([
            'payout_status'      => PayoutStatus::Paid,
            'stripe_transfer_id' => ($data['reference'] ?? null) ?: 'manual',
            'paid_at'            => $transaction->paid_at ?? now(),
        ]);

        return back()->with('success', 'Payout marked as transferred.');
    }
}
