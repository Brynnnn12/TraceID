<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\ConfigStatus;
use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Enums\VerificationType;
use App\Models\ActivityLog;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerificationService
{
    public function __construct(
        private readonly PhotoService $photoService,
        private readonly ActivityService $activityService,
    ) {}

    public function isSectionActive(VerificationType $type): bool
    {
        return $this->hasActiveConfig(
            $type === VerificationType::BankTransfer ? BankTransfer::query() : SocialMedia::query(),
        );
    }

    public function recordLinkOpened(Request $request): void
    {
        $ip = $request->ip();

        $exists = ActivityLog::query()
            ->where('activity', ActivityType::LinkDibuka)
            ->where('description', $ip)
            ->exists();

        if (! $exists) {
            $this->activityService->record(null, ActivityType::LinkDibuka, $ip);
        }
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'TRV-'.now()->format('Ymd').'-';

        $last = Verification::query()
            ->where('reference_number', 'like', $prefix.'%')
            ->orderByDesc('reference_number')
            ->value('reference_number');

        $sequence = $last !== null ? ((int) Str::afterLast($last, '-')) + 1 : 1;

        return $prefix.Str::padLeft((string) $sequence, 4, '0');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function recordVerification(VerificationType $type, array $validated, Request $request): Verification
    {
        return DB::transaction(function () use ($type, $validated, $request): Verification {
            $photo = $this->photoService->store($this->photosFrom($request));

            $photoStatus = $photo['photo_status'] ?? null;

            if ($photoStatus === null && ($validated['photo_status'] ?? null) === PhotoStatus::Ditolak->value) {
                $photoStatus = PhotoStatus::Ditolak;
            }

            $verification = Verification::create([
                'verification_type' => $type,
                'reference_number' => $this->generateReferenceNumber(),
                'photo_paths' => $photo['photo_paths'] ?? null,
                'photo_status' => $photoStatus,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'accuracy' => $validated['accuracy'] ?? null,
                'location_status' => $this->locationStatus($validated),
                'timezone' => $validated['timezone'] ?? null,
                'screen_resolution' => $validated['screen_resolution'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                ...$this->captureDeviceMetadata($request),
            ]);

            $this->recordActivities($type, $verification);

            return $verification;
        });
    }

    private function hasActiveConfig(Builder $query): bool
    {
        return $query->where('status', ConfigStatus::Aktif)->exists();
    }

    private function recordActivities(VerificationType $type, Verification $verification): void
    {
        $this->activityService->record(
            $type,
            $type === VerificationType::BankTransfer ? ActivityType::KonfirmasiTransfer : ActivityType::FollowSocialMedia,
            $verification->reference_number,
        );

        if ($verification->photo_status === PhotoStatus::Diberikan) {
            $this->activityService->record($type, ActivityType::FotoDiberikan, $verification->reference_number);
        }

        if ($verification->location_status === LocationStatus::Diberikan) {
            $this->activityService->record($type, ActivityType::LokasiDiberikan, $verification->reference_number);
        }
    }

    /**
     * @return list<UploadedFile>
     */
    private function photosFrom(Request $request): array
    {
        $photos = $request->file('photo');

        if ($photos === null) {
            return [];
        }

        return is_array($photos) ? $photos : [$photos];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function locationStatus(array $validated): ?LocationStatus
    {
        $explicit = isset($validated['location_status'])
            ? LocationStatus::tryFrom((string) $validated['location_status'])
            : null;

        if ($explicit !== null) {
            return $explicit;
        }

        if (($validated['latitude'] ?? null) !== null && ($validated['longitude'] ?? null) !== null) {
            return LocationStatus::Diberikan;
        }

        return null;
    }

    /**
     * @return array<string, string|null>
     */
    private function captureDeviceMetadata(Request $request): array
    {
        $userAgent = $request->userAgent() ?? '';

        return [
            'browser' => $this->browserFromUserAgent($userAgent),
            'operating_system' => $this->operatingSystemFromUserAgent($userAgent),
            'device_type' => $this->deviceTypeFromUserAgent($userAgent),
            'language' => $request->getLanguages()[0] ?? null,
        ];
    }

    private function browserFromUserAgent(string $userAgent): string
    {
        $browsers = [
            'Edg/' => 'Edge',
            'OPR/' => 'Opera',
            'Firefox/' => 'Firefox',
            'Chrome/' => 'Chrome',
            'Safari/' => 'Safari',
        ];

        foreach ($browsers as $fragment => $name) {
            if (str_contains($userAgent, $fragment)) {
                return $name;
            }
        }

        return 'Lainnya';
    }

    private function operatingSystemFromUserAgent(string $userAgent): string
    {
        $operatingSystems = [
            'Windows NT 10.0' => 'Windows 10/11',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.1' => 'Windows 7',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iPadOS',
            'Mac OS X' => 'macOS',
            'Linux' => 'Linux',
        ];

        foreach ($operatingSystems as $fragment => $name) {
            if (str_contains($userAgent, $fragment)) {
                return $name;
            }
        }

        return 'Lainnya';
    }

    private function deviceTypeFromUserAgent(string $userAgent): string
    {
        if (str_contains($userAgent, 'iPad')
            || (str_contains($userAgent, 'Android') && str_contains($userAgent, 'Tablet'))) {
            return 'tablet';
        }

        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'iPhone')) {
            return 'mobile';
        }

        return 'desktop';
    }
}
