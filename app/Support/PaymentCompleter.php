<?php

namespace App\Support;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use App\Models\Transaction;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * The one place a successful Stripe checkout session becomes a paid
 * transaction and a completed booking.
 *
 * Two callers share this:
 *
 *  - StripeWebhookController - `checkout.session.completed` /
 *    `async_payment_succeeded`, the authoritative path.
 *  - Customer\PaymentController::success - the checkout return page. The
 *    customer lands here the moment Stripe finishes the payment, possibly
 *    before the webhook arrives - or when the webhook never does (e.g.
 *    `stripe listen` isn't running locally). Retrieving the session from
 *    Stripe here and completing synchronously keeps the booking status
 *    honest in both cases.
 *
 * Idempotent: an already-paid transaction returns early, so the webhook
 * replaying after the return page (or vice versa) never double-notifies.
 *
 * The same guards run regardless of caller: the session must be paid, the
 * charged amount must match the recorded one, and a disputed transaction
 * never auto-completes its booking.
 */
class PaymentCompleter
{
    public static function forSession($session, ?int $customerId = null): ?Transaction
    {
        // With delayed-notification payment methods the completed event can
        // arrive while the session is still unpaid, so gate on payment_status
        // rather than treating the event itself as proof of payment.
        if (($session->payment_status ?? null) === 'unpaid') {
            Log::info('Checkout session completed but still unpaid', ['session' => $session->id]);

            return null;
        }

        return DB::transaction(function () use ($session, $customerId) {
            $transaction = Transaction::where('stripe_checkout_session_id', $session->id)
                ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
                ->lockForUpdate()
                ->first()
                ?? Transaction::whereKey(data_get($session->metadata, 'transaction_id'))
                    ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
                    ->lockForUpdate()
                    ->first();

            if (! $transaction) {
                Log::warning('No transaction for Stripe session', ['session' => $session->id]);

                return null;
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

                return null;
            }

            if ($transaction->status === PaymentStatus::Paid) {
                return $transaction;
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

            // Payment completes the booking immediately - the shoot itself is
            // the next milestone: BookingCompleter moves a completed booking
            // to `visited` once the booking date has arrived, and only then
            // releases the escrow. Guarded on Confirmed so a booking cancelled
            // between checkout and this completion isn't resurrected, and on
            // disputed_at so a disputed payment never auto-completes.
            $booking = $transaction->booking;

            if ($booking
                && $booking->status === BookingStatus::Confirmed
                && $transaction->disputed_at === null) {
                $booking->update(['status' => BookingStatus::Completed]);
            }

            return $transaction;
        });
    }
}
