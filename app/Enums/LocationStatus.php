<?php

namespace App\Enums;

enum LocationStatus: string
{
    case Diberikan = 'diberikan';
    case Ditolak = 'ditolak';
    case Gagal = 'gagal';
}
