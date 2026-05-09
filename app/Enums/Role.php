<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Owner = 'owner';
    case Photographer = 'photographer';

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin        => 'admin.dashboard',
            self::Owner        => 'owner.listings',
            self::Photographer => 'photographer.dashboard',
        };
    }
}
