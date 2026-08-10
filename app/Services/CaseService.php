<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\CaseStatus;
use App\Models\CaseFile;
use Illuminate\Support\Str;

class CaseService
{
    public function __construct(private readonly ActivityService $activityService) {}

    public function generateReferenceNumber(): string
    {
        $prefix = 'TRC-'.now()->format('Ymd').'-';

        $last = CaseFile::query()
            ->where('reference_number', 'like', $prefix.'%')
            ->orderByDesc('reference_number')
            ->value('reference_number');

        $sequence = $last !== null ? ((int) Str::afterLast($last, '-')) + 1 : 1;

        return $prefix.Str::padLeft((string) $sequence, 4, '0');
    }

    public function generateToken(): string
    {
        do {
            $token = Str::random(32);
        } while (CaseFile::where('token', $token)->exists());

        return $token;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): CaseFile
    {
        $case = CaseFile::create([
            ...$data,
            'reference_number' => $this->generateReferenceNumber(),
            'token' => $this->generateToken(),
            'status' => CaseStatus::Aktif,
            'expires_at' => now()->addHours(24),
        ]);

        $this->activityService->record($case, ActivityType::LinkDibuat);

        return $case;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(CaseFile $case, array $data): CaseFile
    {
        $case->update($data);

        return $case;
    }

    public function delete(CaseFile $case): void
    {
        $case->delete();
    }
}
