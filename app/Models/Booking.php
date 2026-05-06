<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'location_id', 'photographer_id',
        'booking_date', 'hours',
        'total_price', 'shoot_type', 'status'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }
}