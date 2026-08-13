<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-mono font-semibold text-xl text-gray-800 leading-tight">
                {{ $verification->reference_number }}
            </h2>
            <a href="{{ route('verifications.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-900">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    @if ($photos->isNotEmpty())
                        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900">Foto</h3>
                                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    @foreach ($photos as $photo)
                                        <a href="{{ $photo['url'] }}" target="_blank" rel="noopener"
                                           class="group block overflow-hidden rounded-lg border border-gray-200">
                                            <img src="{{ $photo['url'] }}" alt="Foto verifikasi {{ $loop->iteration }}"
                                                 class="h-40 w-full object-cover transition group-hover:scale-105">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($verification->hasCoordinates())
                        <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900">Lokasi</h3>
                                <div class="z-0 mt-4 h-56 rounded-lg"
                                     x-data="leafletMap(@js((float) $verification->latitude), @js((float) $verification->longitude))"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Detail Verifikasi</h3>

                        <dl class="mt-4 grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Verifikasi</dt>
                                <dd class="mt-0.5 text-sm">{{ $verification->verification_type->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Waktu Verifikasi</dt>
                                <dd class="mt-0.5 text-sm">{{ $verification->created_at->format('d M Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Foto</dt>
                                <dd class="mt-0.5">
                                    <x-status-badge :status="$verification->photo_status?->value ?? ''">
                                        {{ $verification->photo_status?->label() ?? 'Tidak ada' }}
                                    </x-status-badge>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Lokasi</dt>
                                <dd class="mt-0.5">
                                    <x-status-badge :status="$verification->location_status?->value ?? ''">
                                        {{ $verification->location_status?->label() ?? 'Tidak ada' }}
                                    </x-status-badge>
                                </dd>
                            </div>
                            @if ($verification->hasCoordinates())
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Koordinat</dt>
                                    <dd class="mt-0.5 font-mono text-sm">
                                        {{ $verification->latitude }}, {{ $verification->longitude }}
                                        @if ($verification->accuracy !== null)
                                            <span class="ml-2 text-xs text-gray-400">±{{ $verification->accuracy }} m</span>
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Metadata Perangkat</h3>

                    <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                            <dd class="mt-0.5 font-mono text-sm">{{ $verification->ip_address ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Browser</dt>
                            <dd class="mt-0.5 text-sm">{{ $verification->browser ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sistem Operasi</dt>
                            <dd class="mt-0.5 text-sm">{{ $verification->operating_system ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipe Perangkat</dt>
                            <dd class="mt-0.5 text-sm">{{ $verification->device_type ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bahasa</dt>
                            <dd class="mt-0.5 text-sm">{{ $verification->language ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                            <dd class="mt-0.5 text-sm">{{ $verification->timezone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Resolusi Layar</dt>
                            <dd class="mt-0.5 text-sm">{{ $verification->screen_resolution ?? '—' }}</dd>
                        </div>
                        <div class="col-span-2 sm:col-span-3">
                            <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                            <dd class="mt-0.5 text-xs break-all text-gray-600">{{ $verification->user_agent ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
