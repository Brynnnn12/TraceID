<?php

namespace Database\Factories;

use App\Enums\ConfigStatus;
use App\Models\SocialMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialMedia>
 */
class SocialMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => null,
            'username' => null,
            'profile_url' => null,
            'caption' => null,
            'status' => ConfigStatus::Aktif,
        ];
    }

    public function configured(): static
    {
        return $this->state(fn (): array => [
            'platform' => 'Instagram',
            'username' => fake()->userName(),
            'profile_url' => fake()->url(),
            'caption' => fake()->sentence(),
        ]);
    }

    public function ditutup(): static
    {
        return $this->state(fn (): array => ['status' => ConfigStatus::Ditutup]);
    }
}
