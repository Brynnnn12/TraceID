<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Http\Requests\UpdateSocialMediaRequest;
use App\Models\SocialMedia;
use App\Services\ActivityService;

class SocialMediaController extends Controller
{
    public function __construct(private readonly ActivityService $activityService) {}

    public function edit()
    {
        return view('social-media.edit', [
            'socialMedia' => SocialMedia::first(),
        ]);
    }

    public function update(UpdateSocialMediaRequest $request)
    {
        $socialMedia = SocialMedia::query()->firstOrCreate([]);
        $socialMedia->update($request->validated());

        $this->activityService->record(null, ActivityType::KonfigurasiSocialMediaDiperbarui);

        return redirect()
            ->route('social-media.edit')
            ->with('status', 'Konfigurasi social media berhasil diperbarui.');
    }
}
