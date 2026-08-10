<?php

namespace App\Services;

use App\Enums\PhotoStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class PhotoService
{
    /**
     * Validate and store an uploaded photo to the private disk.
     *
     * @return array{photo_path: string|null, photo_status: PhotoStatus|null}
     */
    public function store(?UploadedFile $photo): array
    {
        if ($photo === null) {
            return ['photo_path' => null, 'photo_status' => null];
        }

        $validator = Validator::make(['photo' => $photo], [
            'photo' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return ['photo_path' => null, 'photo_status' => PhotoStatus::Gagal];
        }

        return [
            'photo_path' => $photo->store('verifications', 'private'),
            'photo_status' => PhotoStatus::Diberikan,
        ];
    }
}
