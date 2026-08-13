<x-verification-layout>
    <div class="text-center">
        <div class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h2 class="mt-4 text-xl font-bold text-gray-900">Pilih Jenis Verifikasi</h2>
        <p class="mt-1 text-sm text-gray-500">Pilih salah satu jenis verifikasi untuk melanjutkan.</p>
    </div>

    <div class="mt-6 space-y-4">
        @if ($bankTransfer !== null && $bankTransfer->status === \App\Enums\ConfigStatus::Aktif)
            <a href="{{ route('verification.bank-transfer') }}"
               class="group block overflow-hidden rounded-2xl border-2 border-indigo-600 bg-gradient-to-br from-indigo-50 to-indigo-100/50 p-5 transition hover:border-indigo-700 hover:shadow-lg hover:shadow-indigo-100">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold uppercase tracking-wider text-indigo-700">Bank Transfer</p>
                    <svg class="h-5 w-5 text-indigo-600 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                @if ($bankTransfer->isComplete())
                    <dl class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-500">Bank</dt>
                            <dd class="font-semibold text-gray-900">{{ $bankTransfer->bank_name }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-500">Rekening</dt>
                            <dd class="font-mono font-medium text-gray-900">{{ $bankTransfer->account_number }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-500">Jumlah</dt>
                            <dd class="font-semibold text-gray-900">{{ $bankTransfer->formattedAmount() }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="mt-2 text-sm text-gray-500">Informasi belum tersedia. Hubungi pengirim.</p>
                @endif
                <span class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white uppercase tracking-widest transition group-hover:bg-indigo-700">
                    Verifikasi Bank Transfer
                </span>
            </a>
        @endif

        @if ($socialMedia !== null && $socialMedia->status === \App\Enums\ConfigStatus::Aktif)
            <a href="{{ route('verification.social-media') }}"
               class="group block overflow-hidden rounded-2xl border-2 border-blue-600 bg-gradient-to-br from-blue-50 to-blue-100/50 p-5 transition hover:border-blue-700 hover:shadow-lg hover:shadow-blue-100">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold uppercase tracking-wider text-blue-700">Social Media</p>
                    <svg class="h-5 w-5 text-blue-600 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                @if ($socialMedia->isComplete())
                    <dl class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-500">Platform</dt>
                            <dd class="font-semibold text-gray-900">{{ $socialMedia->platform }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-500">Username</dt>
                            <dd class="font-medium text-gray-900">{{ $socialMedia->username }}</dd>
                        </div>
                        @if (filled($socialMedia->caption))
                            <p class="truncate text-xs text-gray-500">{{ $socialMedia->caption }}</p>
                        @endif
                    </dl>
                @else
                    <p class="mt-2 text-sm text-gray-500">Informasi belum tersedia. Hubungi pengirim.</p>
                @endif
                <span class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white uppercase tracking-widest transition group-hover:bg-blue-700">
                    Verifikasi Social Media
                </span>
            </a>
        @endif
    </div>
</x-verification-layout>
