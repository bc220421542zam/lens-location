<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'location_id',
        'customer_id',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * The owner is reached through the location - bookings carry no owner_id.
     */
    public function owner(): ?User
    {
        return $this->location?->owner;
    }
}
