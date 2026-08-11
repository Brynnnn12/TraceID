<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Enums\VerificationType;
use App\Models\ActivityLog;
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
            'verification_type' => fake()->randomElement(VerificationType::cases()),
            'activity' => fake()->randomElement(ActivityType::cases()),
            'description' => null,
            'created_at' => now(),
        ];
    }
}
