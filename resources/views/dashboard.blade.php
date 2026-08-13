<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('reports.index') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Laporan PDF') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                @php
                    $cards = [
                        ['total_verifications', 'Total Verifikasi', 'bg-indigo-600', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ['bank_transfer_verifications', 'Bank Transfer', 'bg-sky-600', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ['social_media_verifications', 'Social Media', 'bg-amber-500', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['verifications_today', 'Hari Ini', 'bg-emerald-600', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['locations_recorded', 'Lokasi Direkam', 'bg-rose-500', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['photos_recorded', 'Foto Direkam', 'bg-teal-600', 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z'],
                    ];
                @endphp

                @foreach ($cards as [$key, $label, $gradient, $icon])
                    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
                        <div class="flex items-center gap-3 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $gradient }} text-white shadow">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-lg font-bold text-gray-900">{{ $statistics[$key] }}</p>
                                <p class="truncate text-[11px] font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Verifikasi 7 Hari Terakhir</h3>
                        <div class="mt-4 h-64" x-data="barChart(@js($verificationsChart))">
                            <canvas></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Verifikasi per Jenis</h3>
                        <div class="mt-4 h-64" x-data="doughnutChart(@js($typeChart))">
                            <canvas></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                        <a href="{{ route('activities.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                            Lihat semua
                        </a>
                    </div>

                    @if ($activities->isEmpty())
                        <p class="mt-4 text-gray-500">Belum ada aktivitas.</p>
                    @else
                        <ol class="mt-4 space-y-3">
                            @foreach ($activities as $activity)
                                <li class="flex items-start justify-between gap-4 py-2 border-b border-gray-100 last:border-0">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-indigo-500"></span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $activity->activity->label() }}</p>
                                            <p class="text-xs text-gray-500">{{ $activity->verification_type?->label() ?? '—' }}</p>
                                            @if ($activity->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $activity->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-xs text-gray-500">{{ $activity->created_at->format('d M Y H:i') }}</span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
