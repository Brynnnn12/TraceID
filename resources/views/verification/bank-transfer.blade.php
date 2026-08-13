<x-verification-layout>
    <div class="text-center">
        <h2 class="text-xl font-bold text-gray-900">Verifikasi Bank Transfer</h2>
        <p class="mt-1 text-sm text-gray-500">Periksa detail di bawah ini, lalu tekan tombol Konfirmasi.</p>
    </div>

    @if ($bankTransfer !== null && $bankTransfer->isComplete())
        <div class="mt-6">
            <x-bank-transfer-card :config="$bankTransfer" />
        </div>

        <x-verification-form
            type="{{ \App\Enums\VerificationType::BankTransfer->value }}"
            label="Konfirmasi Pembayaran"
        />

        <a href="{{ route('verification.show') }}"
           class="mt-4 inline-flex w-full items-center justify-center gap-1 text-center text-xs font-medium text-gray-400 transition hover:text-gray-600">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
            </svg>
            Pilih jenis verifikasi lain
        </a>
    @else
        <div class="mt-6 rounded-2xl border border-gray-200 bg-gray-50 p-6 text-center">
            <p class="text-sm text-gray-500">Informasi belum tersedia. Hubungi pengirim.</p>
        </div>
    @endif
</x-verification-layout>
