<?php

namespace Database\Factories;

use App\Models\CaseFile;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Verification>
 */
class VerificationFactory extends Factory
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
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'ip_address' => fake()->ipv4(),
            'browser' => 'Chrome',
            'operating_system' => 'Windows',
            'device_type' => 'desktop',
            'language' => 'id',
            'user_agent' => fake()->userAgent(),
        ];
    }
}
