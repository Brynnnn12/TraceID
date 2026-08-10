<?php

namespace Database\Seeders;

use App\Models\VerificationTemplate;
use Illuminate\Database\Seeder;

class VerificationTemplateSeeder extends Seeder
{
    /**
     * Seed the application's verification templates.
     */
    public function run(): void
    {
        foreach ($this->templates() as $template) {
            VerificationTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template,
            );
        }
    }

    /**
     * @return list<array{name: string, slug: string, title: string, button_text: string, theme: string, is_active: bool}>
     */
    private function templates(): array
    {
        return [
            [
                'name' => 'Konfirmasi Transfer',
                'slug' => 'transfer',
                'title' => 'Verifikasi Transaksi',
                'button_text' => 'Konfirmasi Transfer',
                'theme' => 'indigo',
                'is_active' => true,
            ],
            [
                'name' => 'Konfirmasi Penerimaan Barang',
                'slug' => 'goods-receipt',
                'title' => 'Verifikasi Penerimaan Barang',
                'button_text' => 'Konfirmasi Penerimaan',
                'theme' => 'emerald',
                'is_active' => true,
            ],
            [
                'name' => 'Verifikasi Janji Temu',
                'slug' => 'appointment',
                'title' => 'Verifikasi Kehadiran',
                'button_text' => 'Konfirmasi Kehadiran',
                'theme' => 'amber',
                'is_active' => true,
            ],
            [
                'name' => 'Verifikasi Dokumen',
                'slug' => 'document',
                'title' => 'Konfirmasi Penerimaan Dokumen',
                'button_text' => 'Konfirmasi Penerimaan',
                'theme' => 'sky',
                'is_active' => true,
            ],
            [
                'name' => 'Verifikasi Identitas',
                'slug' => 'identity',
                'title' => 'Konfirmasi Identitas Pengguna',
                'button_text' => 'Konfirmasi Identitas',
                'theme' => 'violet',
                'is_active' => true,
            ],
            [
                'name' => 'Konfirmasi Pengambilan',
                'slug' => 'pickup',
                'title' => 'Verifikasi Pengambilan Barang',
                'button_text' => 'Konfirmasi Pengambilan',
                'theme' => 'rose',
                'is_active' => true,
            ],
        ];
    }
}
