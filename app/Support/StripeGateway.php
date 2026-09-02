<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use Stripe\StripeClient;

/**
 * Every piece of Stripe knowledge in the app lives here so controllers never
 * touch config or the SDK directly.
 *
 * Charge model: escrow. The customer pays the platform and 100% of the money
 * sits on the platform balance. When a booking completes, its transaction
 * becomes eligible, and the `payouts:process` command issues standalone
 * Stripe Transfers to owners' connected accounts (90% owner / 10% commission).
 */
class StripeGateway
{
    public function __construct(private StripeClient $stripe) {}

    public function currency(): string
    {
        return strtolower(config('services.stripe.currency'));
    }

    public function commissionRate(): float
    {
        return (float) config('services.stripe.commission_rate');
    }

    /**
     * Split a gross amount into integer minor units.
     *
     * Done in integers so amount, fee and owner share always reconcile exactly
     * - deriving the owner's share by float multiplication drifts by a cent.
     *
     * @return array{amount_minor:int, fee_minor:int, owner_minor:int}
     */
    public function split(float $amount): array
    {
        $amountMinor = (int) round($amount * 100);
        $feeMinor    = (int) round($amountMinor * $this->commissionRate());

        return [
            'amount_minor' => $amountMinor,
            'fee_minor'    => $feeMinor,
            'owner_minor'  => $amountMinor - $feeMinor,
        ];
    }

    // ---------------------------------------------------------------- Connect

    /**
     * Accounts v2 options. The v2 core endpoints are preview-only, so these
     * calls carry an explicit version while v1 calls use the SDK default.
     */
    private function v2Opts(): array
    {
        return ['stripe_version' => config('services.stripe.preview_version')];
    }

    /**
     * Create a recipient-configuration connected account for an owner.
     *
     * Marketplace defaults per Stripe's guidance: express dashboard, platform
     * collects fees and owns negative-balance liability. We deliberately do
     * NOT request the merchant configuration - a recipient only needs
     * stripe_balance.stripe_transfers, and asking for more lengthens onboarding.
     */
    public function createConnectedAccount(User $owner): string
    {
        $account = $this->stripe->v2->core->accounts->create([
            'contact_email' => $owner->email,
            'display_name'  => $owner->business_name ?: $owner->name,
            'dashboard'     => 'express',
            'identity'      => [
                'country' => strtolower(config('services.stripe.connect_country')),
            ],
            'defaults' => [
                'responsibilities' => [
                    'fees_collector'   => 'application',
                    'losses_collector' => 'application',
                ],
            ],
            'configuration' => [
                'recipient' => [
                    'capabilities' => [
                        'stripe_balance' => [
                            'stripe_transfers' => ['requested' => true],
                        ],
                    ],
                ],
            ],
            'metadata' => ['user_id' => (string) $owner->id],
            'include'  => ['configuration.recipient', 'identity', 'requirements'],
        ], $this->v2Opts());

        return $account->id;
    }

    /**
     * Single-use Stripe-hosted onboarding URL.
     */
    public function onboardingUrl(string $accountId, string $refreshUrl, string $returnUrl): string
    {
        $link = $this->stripe->v2->core->accountLinks->create([
            'account'  => $accountId,
            'use_case' => [
                'type'               => 'account_onboarding',
                'account_onboarding' => [
                    'configurations' => ['recipient'],
                    'refresh_url'    => $refreshUrl,
                    'return_url'     => $returnUrl,
                    // Collect eventually_due up front so owners aren't
                    // interrupted by a second round of requirements later.
                    'collection_options' => ['fields' => 'eventually_due'],
                ],
            ],
        ], $this->v2Opts());

        return $link->url;
    }

    /**
     * Express dashboard login link, so owners can see their own payouts.
     */
    public function dashboardUrl(string $accountId): string
    {
        return $this->stripe->accounts->createLoginLink($accountId)->url;
    }

