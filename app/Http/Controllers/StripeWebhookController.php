<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\StripeEvent;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AdminDisputeNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentSuccessNotification;
use App\Support\PayoutEligibility;
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
                'charge.refunded'                          => $this->chargeRefunded($event->data->object),
                'charge.dispute.created'                   => $this->chargeDisputed($event->data->object),
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
                'payout_status'            => PayoutStatus::Held,
                'held_since'               => now(),
            ]);

            // A mailer outage must not roll the payment back - Stripe would
            // retry the event forever while the mail stays down. Log and move
            // on; the database notification row is part of this transaction
            // and rolls back atomically with the payment itself.
            try {
                $transaction->customer?->notify(new PaymentSuccessNotification($transaction));
            } catch (\Throwable $e) {
                Log::warning('Failed to notify customer of payment', [
                    'transaction' => $transaction->id,
                    'error'       => $e->getMessage(),
                ]);
            }

            try {
                $transaction->owner?->notify(new PaymentReceivedNotification($transaction));
            } catch (\Throwable $e) {
                Log::warning('Failed to notify owner of payment', [
                    'transaction' => $transaction->id,
                    'error'       => $e->getMessage(),
                ]);
            }

            // Payment alone doesn't settle the booking - the booking date still
            // has to arrive. Guarded on Confirmed so a booking cancelled
            // between checkout and this webhook isn't resurrected, on
            // hasStarted() so a future booking stays `confirmed`, and on
            // disputed_at so a disputed payment never auto-completes;
            // BookingCompleter promotes it on the next settle pass.
            $booking = $transaction->booking;

            if ($booking
                && $booking->status === BookingStatus::Confirmed
                && $transaction->disputed_at === null
                && $booking->hasStarted()) {
                $booking->update(['status' => BookingStatus::Completed]);
                PayoutEligibility::markEligibleForBooking($booking);
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

    /**
     * A dispute is not a refund yet: the charge stays `paid`, but the money
     * freezes in escrow - `disputed_at` excludes the transaction from booking
     * completion and from the payout batch until an admin resolves it, and
     * flags it for review on the ledger.
     */
    private function chargeDisputed($dispute): void
    {
        DB::transaction(function () use ($dispute) {
            $transaction = Transaction::where('stripe_payment_intent_id', $dispute->payment_intent)
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return;
            }

            $transaction->update(['disputed_at' => now()]);

            User::where('role', 'admin')->each(
                fn (User $admin) => $admin->notify(new AdminDisputeNotification($transaction->id, $transaction->booking_id))
            );
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
