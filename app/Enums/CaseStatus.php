<?php

namespace App\Enums;

enum CaseStatus: string
{
    case Aktif = 'aktif';
    case LinkDibuka = 'link_dibuka';
    case Terverifikasi = 'terverifikasi';
    case Ditutup = 'ditutup';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::LinkDibuka => 'Link dibuka',
            self::Terverifikasi => 'Terverifikasi',
            self::Ditutup => 'Ditutup',
        };
    }
}
