<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Support\StripeGateway;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct(private StripeGateway $stripe) {}

    public function index(Request $request): View
    {
        // Visiting clears the Payments sidebar dot until the next checkout.
        $request->user()->markSectionViewed('customer.payments');

        $request->validate([
            'search'     => 'nullable|string|max:255',
            'status'     => 'nullable|in:paid,pending,failed,refunded',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
        ]);

        $transactions = Transaction::where('customer_id', auth()->id())
            ->with('booking.location')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('gateway_ref', 'like', $term)
                    ->orWhere('stripe_payment_intent_id', 'like', $term)
                    ->orWhereHas('booking.location', fn ($q) => $q->where('title', 'like', $term));
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('min_amount'), fn ($q) => $q->where('amount', '>=', $request->float('min_amount')))
            ->when($request->filled('max_amount'), fn ($q) => $q->where('amount', '<=', $request->float('max_amount')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.payments', compact('transactions'));
    }

    /**
     * Start a hosted Checkout Session for a confirmed booking.
     *
     * POST, not GET: this creates a Stripe session and a database row, so it
     * must not be reachable by a prefetched link.
     */
    public function pay(Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        $booking->loadMissing('location.owner', 'customer');

        if ($booking->status !== BookingStatus::Confirmed) {
            return back()->with('error', 'Only confirmed bookings can be paid.');
        }

        if ($booking->hasEnded()) {
            return back()->with('error', 'This booking has expired - the payment window closed when the shoot ended.');
        }

        if ($booking->transactions()->where('status', PaymentStatus::Paid)->exists()) {
            return back()->with('error', 'This booking is already paid.');
        }

        $owner = $booking->location?->owner;

        if (! $owner?->canReceivePayouts()) {
            return back()->with(
                'error',
                'This owner has not finished setting up payouts yet, so payment is unavailable. Please try again later.',
            );
        }

        // Reuse an existing pending transaction rather than inserting a new row
        // on every click - the idempotency key is derived from its id.
        $transaction = DB::transaction(function () use ($booking, $owner) {
            $split = $this->stripe->split((float) $booking->total_price);

            return Transaction::firstOrCreate(
                [
                    'booking_id' => $booking->id,
                    'status'     => PaymentStatus::Pending,
                ],
                [
                    'customer_id'   => $booking->customer_id,
                    'owner_id'      => $owner->id,
                    'amount'        => $split['amount_minor'] / 100,
                    'platform_fee'  => $split['fee_minor'] / 100,
                    'owner_earning' => $split['owner_minor'] / 100,
                    'currency'      => strtoupper($this->stripe->currency()),
                ],
            );
        });

        try {
            $session = $this->stripe->checkoutSession(
                $booking,
                $transaction,
                $owner->stripe_account_id,
                route('customer.payments.success'),
                route('customer.bookings'),
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session failed', [
                'booking_id' => $booking->id,
                'message'    => $e->getMessage(),
            ]);

            return back()->with('error', 'We could not start the payment. Please try again.');
        }

        $transaction->update(['stripe_checkout_session_id' => $session->id]);

        return redirect()->away($session->url);
    }

    /**
     * Landing page after Checkout.
     *
     * Deliberately read-only: the customer may never reach this page, so the
     * webhook is the only thing that marks a transaction paid.
     */
    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->string('session_id')->toString();

        if ($sessionId === '') {
            return redirect()->route('customer.bookings');
        }

        $transaction = Transaction::where('stripe_checkout_session_id', $sessionId)
            ->where('customer_id', auth()->id())
            ->first();

        if (! $transaction) {
            return redirect()->route('customer.bookings');
        }

        $message = $transaction->status === PaymentStatus::Paid
            ? 'Payment successful. Your booking is confirmed.'
            : 'Payment received - we are confirming it with Stripe now.';

        return redirect()->route('customer.bookings')->with('success', $message);
    }
}
