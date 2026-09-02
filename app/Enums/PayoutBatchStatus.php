<?php

namespace App\Enums;

enum PayoutBatchStatus: string
{
    /** Batch created, transfers not all issued yet. */
    case Pending = 'pending';

    /** All transfers in the batch were issued successfully. */
    case Processed = 'processed';

    /** The batch run failed partway; transactions stay eligible for retry. */
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Processing',
            self::Processed => 'Processed',
            self::Failed    => 'Failed',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending   => 'bg-yellow-100 text-yellow-700',
            self::Processed => 'bg-green-100 text-green-700',
            self::Failed    => 'bg-red-100 text-red-600',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Pending   => 'bg-yellow-600',
            self::Processed => 'bg-green-600',
            self::Failed    => 'bg-red-600',
        };
    }
}
