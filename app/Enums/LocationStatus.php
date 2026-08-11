<?php

namespace App\Enums;

enum LocationStatus: string
{
    case Diberikan = 'diberikan';
    case Ditolak = 'ditolak';
    case Gagal = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::Diberikan => 'Lokasi diberikan',
            self::Ditolak => 'Ditolak',
            self::Gagal => 'Gagal',
        };
    }
}
