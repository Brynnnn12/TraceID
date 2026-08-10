<?php

namespace App\Enums;

enum CaseStatus: string
{
    case Aktif = 'aktif';
    case LinkDibuka = 'link_dibuka';
    case Terverifikasi = 'terverifikasi';
    case Ditutup = 'ditutup';
}
