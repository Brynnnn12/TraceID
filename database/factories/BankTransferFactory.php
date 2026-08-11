<?php

namespace Database\Factories;

use App\Enums\ConfigStatus;
use App\Models\BankTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankTransfer>
 */
class BankTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_name' => null,
            'account_number' => null,
            'amount' => null,
            'notes' => null,
            'status' => ConfigStatus::Aktif,
        ];
    }

    public function configured(): static
    {
        return $this->state(fn (): array => [
            'bank_name' => 'Bank Contoh',
            'account_number' => fake()->numerify('##########'),
            'amount' => fake()->numberBetween(50000, 5000000),
            'notes' => fake()->sentence(),
        ]);
    }

    public function ditutup(): static
    {
        return $this->state(fn (): array => ['status' => ConfigStatus::Ditutup]);
    }
}
