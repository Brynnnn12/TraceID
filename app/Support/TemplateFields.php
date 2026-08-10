<?php

namespace App\Support;

class TemplateFields
{
    /**
     * Field definitions per template slug.
     *
     * @return array<string, list<array{key: string, label: string, type: string, format?: string}>>
     */
    public static function all(): array
    {
        return [
            'transfer' => [
                ['key' => 'target_name', 'label' => 'Nama Penerima', 'type' => 'text'],
                ['key' => 'bank_name', 'label' => 'Nama Bank', 'type' => 'text'],
                ['key' => 'account_number', 'label' => 'Nomor Rekening', 'type' => 'text'],
                ['key' => 'amount', 'label' => 'Jumlah Transfer', 'type' => 'number', 'format' => 'currency'],
            ],
            'goods-receipt' => [
                ['key' => 'resi_number', 'label' => 'Nomor Resi', 'type' => 'text'],
                ['key' => 'recipient_name', 'label' => 'Nama Penerima', 'type' => 'text'],
                ['key' => 'address', 'label' => 'Alamat', 'type' => 'textarea'],
            ],
            'appointment' => [
                ['key' => 'attendee_name', 'label' => 'Nama Peserta', 'type' => 'text'],
                ['key' => 'schedule_date', 'label' => 'Jadwal Janji Temu', 'type' => 'date'],
                ['key' => 'location', 'label' => 'Lokasi', 'type' => 'text'],
            ],
            'document' => [
                ['key' => 'document_name', 'label' => 'Nama Dokumen', 'type' => 'text'],
                ['key' => 'document_number', 'label' => 'Nomor Dokumen', 'type' => 'text'],
                ['key' => 'receiver_name', 'label' => 'Nama Penerima', 'type' => 'text'],
            ],
            'identity' => [
                ['key' => 'full_name', 'label' => 'Nama Lengkap', 'type' => 'text'],
                ['key' => 'reference_number', 'label' => 'Nomor Referensi', 'type' => 'text'],
                ['key' => 'purpose', 'label' => 'Tujuan Verifikasi', 'type' => 'textarea'],
            ],
            'pickup' => [
                ['key' => 'item_description', 'label' => 'Deskripsi Barang', 'type' => 'text'],
                ['key' => 'pickup_code', 'label' => 'Kode Pengambilan', 'type' => 'text'],
                ['key' => 'pickup_name', 'label' => 'Nama Pengambil', 'type' => 'text'],
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, type: string, format?: string}>
     */
    public static function for(string $slug): array
    {
        return self::all()[$slug] ?? [];
    }
}
