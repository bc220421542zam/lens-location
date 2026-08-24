<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Failed   = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending  => 'bg-yellow-100 text-yellow-700',
            self::Paid     => 'bg-green-100 text-green-700',
            self::Failed   => 'bg-red-100 text-red-600',
            self::Refunded => 'bg-gray-100 text-gray-600',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Pending  => 'bg-yellow-600',
            self::Paid     => 'bg-green-600',
            self::Failed   => 'bg-red-600',
            self::Refunded => 'bg-gray-500',
        };
    }
}
