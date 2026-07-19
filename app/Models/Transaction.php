<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'owner_id',
        'amount',
        'owner_earning',
        'jazzcash_txn_ref',
        'status',
        'payout_status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}