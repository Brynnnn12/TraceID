<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\CaseFile;
use Illuminate\Database\Eloquent\Builder;

class ActivityService
{
    public function record(CaseFile $case, ActivityType $type, ?string $description = null): ActivityLog
    {
        return $case->activities()->create([
            'activity' => $type,
            'description' => $description,
        ]);
    }

    /**
     * @param  array{search?: ?string, from?: ?string, to?: ?string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20)
    {
        return ActivityLog::query()
            ->with('case')
            ->when(! blank($filters['search'] ?? null), function (Builder $query) use ($filters) {
                $search = trim((string) $filters['search']);

                $query->where(function (Builder $sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhereHas('case', function (Builder $case) use ($search) {
                            $case->where('reference_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when(! blank($filters['from'] ?? null), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters['from']);
            })
            ->when(! blank($filters['to'] ?? null), function (Builder $query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters['to']);
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
