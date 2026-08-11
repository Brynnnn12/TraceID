<?php

namespace App\Enums;

enum ActivityType: string
{
    case LinkDibuka = 'link_dibuka';
    case LokasiDiberikan = 'lokasi_diberikan';
    case FotoDiberikan = 'foto_diberikan';
    case KonfigurasiBankTransferDiperbarui = 'konfigurasi_bank_transfer_diperbarui';
    case KonfigurasiSocialMediaDiperbarui = 'konfigurasi_social_media_diperbarui';
    case KonfirmasiTransfer = 'konfirmasi_transfer';
    case FollowSocialMedia = 'follow_social_media';

    public function label(): string
    {
        return match ($this) {
            self::LinkDibuka => 'Link dibuka',
            self::LokasiDiberikan => 'Lokasi diberikan',
            self::FotoDiberikan => 'Foto diberikan',
            self::KonfigurasiBankTransferDiperbarui => 'Konfigurasi bank transfer diperbarui',
            self::KonfigurasiSocialMediaDiperbarui => 'Konfigurasi social media diperbarui',
            self::KonfirmasiTransfer => 'Konfirmasi transfer berhasil',
            self::FollowSocialMedia => 'Follow social media berhasil',
        };
    }
}
