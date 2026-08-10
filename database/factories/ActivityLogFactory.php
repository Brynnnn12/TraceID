<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\CaseFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_id' => CaseFile::factory(),
            'activity' => ActivityType::LinkDibuat,
            'description' => null,
        ];
    }
}
