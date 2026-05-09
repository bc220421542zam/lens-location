<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active  = 'active';
    case Blocked = 'blocked';

    public function toggle(): self
    {
        return $this === self::Active ? self::Blocked : self::Active;
    }
}
