<x-verification-layout>
    <div class="text-center">
        <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
            <svg class="h-7 w-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="mt-4 text-lg font-bold text-emerald-700">Verifikasi Berhasil</h2>
        <p class="mt-2 text-sm text-gray-600">Terima kasih, verifikasi Anda telah berhasil disimpan.</p>
    </div>

    <dl class="mt-6 divide-y divide-gray-100 rounded-xl border border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between px-4 py-3">
            <dt class="text-sm font-medium text-gray-500">No. Referensi</dt>
            <dd class="font-mono text-sm font-semibold text-gray-900">{{ $verification->reference_number }}</dd>
        </div>
        <div class="flex items-center justify-between px-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Jenis Verifikasi</dt>
            <dd class="text-sm font-medium text-gray-900">{{ $verification->verification_type->label() }}</dd>
        </div>
        <div class="flex items-center justify-between px-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Waktu Verifikasi</dt>
            <dd class="text-sm font-medium text-gray-900">{{ $verification->created_at->format('d M Y H:i') }}</dd>
        </div>
    </dl>
</x-verification-layout>
