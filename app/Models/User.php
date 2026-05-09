<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'profile_picture',
        'notif_new_booking',
        'notif_new_user',
        'notif_new_listing',
        'notif_dispute',
        'notif_review',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'notif_new_booking'  => 'boolean',
            'notif_new_user'     => 'boolean',
            'notif_new_listing'  => 'boolean',
            'notif_dispute'      => 'boolean',
            'notif_review'       => 'boolean',
        ];
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}