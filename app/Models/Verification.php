<?php

namespace App\Models;

use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use Database\Factories\VerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    public const UPDATED_AT = null;

    /** @use HasFactory<VerificationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'case_id',
        'photo_path',
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
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy' => 'decimal:2',
            'photo_status' => PhotoStatus::class,
            'location_status' => LocationStatus::class,
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }
}
