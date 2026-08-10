<x-guest-layout>
    <div class="text-center">
        <h2 class="text-lg font-semibold text-green-600">Transaksi Sudah Diverifikasi</h2>
        <p class="mt-2 text-sm text-gray-600">Transaksi dengan nomor referensi berikut telah berhasil diverifikasi.</p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4">
        <div>
            <dt class="text-sm font-medium text-gray-500">Nama Penerima</dt>
            <dd class="mt-1 text-sm">{{ $case->target_name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Bank</dt>
            <dd class="mt-1 text-sm">{{ $case->bank_name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Nomor Rekening</dt>
            <dd class="mt-1 text-sm">{{ $case->account_number }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Jumlah Transfer</dt>
            <dd class="mt-1 text-sm font-semibold">Rp {{ number_format($case->amount, 0, ',', '.') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">No. Referensi</dt>
            <dd class="mt-1 font-mono text-sm">{{ $case->reference_number }}</dd>
        </div>
    </dl>
</x-guest-layout>
