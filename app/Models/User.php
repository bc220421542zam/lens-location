<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'business_name',
        'country',
        'address',
        'gender',
        'email',
        'phone',
        'password',
        'profile_picture',
        'status',
        'google_id',
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
            'role'               => Role::class,
            'status'             => UserStatus::class,
            'notif_new_booking'  => 'boolean',
            'notif_new_user'     => 'boolean',
            'notif_new_listing'  => 'boolean',
            'notif_dispute'      => 'boolean',
            'notif_review'       => 'boolean',
        ];
    }

    public function isBlocked(): bool
    {
        return $this->status === UserStatus::Blocked;
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function favorites(): BelongsToMany
{
    return $this->belongsToMany(Location::class, 'favorites')->withTimestamps();
}

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

}
