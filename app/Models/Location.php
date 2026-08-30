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
        'category_id',
        'price_per_hour',
        'image',
        'images',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
            'latitude'       => 'decimal:7',
            'longitude'      => 'decimal:7',
            'status'         => ListingStatus::class,
            'images'         => 'array',
        ];
    }

    /**
     * Every image of the listing, cover first. Falls back to the legacy
     * single `image` column for listings created before galleries existed.
     *
     * @return array<int, string>
     */
    public function gallery(): array
    {
        return array_values(array_filter($this->images ?: [$this->image]));
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
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
