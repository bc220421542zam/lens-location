<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per Stripe event id we've already processed. See the
 * create_stripe_events_table migration for why.
 */
class StripeEvent extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'type', 'processed_at'];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }
}
