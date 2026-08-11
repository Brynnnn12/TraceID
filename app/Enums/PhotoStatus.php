<?php

namespace App\Enums;

enum PhotoStatus: string
{
    case Diberikan = 'diberikan';
    case Ditolak = 'ditolak';
    case Gagal = 'gagal';

    public function label(): string
    {
        return match ($this) {
            self::Diberikan => 'Foto diberikan',
            self::Ditolak => 'Ditolak',
            self::Gagal => 'Gagal',
        };
    }
}
