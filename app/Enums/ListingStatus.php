<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function toggleApproval(): self
    {
        return $this === self::Approved ? self::Pending : self::Approved;
    }
}
