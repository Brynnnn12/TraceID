<?php

namespace App\Services;

use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Models\ActivityLog;
use App\Models\CaseFile;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    /**
     * @return array{total_cases: int, total_verifications: int, verifications_today: int, locations_recorded: int, photos_recorded: int}
     */
    public function statistics(): array
    {
        return [
            'total_cases' => CaseFile::count(),
            'total_verifications' => Verification::count(),
            'verifications_today' => Verification::whereDate('created_at', today())->count(),
            'locations_recorded' => Verification::where('location_status', LocationStatus::Diberikan)->count(),
            'photos_recorded' => Verification::where('photo_status', PhotoStatus::Diberikan)->count(),
        ];
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    public function recentActivities(int $limit = 10): Collection
    {
        return ActivityLog::query()
            ->with('case')
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
