<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired   = 'expired';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending   => 'bg-yellow-100 text-yellow-700',
            self::Confirmed => 'bg-green-100 text-green-700',
            self::Completed => 'bg-indigo-100 text-indigo-700',
            self::Cancelled => 'bg-red-100 text-red-600',
            self::Expired   => 'bg-gray-100 text-gray-600',
        };
    }
}
