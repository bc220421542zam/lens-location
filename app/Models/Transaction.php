<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'owner_id',
        'amount',
        'owner_earning',
        'platform_fee',
        'currency',
        'gateway_ref',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'stripe_transfer_id',
        'status',
        'payout_status',
        'paid_at',
        'platform_commission',
        'owner_payout_amount',
        'held_since',
        'transferred_at',
        'disputed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'               => 'decimal:2',
            'owner_earning'        => 'decimal:2',
            'platform_fee'         => 'decimal:2',
            'platform_commission'  => 'decimal:2',
            'owner_payout_amount'  => 'decimal:2',
            'status'               => PaymentStatus::class,
            'payout_status'        => PayoutStatus::class,
            'paid_at'              => 'datetime',
            'held_since'           => 'datetime',
            'transferred_at'       => 'datetime',
            'disputed_at'          => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Payout batches this transaction was paid out in. At most one, since a
     * paid-out transaction is never selected for a second batch.
     */
    public function payouts(): BelongsToMany
    {
        return $this->belongsToMany(Payout::class, 'payout_transaction')->withTimestamps();
    }

    /**
     * The payout batch that actually paid this transaction out, if any.
     */
    public function latestPayout(): ?Payout
    {
        return $this->payouts()->orderByDesc('payouts.processed_at')->first();
    }

    /**
     * The reference shown to users. Prefers the PaymentIntent id, which is what
     * they'll find in a Stripe receipt or the Dashboard.
     */
    public function reference(): string
    {
        return $this->stripe_payment_intent_id ?: ($this->gateway_ref ?: '-');
    }
}
