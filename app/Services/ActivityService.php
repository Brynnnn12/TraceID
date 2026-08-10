<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\CaseFile;

class ActivityService
{
    public function record(CaseFile $case, ActivityType $type, ?string $description = null): ActivityLog
    {
        return $case->activities()->create([
            'activity' => $type,
            'description' => $description,
        ]);
    }
}
