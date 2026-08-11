<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Enums\VerificationType;
use Database\Factories\ActivityLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    /** @use HasFactory<ActivityLogFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'verification_type',
        'activity',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verification_type' => VerificationType::class,
            'activity' => ActivityType::class,
        ];
    }
}
