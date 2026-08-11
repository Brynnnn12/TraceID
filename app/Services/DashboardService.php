<?php

namespace App\Services;

use App\Enums\ConfigStatus;
use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Enums\VerificationType;
use App\Models\ActivityLog;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    /**
     * @return array{total_verifications: int, bank_transfer_verifications: int, social_media_verifications: int, verifications_today: int, locations_recorded: int, photos_recorded: int, active_configurations: int}
     */
    public function statistics(): array
    {
        return [
            'total_verifications' => Verification::count(),
            'bank_transfer_verifications' => Verification::where('verification_type', VerificationType::BankTransfer)->count(),
            'social_media_verifications' => Verification::where('verification_type', VerificationType::SocialMedia)->count(),
            'verifications_today' => Verification::whereDate('created_at', today())->count(),
            'locations_recorded' => Verification::where('location_status', LocationStatus::Diberikan)->count(),
            'photos_recorded' => Verification::where('photo_status', PhotoStatus::Diberikan)->count(),
            'active_configurations' => BankTransfer::query()->where('status', ConfigStatus::Aktif)->count()
                + SocialMedia::query()->where('status', ConfigStatus::Aktif)->count(),
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
    public function verificationsByType(): array
    {
        return [
            'labels' => [VerificationType::BankTransfer->label(), VerificationType::SocialMedia->label()],
            'data' => [
                Verification::where('verification_type', VerificationType::BankTransfer)->count(),
                Verification::where('verification_type', VerificationType::SocialMedia)->count(),
            ],
        ];
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    public function recentActivities(int $limit = 10): Collection
    {
        return ActivityLog::query()
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
