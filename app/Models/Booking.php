<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;

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

    /**
     * When the booked shoot finishes. There is no end-date column - a booking
     * is a start time plus a duration in hours.
     */
    public function endsAt(): Carbon
    {
        return $this->booking_date->copy()->addHours($this->hours);
    }

    /**
     * True once the shoot is over. This - not payment - is what makes a booking
     * eligible to be completed and reviewed.
     */
    public function hasEnded(?Carbon $now = null): bool
    {
        return $this->endsAt()->lte($now ?? now());
    }

    /**
     * Bookings whose shoot has finished: booking_date + hours <= $now.
     *
     * Driver-specific because neither driver's interval syntax is portable and
     * the duration lives in a column, not a literal. Mirrors the MySQL/SQLite
     * branch in the 2026_08_24_100100 stripe-columns migration.
     */
    public function scopeServiceEnded(Builder $query, ?Carbon $now = null): Builder
    {
        $driver = $query->getConnection()->getDriverName();

        $endsAt = match ($driver) {
            'mysql'  => 'DATE_ADD(booking_date, INTERVAL hours HOUR)',
            'sqlite' => "datetime(booking_date, '+' || hours || ' hours')",
            default  => throw new RuntimeException(
                "scopeServiceEnded() has no end-time SQL for driver [{$driver}]."
            ),
        };

        return $query->whereRaw("{$endsAt} <= ?", [($now ?? now())->toDateTimeString()]);
    }
}
