<?php

namespace App\Services;

use App\Enums\PhotoStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class PhotoService
{
    public const MAX_PHOTOS = 3;

    /**
     * Validate and store up to three uploaded photos to the private disk.
     *
     * @param  list<UploadedFile>  $photos
     * @return array{photo_paths: list<string>|null, photo_status: PhotoStatus|null}
     */
    public function store(array $photos): array
    {
        $photos = array_slice(array_values($photos), 0, self::MAX_PHOTOS);

        if ($photos === []) {
            return ['photo_paths' => null, 'photo_status' => null];
        }

        $paths = [];

        foreach ($photos as $photo) {
            $validator = Validator::make(['photo' => $photo], [
                'photo' => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
            ]);

            if ($validator->fails()) {
                continue;
            }

            $paths[] = $photo->store('verifications', 'private');
        }

        return [
            'photo_paths' => $paths === [] ? null : $paths,
            'photo_status' => $paths === [] ? PhotoStatus::Gagal : PhotoStatus::Diberikan,
        ];
    }
}
