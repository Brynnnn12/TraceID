<?php

namespace App\Enums;

enum PhotoStatus: string
{
    case Diberikan = 'diberikan';
    case Ditolak = 'ditolak';
    case Gagal = 'gagal';
}
