<?php

namespace App\Models;

use App\Enums\CaseStatus;
use Database\Factories\CaseFileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'template_id',
        'fields',
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
            'fields' => 'array',
            'status' => CaseStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(VerificationTemplate::class, 'template_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'case_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'case_id');
    }

    public function fieldValue(string $key): ?string
    {
        $value = $this->fields[$key] ?? null;

        return $value === '' ? null : $value;
    }

    public function formattedField(string $key): ?string
    {
        $value = $this->fieldValue($key);

        if ($value === null) {
            return null;
        }

        $field = collect($this->template?->fields() ?? [])->firstWhere('key', $key);

        if (($field['format'] ?? null) === 'currency') {
            return 'Rp '.number_format((float) $value, 0, ',', '.');
        }

        return $value;
    }

    public function summary(): string
    {
        $parts = [];

        foreach ($this->template?->fields() ?? [] as $field) {
            $value = $this->formattedField($field['key']);

            if ($value !== null) {
                $parts[] = $field['label'].': '.$value;
            }
        }

        return implode(' · ', $parts);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->status !== CaseStatus::Terverifikasi
            && $this->expires_at->isPast();
    }
}
