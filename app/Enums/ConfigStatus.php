<?php

namespace App\Enums;

enum ConfigStatus: string
{
    case Aktif = 'aktif';
    case Ditutup = 'ditutup';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Ditutup => 'Ditutup',
        };
    }
}
