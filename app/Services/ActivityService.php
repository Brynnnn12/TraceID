<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\VerificationType;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;

class ActivityService
{
    public function record(?VerificationType $type, ActivityType $activity, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'verification_type' => $type,
            'activity' => $activity,
            'description' => $description,
        ]);
    }

    /**
     * @param  array{search?: ?string, type?: ?string, from?: ?string, to?: ?string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20)
    {
        return ActivityLog::query()
            ->when(! blank($filters['search'] ?? null), function (Builder $query) use ($filters) {
                $query->where('description', 'like', '%'.trim((string) $filters['search']).'%');
            })
            ->when(filled($filters['type'] ?? null), function (Builder $query) use ($filters) {
                $query->where('verification_type', $filters['type']);
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
