<?php

namespace App\Http\Controllers\Owner;

use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningController extends Controller
{
    public function index(Request $request): View
    {
        // Visiting clears the Earnings sidebar dot until the next new payment.
        $request->user()->markSectionViewed('owner.earnings');

        $request->validate([
            'search'        => 'nullable|string|max:255',
            'status'        => 'nullable|in:paid,pending,failed,refunded',
            'payout_status' => 'nullable|in:held,eligible,paid_out',
            'min_amount'    => 'nullable|numeric|min:0',
            'max_amount'    => 'nullable|numeric|min:0|gte:min_amount',
        ]);

        $ownerId = auth()->id();

        $transactions = Transaction::where('owner_id', $ownerId)
            ->with('booking.location')
            ->when($request->filled('search'), fn ($q) => $q->whereHas('booking.location', fn ($q) => $q->where('title', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payout_status'), fn ($q) => $q->where('payout_status', $request->string('payout_status')))
            ->when($request->filled('min_amount'), fn ($q) => $q->where('amount', '>=', $request->float('min_amount')))
            ->when($request->filled('max_amount'), fn ($q) => $q->where('amount', '<=', $request->float('max_amount')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $paid = Transaction::where('owner_id', $ownerId)->where('status', PaymentStatus::Paid);

        $stats = [
            'total_earned' => (clone $paid)->sum('owner_payout_amount'),

            // Escrow: money sits on the platform until the booking completes
            // and the weekly/monthly batch transfers it out.
            'in_escrow' => (clone $paid)->where('payout_status', PayoutStatus::Held)->sum('owner_payout_amount'),

            'eligible' => (clone $paid)->where('payout_status', PayoutStatus::Eligible)->sum('owner_payout_amount'),

            'transferred' => (clone $paid)->where('payout_status', PayoutStatus::PaidOut)->sum('owner_payout_amount'),
        ];

        return view('owner.earnings', compact('transactions', 'stats'));
    }
}
