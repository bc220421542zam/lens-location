<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status',
    ];

    // Relationship — location belongs to a user (owner)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}