    /**
     * Read the recipient transfer capability straight from Stripe and persist
     * it on the user.
     *
     * The v1 `charges_enabled` / `payouts_enabled` booleans are deprecated for
     * v2 accounts - the authoritative field is the capability status.
     */
    public function syncAccountStatus(User $owner): User
    {
        if (! $owner->stripe_account_id) {
            return $owner;
        }

        $account = $this->stripe->v2->core->accounts->retrieve(
            $owner->stripe_account_id,
            ['include' => ['configuration.recipient', 'requirements']],
            $this->v2Opts(),
        );

        $status = data_get(
            $account->toArray(),
            'configuration.recipient.capabilities.stripe_balance.stripe_transfers.status',
        );

        $owner->forceFill([
            'stripe_transfers_status' => $status,
            'stripe_onboarded_at'     => $status === User::TRANSFERS_ACTIVE
                ? ($owner->stripe_onboarded_at ?? now())
                : $owner->stripe_onboarded_at,
        ])->save();

        return $owner;
    }

    // --------------------------------------------------------------- Checkout

    /**
     * Hosted Checkout Session for a booking, as an escrow charge.
     *
     * No `application_fee_amount` and no `transfer_data.destination`: the full
     * amount lands on the platform balance and stays there until the batch
     * payout issues a standalone transfer.
     *
     * `payment_method_types` is deliberately omitted so Stripe picks the
     * highest-converting methods for each customer (dynamic payment methods).
     */
    public function checkoutSession(
        Booking $booking,
        Transaction $transaction,
        string $successUrl,
        string $cancelUrl,
    ) {
        $split = $this->split((float) $transaction->amount);

        $metadata = [
            'booking_id'     => (string) $booking->id,
            'transaction_id' => (string) $transaction->id,
            'owner_id'       => (string) $transaction->owner_id,
        ];

        return $this->stripe->checkout->sessions->create([
            'mode'                   => 'payment',
            'client_reference_id'    => (string) $booking->id,
            'customer_email'         => $booking->customer->email,
            'integration_identifier' => 'lenslocation-booking-vqmtxhbe',
            'line_items'             => [[
                'quantity'   => 1,
                'price_data' => [
                    'currency'     => $this->currency(),
                    'unit_amount'  => $split['amount_minor'],
                    'product_data' => [
                        'name'        => $booking->location->title,
                        'description' => $booking->hours.' hour'.($booking->hours > 1 ? 's' : '')
                                         .' - '.$booking->booking_date->format('M d, Y g:i A'),
                    ],
                ],
            ]],
            'payment_intent_data' => [
                'metadata' => $metadata,
            ],
            'metadata'    => $metadata,
            'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
        ], ['idempotency_key' => "booking-{$booking->id}-txn-{$transaction->id}"]);
    }

    public function retrieveSession(string $sessionId)
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }

    public function retrieveCharge(string $chargeId)
    {
        return $this->stripe->charges->retrieve($chargeId);
    }

    /**
     * Refund an escrow charge. No application fee was collected at charge time,
     * so there is nothing to claw back there; `reverse_transfer` handles the
     * case where a batch transfer already moved the owner's share.
     */
    public function refund(string $paymentIntentId)
    {
        return $this->stripe->refunds->create([
            'payment_intent'   => $paymentIntentId,
            'reverse_transfer' => true,
        ]);
    }

    /**
     * Standalone transfer from the platform balance to an owner's connected
     * account. The idempotency key is stable per transaction, which is what
     * makes command retries and re-runs safe from double payment - a retry
     * returns the existing transfer instead of moving money twice.
     */
    public function transfer(Transaction $transaction, User $owner): \Stripe\Transfer
    {
        return $this->stripe->transfers->create([
            'amount'      => (int) round((float) $transaction->owner_payout_amount * 100),
            'currency'    => $this->currency(),
            'destination' => $owner->stripe_account_id,
            'description' => 'Booking payout',
            'metadata'    => [
                'transaction_id' => (string) $transaction->id,
                'booking_id'     => (string) $transaction->booking_id,
                'owner_id'       => (string) $owner->id,
            ],
        ], ['idempotency_key' => "transfer-txn-{$transaction->id}"]);
    }
}
