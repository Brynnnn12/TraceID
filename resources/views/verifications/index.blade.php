<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Verifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('verifications.index') }}" class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div>
                            <select name="type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Semua Jenis</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-primary-button>{{ __('Filter') }}</x-primary-button>
                            <a href="{{ route('verifications.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Reset') }}</a>
                        </div>
                    </form>

                    @if ($verifications->isEmpty())
                        <div class="py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500">Belum ada verifikasi.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Referensi</th>
                                        <th class="px-4 py-3">Jenis</th>
                                        <th class="px-4 py-3">Foto</th>
                                        <th class="px-4 py-3">Lokasi</th>
                                        <th class="px-4 py-3">Waktu</th>
                                        <th class="px-4 py-3 text-right">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($verifications as $verification)
                                        <tr class="transition hover:bg-gray-50">
                                            <td class="px-4 py-3 font-mono text-sm">{{ $verification->reference_number }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1.5 text-sm">
                                                    @if ($verification->verification_type->value === 'bank_transfer')
                                                        <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    @endif
                                                    {{ $verification->verification_type->label() }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <x-status-badge :status="$verification->photo_status?->value ?? ''">
                                                    {{ $verification->photo_status?->label() ?? 'Tidak ada' }}
                                                </x-status-badge>
                                            </td>
                                            <td class="px-4 py-3">
                                                <x-status-badge :status="$verification->location_status?->value ?? ''">
                                                    {{ $verification->location_status?->label() ?? 'Tidak ada' }}
                                                </x-status-badge>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $verification->created_at->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('verifications.show', $verification) }}"
                                                   class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                                    Detail
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $verifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
