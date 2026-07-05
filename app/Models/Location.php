<?php

namespace App\Models;

use App\Enums\ListingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'city',
        'category',
        'price_per_hour',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
            'status'         => ListingStatus::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category');
    }
}
