<?php

namespace App\Enums;

enum VerificationType: string
{
    case BankTransfer = 'bank_transfer';
    case SocialMedia = 'social_media';

    public function label(): string
    {
        return match ($this) {
            self::BankTransfer => 'Bank Transfer',
            self::SocialMedia => 'Social Media',
        };
    }
}
