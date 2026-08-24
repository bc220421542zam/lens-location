<?php

namespace App\Enums;

enum PayoutStatus: string
{
    /** No transfer to the owner's connected account has landed yet. */
    case Unpaid = 'unpaid';

    /** Stripe transferred the owner's share to their connected account. */
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'In transit',
            self::Paid   => 'Transferred',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-yellow-100 text-yellow-700',
            self::Paid   => 'bg-green-100 text-green-700',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-yellow-600',
            self::Paid   => 'bg-green-600',
        };
    }
}
