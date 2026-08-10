<?php

namespace Database\Factories;

use App\Models\VerificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerificationTemplate>
 */
class VerificationTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Konfirmasi Transfer',
            'slug' => 'transfer',
            'title' => 'Verifikasi Transaksi',
            'button_text' => 'Konfirmasi Transfer',
            'theme' => 'indigo',
            'is_active' => true,
        ];
    }
}
