<?php

namespace App\Enums;

enum PayoutStatus: string
{
    /** Funds are sitting on the platform balance - the booking hasn't been visited. */
    case Held = 'held';

    /** Booking visited; the transfer to the owner failed or the owner cannot receive payouts yet - the batch retries. */
    case Eligible = 'eligible';

    /** A Stripe transfer to the owner's connected account has been issued. */
    case PaidOut = 'paid_out';

    public function label(): string
    {
        return match ($this) {
            self::Held     => 'On platform',
            self::Eligible => 'Pending',
            self::PaidOut  => 'Transferred',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Held     => 'bg-gray-100 text-gray-600',
            self::Eligible => 'bg-yellow-100 text-yellow-700',
            self::PaidOut  => 'bg-green-100 text-green-700',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Held     => 'bg-gray-500',
            self::Eligible => 'bg-yellow-600',
            self::PaidOut  => 'bg-green-600',
        };
    }
}
