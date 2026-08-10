<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kasus') }}
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('cases.edit', $case) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('cases.destroy', $case) }}"
                      onsubmit="return confirm('Hapus kasus ini?');">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>{{ __('Hapus') }}</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">No. Referensi</dt>
                            <dd class="mt-1 font-mono text-sm">{{ $case->reference_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $case->status->value }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Target</dt>
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
                            <dd class="mt-1 text-sm">Rp {{ number_format($case->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Token</dt>
                            <dd class="mt-1 font-mono text-sm">{{ $case->token }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Berlaku Hingga</dt>
                            <dd class="mt-1 text-sm">{{ $case->expires_at?->format('d M Y H:i') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                            <dd class="mt-1 text-sm">{{ $case->notes }}</dd>
                        </div>
                    </dl>

                    @if ($case->status !== \App\Enums\CaseStatus::Terverifikasi)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg"
                             x-data="{ link: '{{ route('verification.show', $case->token) }}', copied: false }">
                            <p class="text-sm font-medium text-gray-700">Link Verifikasi</p>
                            <div class="mt-2 flex items-center gap-2">
                                <input type="text" readonly :value="link"
                                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <button type="button"
                                        @click="navigator.clipboard.writeText(link).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <span x-show="!copied">{{ __('Salin') }}</span>
                                    <span x-show="copied" x-cloak>{{ __('Tersalin') }}</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if ($case->verifications->isNotEmpty())
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Riwayat Verifikasi</h3>

                            <div class="mt-4 space-y-4">
                                @foreach ($case->verifications as $verification)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-gray-700">Verifikasi #{{ $verification->id }}</p>
                                            <span class="text-xs text-gray-500">{{ $verification->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                            <span>{{ $verification->ip_address }}</span>
                                            <span>{{ $verification->browser }} · {{ $verification->operating_system }} · {{ $verification->device_type }}</span>
                                            @if ($verification->photo_status)
                                                <span>Foto: {{ $verification->photo_status->value }}</span>
                                            @endif
                                            @if ($verification->location_status)
                                                <span>Lokasi: {{ $verification->location_status->value }}</span>
                                            @endif
                                            @if ($verification->latitude && $verification->longitude)
                                                <span>{{ $verification->latitude }}, {{ $verification->longitude }}</span>
                                            @endif
                                        </div>
                                        @if ($verification->photo_path)
                                            <img
                                                src="{{ URL::signedRoute('verification.photo', ['verification' => $verification->id]) }}"
                                                alt="Foto verifikasi"
                                                class="mt-3 w-40 rounded-md border border-gray-200"
                                            >
                                        @endif

                                        @if ($verification->latitude && $verification->longitude)
                                            <div
                                                x-data="leafletMap({{ $verification->latitude }}, {{ $verification->longitude }})"
                                                class="z-0 mt-3 h-56 w-full rounded-md border border-gray-200"
                                            ></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
