<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Verifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                        <p class="text-gray-500">Belum ada verifikasi.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-3 py-2">Referensi</th>
                                    <th class="px-3 py-2">Jenis</th>
                                    <th class="px-3 py-2">Foto</th>
                                    <th class="px-3 py-2">Lokasi</th>
                                    <th class="px-3 py-2">Waktu</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($verifications as $verification)
                                    <tr>
                                        <td class="px-3 py-2 font-mono text-sm">{{ $verification->reference_number }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $verification->verification_type->label() }}</td>
                                        <td class="px-3 py-2">
                                            <x-status-badge :status="$verification->photo_status?->value ?? ''">
                                                {{ $verification->photo_status?->label() ?? 'Tidak ada' }}
                                            </x-status-badge>
                                        </td>
                                        <td class="px-3 py-2">
                                            <x-status-badge :status="$verification->location_status?->value ?? ''">
                                                {{ $verification->location_status?->label() ?? 'Tidak ada' }}
                                            </x-status-badge>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-600">{{ $verification->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-3 py-2 text-right">
                                            <a href="{{ route('verifications.show', $verification) }}"
                                               class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $verifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
