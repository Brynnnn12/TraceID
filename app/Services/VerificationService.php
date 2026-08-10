<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\CaseStatus;
use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Exceptions\VerificationLinkException;
use App\Models\CaseFile;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationService
{
    public function __construct(
        private readonly PhotoService $photoService,
        private readonly ActivityService $activityService,
    ) {}

    public function resolveToken(string $token): CaseFile
    {
        $case = CaseFile::where('token', $token)->first();

        if ($case === null) {
            throw new VerificationLinkException('Link verifikasi tidak valid.');
        }

        if ($case->status === CaseStatus::Ditutup) {
            throw new VerificationLinkException('Link ini sudah tidak aktif.');
        }

        if ($case->isExpired()) {
            throw new VerificationLinkException('Link verifikasi sudah kedaluwarsa. Hubungi pengirim untuk link baru.');
        }

        if ($case->status === CaseStatus::Aktif) {
            $case->update(['status' => CaseStatus::LinkDibuka]);
            $this->activityService->record($case, ActivityType::LinkDibuka);
        }

        return $case;
    }

    public function verificationUrl(CaseFile $case): string
    {
        return route('verification.show', $case->token);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function recordVerification(CaseFile $case, array $validated, Request $request): Verification
    {
        return DB::transaction(function () use ($case, $validated, $request): Verification {
            $photos = $request->file('photo');
            $photos = is_array($photos) ? $photos : [$photos];

            $photo = $this->photoService->store(array_filter($photos));

            $photoStatus = $photo['photo_status'] ?? null;

            if ($photoStatus === null && ($validated['photo_status'] ?? null) === PhotoStatus::Ditolak->value) {
                $photoStatus = PhotoStatus::Ditolak;
            }

            $verification = $case->verifications()->create([
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

            $case->update(['status' => CaseStatus::Terverifikasi]);

            $this->recordActivities($case, $verification);

            return $verification;
        });
    }

    private function recordActivities(CaseFile $case, Verification $verification): void
    {
        $this->activityService->record($case, ActivityType::VerifikasiSelesai);

        if ($verification->photo_status === PhotoStatus::Diberikan) {
            $this->activityService->record($case, ActivityType::FotoDiberikan);
        }

        if ($verification->location_status === LocationStatus::Diberikan) {
            $this->activityService->record($case, ActivityType::LokasiDiberikan);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function locationStatus(array $validated): ?LocationStatus
    {
        $explicit = LocationStatus::tryFrom($validated['location_status'] ?? '');

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
