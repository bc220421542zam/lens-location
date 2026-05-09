<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'location_id',
        'photographer_id',
        'booking_date',
        'hours',
        'total_price',
        'shoot_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'datetime',
            'total_price'  => 'decimal:2',
            'status'       => BookingStatus::class,
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }
}
