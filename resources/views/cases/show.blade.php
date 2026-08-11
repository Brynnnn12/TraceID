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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Grid 2 kolom: Detail di kiri, Hasil di kanan --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                {{-- Kolom Kiri: Detail Kasus (3 kolom) --}}
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
                        <div class="p-6 text-gray-900">
                            {{-- Header Card --}}
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Informasi Kasus
                                </h3>
                                <span class="text-xs text-gray-400">ID: #{{ $case->id }}</span>
                            </div>

                            {{-- Detail Informasi --}}
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Reference Number --}}
                                <div class="col-span-1">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">No. Referensi</dt>
                                    <dd class="mt-1.5 font-mono text-sm font-semibold text-gray-900 bg-gray-50 px-3 py-1.5 rounded-md inline-block">
                                        {{ $case->reference_number }}
                                    </dd>
                                </div>

                                {{-- Status --}}
                                <div class="col-span-1">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</dt>
                                    <dd class="mt-1.5">
                                        <x-status-badge :status="$case->status->value" class="text-sm">
                                            {{ $case->status->label() }}
                                        </x-status-badge>
                                    </dd>
                                </div>

                                {{-- Template --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Template</dt>
                                    <dd class="mt-1.5 text-sm font-medium text-gray-900">{{ $case->template->name }}</dd>
                                </div>

                                @foreach ($case->template->fields() as $field)
                                    @if ($case->fieldValue($field['key']) !== null)
                                        <div>
                                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $field['label'] }}</dt>
                                            <dd class="mt-1.5 text-sm font-medium text-gray-900 {{ $field['key'] === 'amount' ? 'text-green-600 font-bold' : '' }} {{ $field['key'] === 'account_number' ? 'font-mono' : '' }}">
                                                {{ $case->formattedField($field['key']) }}
                                            </dd>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- Token --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Token</dt>
                                    <dd class="mt-1.5 font-mono text-sm bg-gray-100 px-3 py-1.5 rounded-md inline-block">
                                        {{ $case->token }}
                                    </dd>
                                </div>

                                {{-- Expires At --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Berlaku Hingga</dt>
                                    <dd class="mt-1.5 text-sm font-medium text-gray-900">
                                        {{ $case->expires_at?->format('d M Y H:i') }}
                                        @if($case->expires_at && $case->expires_at->isPast())
                                            <span class="ml-2 text-xs text-red-600 font-semibold">(Kadaluarsa)</span>
                                        @endif
                                    </dd>
                                </div>

                                {{-- Notes --}}
                                <div class="md:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</dt>
                                    <dd class="mt-1.5 text-sm text-gray-700 bg-gray-50 p-3 rounded-md">
                                        {{ $case->notes ?? '-' }}
                                    </dd>
                                </div>
                            </dl>

                            {{-- Verification Link --}}
                            @if ($case->status === \App\Enums\CaseStatus::Aktif || $case->status === \App\Enums\CaseStatus::LinkDibuka)
                                <div class="mt-6 p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-100"
                                     x-data="{ link: '{{ route('verification.show', $case->token) }}', copied: false }">
                                    <p class="text-sm font-medium text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        Link Verifikasi
                                    </p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <input type="text" readonly :value="link"
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white">
                                        <button type="button"
                                                @click="navigator.clipboard.writeText(link).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <span x-show="!copied">{{ __('Salin') }}</span>
                                            <span x-show="copied" x-cloak>{{ __('Tersalin') }}</span>
                                        </button>
                                    </div>

                                    @if ($case->isExpired())
                                        <p class="mt-3 text-xs font-semibold text-red-600">Link sudah kedaluwarsa.</p>
                                    @endif

                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('cases.regenerate-link', $case) }}">
                                            @csrf
                                            <x-secondary-button type="submit">{{ __('Regenerate Link') }}</x-secondary-button>
                                        </form>
                                        <form method="POST" action="{{ route('cases.close', $case) }}"
                                              onsubmit="return confirm('Nonaktifkan link ini?');">
                                            @csrf
                                            <x-danger-button>{{ __('Nonaktifkan Link') }}</x-danger-button>
                                        </form>
                                    </div>
                                </div>
                            @elseif ($case->status === \App\Enums\CaseStatus::Ditutup)
                                <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-200">
                                    <p class="text-sm font-medium text-gray-600">Link verifikasi telah dinonaktifkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Hasil Verifikasi (2 kolom) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
                        <div class="p-6 text-gray-900">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Hasil Verifikasi
                                </h3>
                                <span class="text-xs bg-gray-100 px-2.5 py-1 rounded-full text-gray-600">
                                    {{ $case->verifications->count() }} verifikasi
                                </span>
                            </div>

                            @if ($case->verifications->isNotEmpty())
                                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach ($case->verifications as $verification)
                                        <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                            {{-- Header Verifikasi --}}
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">
                                                    Verifikasi #{{ $verification->id }}
                                                </span>
                                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $verification->created_at->format('d M Y H:i') }}
                                                </span>
                                            </div>

                                            {{-- Detail Verifikasi --}}
                                            <div class="space-y-2 text-xs">
                                                {{-- IP Address --}}
                                                <div class="flex items-start gap-2">
                                                    <span class="text-gray-500 min-w-[65px]">IP:</span>
                                                    <span class="font-mono text-gray-700 bg-white px-2 py-0.5 rounded-md flex-1">
                                                        {{ $verification->ip_address ?? '-' }}
                                                    </span>
                                                </div>

                                                {{-- Perangkat --}}
                                                <div class="flex items-start gap-2">
                                                    <span class="text-gray-500 min-w-[65px]">Perangkat:</span>
                                                    <span class="text-gray-700 flex-1">
                                                        {{ $verification->browser }} · {{ $verification->operating_system }}
                                                        <span class="block text-gray-400 text-[10px] mt-0.5">{{ $verification->device_type }}</span>
                                                    </span>
                                                </div>

                                                {{-- Status Foto & Lokasi --}}
                                                <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-gray-200">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-gray-500 text-[10px] uppercase tracking-wider">Foto:</span>
                                                        @if ($verification->photo_status)
                                                            <x-status-badge :status="$verification->photo_status->value" class="text-xs">
                                                                {{ $verification->photo_status->value }}
                                                            </x-status-badge>
                                                        @else
                                                            <span class="text-gray-400 text-xs">tidak ada</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-gray-500 text-[10px] uppercase tracking-wider">Lokasi:</span>
                                                        @if ($verification->location_status)
                                                            <x-status-badge :status="$verification->location_status->value" class="text-xs">
                                                                {{ $verification->location_status->value }}
                                                            </x-status-badge>
                                                        @else
                                                            <span class="text-gray-400 text-xs">tidak ada</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Koordinat --}}
                                                @if ($verification->latitude && $verification->longitude)
                                                    <div class="mt-2 p-2 bg-white rounded-md">
                                                        <span class="text-gray-500 text-[10px] uppercase tracking-wider">Koordinat:</span>
                                                        <span class="font-mono text-xs text-gray-700 block mt-0.5">
                                                            {{ $verification->latitude }}, {{ $verification->longitude }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Foto Verifikasi --}}
                                        @if ($verification->photo_paths)
                                            <div class="mt-3">
                                                <p class="text-xs font-medium text-gray-500">Foto Verifikasi</p>
                                                <div class="mt-1 flex flex-wrap gap-2">
                                                    @foreach ($verification->photo_paths as $photoIndex => $photoPath)
                                                        <img
                                                            src="{{ URL::signedRoute('verification.photo', ['verification' => $verification->id, 'photo' => $photoIndex]) }}"
                                                            alt="Foto verifikasi {{ $photoIndex + 1 }}"
                                                            class="w-40 rounded-md border border-gray-200 shadow-sm"
                                                        >
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                            {{-- Map --}}
                                            @if ($verification->latitude && $verification->longitude)
                                                <div
                                                    x-data="leafletMap({{ $verification->latitude }}, {{ $verification->longitude }})"
                                                    class="z-0 mt-3 h-48 w-full rounded-md border border-gray-200"
                                                ></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada verifikasi</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Belum ada data verifikasi untuk kasus ini.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
