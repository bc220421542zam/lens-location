<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Support\PaymentCompleter;
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

        // The payment window closes at the booking date. The persisted
        // `expired` status covers the settled case; hasStarted() covers the
        // gap between the booking date and the next settle pass.
        if ($booking->status === BookingStatus::Expired
            || ($booking->status === BookingStatus::Confirmed && $booking->hasStarted())) {
            return back()->with('error', 'This booking has expired - the payment window closed at the booking date.');
        }

        if ($booking->status !== BookingStatus::Confirmed) {
            return back()->with('error', 'Only confirmed bookings can be paid.');
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

        // A pending transaction may already carry a session from an earlier
        // click. If it's still open, resume it - recreating one under the same
        // idempotency key fails whenever the checkout parameters changed since
        // the first attempt (e.g. APP_URL moved after an ngrok restart).
        if ($transaction->stripe_checkout_session_id) {
            try {
                $existing = $this->stripe->retrieveSession($transaction->stripe_checkout_session_id);

                if (($existing->status ?? null) === 'open' && ($existing->payment_status ?? null) === 'unpaid') {
                    return redirect()->away($existing->url);
                }
            } catch (\Throwable $e) {
                // Unretrievable session - fall through and create a new one.
            }
        }

        $session = $this->startCheckout($booking, $transaction);

        if (! $session) {
            return back()->with('error', 'We could not start the payment. Please try again.');
        }

        $transaction->update(['stripe_checkout_session_id' => $session->id]);

        return redirect()->away($session->url);
    }

    /**
     * Landing page after Checkout.
     *
     * The webhook is the authoritative path, but it may lag behind this page
     * or never arrive (e.g. `stripe listen` isn't running locally), so the
     * session is retrieved from Stripe and completed here synchronously.
     * PaymentCompleter is idempotent - the webhook replay is a no-op - and
     * runs the same guards as the webhook (paid session, amount match, no
     * dispute).
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

        if ($transaction->status !== PaymentStatus::Paid) {
            try {
                $session = $this->stripe->retrieveSession($sessionId);
            } catch (ApiErrorException $e) {
                Log::warning('Could not retrieve checkout session on return page', [
                    'session'     => $sessionId,
                    'transaction' => $transaction->id,
                    'error'       => $e->getMessage(),
                ]);

                return redirect()->route('customer.bookings')
                    ->with('success', 'Payment received - we are confirming it with Stripe now.');
            }

            PaymentCompleter::forSession($session, auth()->id());
        }

        $message = $transaction->fresh()->status === PaymentStatus::Paid
            ? 'Payment successful. Your booking is confirmed.'
            : 'Payment received - we are confirming it with Stripe now.';

        return redirect()->route('customer.bookings')->with('success', $message);
    }

    /**
     * Create the checkout session, recovering from Stripe's idempotency guard.
     *
     * The stable `booking-txn` key means a second click with identical
     * parameters returns the original session - that's the double-click
     * protection. But it also means a second click with *changed* parameters
     * is rejected ("keys for idempotent requests..."), which happens in
     * practice when APP_URL moves after an ngrok restart and the success/cancel
     * URLs change. One retry under a fresh key gets the customer a working
     * session instead of an error page.
     *
     * Returns null when Stripe fails for any other reason.
     */
    private function startCheckout(Booking $booking, Transaction $transaction)
    {
        try {
            return $this->stripe->checkoutSession(
                $booking,
                $transaction,
                route('customer.payments.success'),
                route('customer.bookings'),
            );
        } catch (ApiErrorException $e) {
            if (! str_contains($e->getMessage(), 'idempotent requests can only be used')) {
                Log::error('Stripe checkout session failed', [
                    'booking_id' => $booking->id,
                    'message'    => $e->getMessage(),
                ]);

                return null;
            }

            Log::info('Stripe idempotency key collision; retrying with a fresh key', [
                'booking_id'  => $booking->id,
                'transaction' => $transaction->id,
            ]);
        }

        try {
            return $this->stripe->checkoutSession(
                $booking,
                $transaction,
                route('customer.payments.success'),
                route('customer.bookings'),
                "booking-{$booking->id}-txn-{$transaction->id}-".uniqid(),
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session failed', [
                'booking_id' => $booking->id,
                'message'    => $e->getMessage(),
            ]);

            return null;
        }
    }
}
