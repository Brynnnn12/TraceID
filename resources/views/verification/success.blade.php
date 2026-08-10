<x-guest-layout>
    <div class="text-center">
        <h2 class="text-lg font-semibold text-green-600">Verifikasi Berhasil</h2>
        <p class="mt-2 text-sm text-gray-600">Terima kasih, verifikasi transaksi Anda telah berhasil disimpan.</p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4">
        <div>
            <dt class="text-sm font-medium text-gray-500">No. Referensi Kasus</dt>
            <dd class="mt-1 font-mono text-sm">{{ $case->reference_number }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Waktu Verifikasi</dt>
            <dd class="mt-1 text-sm">{{ $verification->created_at->format('d M Y H:i') }}</dd>
        </div>
    </dl>
</x-guest-layout>
