<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * The timezone users enter and see booking times in.
     */
    public const BOOKING_DISPLAY_TIMEZONE = 'Asia/Karachi';

    protected function casts(): array
    {
        return [
            'booking_date' => 'datetime',
            'total_price'  => 'decimal:2',
            'status'       => BookingStatus::class,
        ];
    }

    /**
     * booking_date is stored in UTC; users enter and see it on Asia/Karachi
     * (UTC+5). Reads go through this accessor so views and notifications show
     * the wall clock the user picked - the `datetime` cast still handles
     * writes and serialization.
     */
    protected function bookingDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null
                ? null
                : ($value instanceof Carbon ? $value->copy() : Carbon::parse($value, 'UTC'))
                    ->timezone(self::BOOKING_DISPLAY_TIMEZONE),
        );
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

    /**
     * True once the booking date has arrived. This is the "visited" moment: the
     * slot is honored (or lost to a no-show) from the start time on, because
     * the owner can no longer rebook it - not at the end of the shoot.
     */
    public function hasStarted(?Carbon $now = null): bool
    {
        return $this->booking_date->lte($now ?? now());
    }

    /**
     * Bookings whose start time has passed: booking_date <= $now.
     */
    public function scopeStarted(Builder $query, ?Carbon $now = null): Builder
    {
        return $query->where('booking_date', '<=', $now ?? now());
    }

    /**
     * When the booked shoot finishes. There is no end-date column - a booking
     * is a start time plus a duration in hours.
     */
    public function endsAt(): Carbon
    {
        return $this->booking_date->copy()->addHours($this->hours);
    }

    /**
     * True once the shoot is over.
     */
    public function hasEnded(?Carbon $now = null): bool
    {
        return $this->endsAt()->lte($now ?? now());
    }
}
