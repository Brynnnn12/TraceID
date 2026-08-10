<?php

namespace App\Models;

use App\Enums\CaseStatus;
use Database\Factories\CaseFileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseFile extends Model
{
    /** @use HasFactory<CaseFileFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'cases';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'reference_number',
        'target_name',
        'bank_name',
        'account_number',
        'amount',
        'notes',
        'status',
        'token',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => CaseStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'case_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'case_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->status !== CaseStatus::Terverifikasi
            && $this->expires_at->isPast();
    }
}
