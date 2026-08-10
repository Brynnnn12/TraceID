<?php

namespace Database\Factories;

use App\Enums\CaseStatus;
use App\Models\CaseFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CaseFile>
 */
class CaseFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_number' => 'TRC-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'target_name' => fake()->name(),
            'bank_name' => fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
            'account_number' => fake()->numerify('##########'),
            'amount' => fake()->numberBetween(10000, 10000000),
            'notes' => fake()->optional()->sentence(),
            'status' => CaseStatus::Aktif,
            'token' => Str::random(32),
            'expires_at' => now()->addHours(24),
        ];
    }
}
