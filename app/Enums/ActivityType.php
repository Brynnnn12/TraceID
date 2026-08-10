<?php

namespace App\Enums;

enum ActivityType: string
{
    case LinkDibuat = 'link_dibuat';
    case LinkDibuka = 'link_dibuka';
    case LokasiDiberikan = 'lokasi_diberikan';
    case FotoDiberikan = 'foto_diberikan';
    case VerifikasiSelesai = 'verifikasi_selesai';

    public function label(): string
    {
        return match ($this) {
            self::LinkDibuat => 'Link dibuat',
            self::LinkDibuka => 'Link dibuka',
            self::LokasiDiberikan => 'Lokasi diberikan',
            self::FotoDiberikan => 'Foto diberikan',
            self::VerifikasiSelesai => 'Verifikasi selesai',
        };
    }
}
