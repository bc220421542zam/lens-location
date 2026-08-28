<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\StripeEvent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Single source of truth for payment state.
 *
 * Sits outside auth and role middleware because Stripe calls it directly, and
 * is CSRF-exempt in bootstrap/app.php. Both of those are only safe because
 * every request's signature is verified below before anything is read.
 */
class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            Log::error('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not set.');

            return response('Webhook secret not configured', 500);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature') ?? '',
                $secret,
            );
        } catch (SignatureVerificationException|UnexpectedValueException $e) {
            Log::warning('Rejected Stripe webhook', ['reason' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        // Claim the event id first. Stripe retries on any non-2xx and can also
        // deliver the same event twice, so a duplicate key here means we have
        // already done the work and must not repeat it.
        try {
            StripeEvent::create([
                'id'           => $event->id,
                'type'         => $event->type,
                'processed_at' => now(),
            ]);
        } catch (QueryException $e) {
            Log::info('Ignored replayed Stripe event', ['id' => $event->id]);

            return response('Already processed', 200);
        }

        try {
            match ($event->type) {
                'checkout.session.completed',
                'checkout.session.async_payment_succeeded' => $this->sessionSucceeded($event->data->object),
                'checkout.session.async_payment_failed',
                'checkout.session.expired'                 => $this->sessionFailed($event->data->object),
                'payment_intent.payment_failed'            => $this->paymentIntentFailed($event->data->object),
                'charge.succeeded'                         => $this->chargeSucceeded($event->data->object),
                'charge.refunded'                          => $this->chargeRefunded($event->data->object),
                default                                    => null,
            };
        } catch (\Throwable $e) {
            // Release the idempotency claim so Stripe's retry can succeed.
            StripeEvent::whereKey($event->id)->delete();

            Log::error('Stripe webhook handler failed', [
                'id'    => $event->id,
                'type'  => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response('Handler error', 500);
        }

        return response('OK', 200);
    }

    /**
     * With delayed-notification payment methods the completed event can arrive
     * while the session is still unpaid, so gate on payment_status rather than
     * treating the event itself as proof of payment.
     */
    private function sessionSucceeded($session): void
    {
        if (($session->payment_status ?? null) === 'unpaid') {
            Log::info('Checkout session completed but still unpaid', ['session' => $session->id]);

            return;
        }

        DB::transaction(function () use ($session) {
            $transaction = $this->lockTransactionForSession($session);

            if (! $transaction) {
                Log::warning('No transaction for Stripe session', ['session' => $session->id]);

                return;
            }

            // Never trust the amount from the event alone - confirm the customer
            // was charged what we recorded before granting the booking.
            $expected = (int) round((float) $transaction->amount * 100);

            if ((int) $session->amount_total !== $expected) {
                Log::error('Stripe amount mismatch; refusing to mark paid', [
                    'transaction' => $transaction->id,
                    'expected'    => $expected,
                    'received'    => $session->amount_total,
                ]);

                return;
            }

            if ($transaction->status === PaymentStatus::Paid) {
                return;
            }

            $transaction->update([
                'status'                   => PaymentStatus::Paid,
                'stripe_payment_intent_id' => $session->payment_intent,
                'gateway_ref'              => $session->payment_intent,
                'paid_at'                  => now(),
            ]);

            // Payment alone doesn't settle the booking - the shoot still has to
            // happen. Guarded on Confirmed so a booking cancelled between
            // checkout and this webhook isn't resurrected, and on hasEnded() so
            // a future shoot stays `confirmed`; BookingCompleter promotes it on
            // the first page load after its end time.
            $booking = $transaction->booking;

            if ($booking && $booking->status === BookingStatus::Confirmed && $booking->hasEnded()) {
                $booking->update(['status' => BookingStatus::Completed]);
            }
        });
    }

    private function sessionFailed($session): void
    {
        DB::transaction(function () use ($session) {
            $transaction = $this->lockTransactionForSession($session);

            if ($transaction && $transaction->status === PaymentStatus::Pending) {
                $transaction->update(['status' => PaymentStatus::Failed]);
            }
        });
    }

    private function paymentIntentFailed($intent): void
    {
        DB::transaction(function () use ($intent) {
            $transaction = Transaction::where('stripe_payment_intent_id', $intent->id)
                ->lockForUpdate()
                ->first();

            if ($transaction && $transaction->status === PaymentStatus::Pending) {
                $transaction->update(['status' => PaymentStatus::Failed]);
            }
        });
    }

    /**
     * A destination charge transfers the owner's share automatically, so the
     * transfer id on the charge is what proves the owner has been paid.
     */
    private function chargeSucceeded($charge): void
    {
        if (! ($charge->transfer ?? null)) {
            return;
        }

        DB::transaction(function () use ($charge) {
            $transaction = Transaction::where('stripe_payment_intent_id', $charge->payment_intent)
                ->lockForUpdate()
                ->first();

            $transaction?->update([
                'stripe_transfer_id' => $charge->transfer,
                'payout_status'      => PayoutStatus::Paid,
            ]);
        });
    }

    private function chargeRefunded($charge): void
    {
        DB::transaction(function () use ($charge) {
            $transaction = Transaction::where('stripe_payment_intent_id', $charge->payment_intent)
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return;
            }

            $transaction->update(['status' => PaymentStatus::Refunded]);
            $transaction->booking?->update(['status' => BookingStatus::Cancelled]);
        });
    }

    private function lockTransactionForSession($session): ?Transaction
    {
        return Transaction::where('stripe_checkout_session_id', $session->id)
            ->lockForUpdate()
            ->first()
            ?? Transaction::whereKey(data_get($session->metadata, 'transaction_id'))
                ->lockForUpdate()
                ->first();
    }
}
