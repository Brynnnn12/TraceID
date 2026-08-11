<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('cases.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Buat Kasus') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_cases'] }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-wide">Total Kasus</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-emerald-500">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_verifications'] }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-wide">Total Verifikasi</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-sky-500">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['verifications_today'] }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-wide">Verifikasi Hari Ini</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-amber-500">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['locations_recorded'] }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-wide">Lokasi Direkam</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-rose-500">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['photos_recorded'] }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-wide">Foto Direkam</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Verifikasi 7 Hari Terakhir</h3>
                        <div class="mt-4 h-64" x-data="barChart(@js($verificationsChart))">
                            <canvas></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Kasus per Status</h3>
                        <div class="mt-4 h-64" x-data="doughnutChart(@js($statusChart))">
                            <canvas></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $activity->activity->label() }}</p>
                                        <a href="{{ route('cases.show', ['case' => $activity->case->getRouteKey()]) }}"
                                           class="text-xs text-indigo-600 hover:text-indigo-900">
                                            {{ $activity->case->reference_number }}
                                        </a>
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
