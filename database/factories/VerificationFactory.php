<?php

namespace Database\Factories;

use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Enums\VerificationType;
use App\Models\Verification;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Verification>
 */
class VerificationFactory extends Factory
{
    protected static int $counter = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'verification_type' => fake()->randomElement(VerificationType::cases()),
            'reference_number' => 'TRV-'.now()->format('Ymd').'-'.str_pad((string) ++self::$counter, 4, '0', STR_PAD_LEFT),
            'photo_paths' => null,
            'latitude' => null,
            'longitude' => null,
            'accuracy' => null,
            'ip_address' => fake()->ipv4(),
            'browser' => 'Chrome',
            'operating_system' => 'Windows',
            'device_type' => 'desktop',
            'language' => 'id',
            'timezone' => 'Asia/Jakarta',
            'screen_resolution' => '1920x1080',
            'user_agent' => fake()->userAgent(),
            'photo_status' => null,
            'location_status' => null,
            'created_at' => now(),
        ];
    }

    public function asBankTransfer(): static
    {
        return $this->state(fn (): array => ['verification_type' => VerificationType::BankTransfer]);
    }

    public function asSocialMedia(): static
    {
        return $this->state(fn (): array => ['verification_type' => VerificationType::SocialMedia]);
    }

    public function withPhoto(): static
    {
        return $this->state(fn (): array => [
            'photo_paths' => [fake()->filePath()],
            'photo_status' => PhotoStatus::Diberikan,
        ]);
    }

    public function withLocation(): static
    {
        return $this->state(fn (): array => [
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'accuracy' => fake()->randomFloat(2, 5, 50),
            'location_status' => LocationStatus::Diberikan,
        ]);
    }

    public function at(DateTimeInterface $date): static
    {
        return $this->state(fn (): array => ['created_at' => $date]);
    }
}
