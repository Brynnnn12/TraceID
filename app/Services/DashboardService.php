<?php

namespace App\Services;

use App\Enums\CaseStatus;
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
     * @return array{labels: list<string>, data: list<int>}
     */
    public function verificationsLast7Days(): array
    {
        $start = today()->subDays(6);

        $counts = Verification::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $data = [];

        for ($day = 0; $day < 7; $day++) {
            $date = $start->copy()->addDays($day);
            $labels[] = $date->format('d M');
            $data[] = (int) $counts->get($date->toDateString(), 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array{labels: list<string>, data: list<int>}
     */
    public function casesByStatus(): array
    {
        $labels = [];
        $data = [];

        foreach (CaseStatus::cases() as $status) {
            $labels[] = $status->label();
            $data[] = CaseFile::where('status', $status)->count();
        }

        return ['labels' => $labels, 'data' => $data];
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
