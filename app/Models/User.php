<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Accounts v2 capability status meaning the owner can receive transfers.
     * The deprecated v1 charges_enabled/payouts_enabled flags are not used.
     */
    public const TRANSFERS_ACTIVE = 'active';

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
        'block_reason',
        'blocked_at',
        'google_id',
        'facebook_id',
        'stripe_account_id',
        'stripe_transfers_status',
        'stripe_onboarded_at',
        'notif_new_booking',
        'notif_new_user',
        'notif_new_listing',
        'notif_dispute',
        'notif_review',
        'notif_push',
        'notif_email',
        'notif_sms',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'blocked_at'         => 'datetime',
            'password'           => 'hashed',
            'role'               => Role::class,
            'status'             => UserStatus::class,
            'notif_new_booking'  => 'boolean',
            'notif_new_user'     => 'boolean',
            'notif_new_listing'  => 'boolean',
            'notif_dispute'      => 'boolean',
            'notif_review'       => 'boolean',
            'notif_push'         => 'boolean',
            'notif_email'        => 'boolean',
            'notif_sms'          => 'boolean',
            'stripe_onboarded_at' => 'datetime',
        ];
    }

    public function isBlocked(): bool
    {
        return $this->status === UserStatus::Blocked;
    }

    /**
     * Human-readable block timestamp — day, date, and time — for the admin
     * UI wherever a user's blocked status is shown.
     */
    public function blockedAtDisplay(): ?string
    {
        return $this->blocked_at?->format('l, d M Y \a\t g:i A');
    }

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
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

    /**
     * Whether Stripe will accept a transfer to this owner's connected account.
     * Gates the customer-facing Pay button.
     */
    public function canReceivePayouts(): bool
    {
        return $this->stripe_account_id !== null
            && $this->stripe_transfers_status === self::TRANSFERS_ACTIVE;
    }

    public function hasStartedStripeOnboarding(): bool
    {
        return $this->stripe_account_id !== null;
    }

    public function transactionsAsOwner(): HasMany
    {
        return $this->hasMany(Transaction::class, 'owner_id');
    }

    public function sectionViews(): HasMany
    {
        return $this->hasMany(SectionView::class);
    }

    /**
     * When this user last opened the given section page, if ever. Sections use
     * route-like keys ('admin.ledger', 'owner.earnings', ...). Prefer the
     * eager-loaded relation (see the sidebar components) to avoid per-tab
     * queries.
     */
    public function sectionViewedAt(string $section): ?\Carbon\Carbon
    {
        return $this->sectionViews->firstWhere('section', $section)?->viewed_at;
    }

    /**
     * Stamp "just opened this section". Called at the top of the matching page
     * controller so the sidebar rendered in the same request already shows no
     * dot.
     */
    public function markSectionViewed(string $section): void
    {
        $this->sectionViews()->updateOrCreate(
            ['section' => $section],
            ['viewed_at' => now()],
        );
    }
}
