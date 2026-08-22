<?php

declare(strict_types=1);

namespace App\Enums;

enum SiteStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Checking = 'checking';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online',
            self::Offline => 'Offline',
            self::Checking => 'Checking',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Online => 'bg-green-500/10 text-green-400 border border-green-500/20',
            self::Offline => 'bg-red-500/10 text-red-400 border border-red-500/20',
            self::Checking => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
        };
    }

    public function dotClass(): string
    {
        return match ($this) {
            self::Online => 'bg-green-400 animate-pulse',
            self::Offline => 'bg-red-400',
            self::Checking => 'bg-yellow-400 animate-pulse',
        };
    }

    public function isOnline(): bool
    {
        return $this === self::Online;
    }

    public function isChecking(): bool
    {
        return $this === self::Checking;
    }

    public function isOffline(): bool
    {
        return $this === self::Offline;
    }
}
