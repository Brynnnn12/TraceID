<?php

namespace App\Models;

use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Enums\VerificationType;
use Database\Factories\VerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    public const UPDATED_AT = null;

    /** @use HasFactory<VerificationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'verification_type',
        'reference_number',
        'photo_paths',
        'latitude',
        'longitude',
        'accuracy',
        'ip_address',
        'browser',
        'operating_system',
        'device_type',
        'language',
        'timezone',
        'screen_resolution',
        'user_agent',
        'photo_status',
        'location_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_type' => VerificationType::class,
            'photo_paths' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy' => 'decimal:2',
            'photo_status' => PhotoStatus::class,
            'location_status' => LocationStatus::class,
        ];
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
