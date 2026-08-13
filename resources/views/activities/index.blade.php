<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('activities.index') }}" class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                   placeholder="{{ __('Cari no. referensi atau deskripsi...') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
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
                            <a href="{{ route('activities.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Reset') }}</a>
                        </div>
                    </form>

                    @if ($activities->isEmpty())
                        <div class="py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500">Belum ada aktivitas.</p>
                        </div>
                    @else
                        <ol class="space-y-0">
                            @foreach ($activities as $activity)
                                <li class="relative flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
                                    <span class="mt-1.5 flex h-3 w-3 shrink-0 items-center justify-center">
                                        <span class="h-3 w-3 rounded-full bg-indigo-500 ring-4 ring-indigo-100"></span>
                                    </span>
                                    <div class="flex flex-1 items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $activity->activity->label() }}</p>
                                            <p class="text-xs text-gray-500">{{ $activity->verification_type?->label() ?? '—' }}</p>
                                            @if ($activity->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $activity->description }}</p>
                                            @endif
                                        </div>
                                        <span class="shrink-0 text-xs text-gray-500">{{ $activity->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-4">
                            {{ $activities->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
