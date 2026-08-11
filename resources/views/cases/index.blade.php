<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kasus') }}
            </h2>
            <a href="{{ route('cases.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Buat Kasus') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('cases.index') }}" class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div>
                            <select name="status"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('Semua Status') }}</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="template"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('Semua Template') }}</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}" @selected((string) $filters['template'] === (string) $template->id)>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-primary-button>{{ __('Filter') }}</x-primary-button>
                            <a href="{{ route('cases.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Reset') }}</a>
                        </div>
                    </form>

                    @if ($cases->isEmpty())
                        <p class="text-gray-500">Belum ada kasus.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-2">No. Referensi</th>
                                    <th class="px-4 py-2">Template</th>
                                    <th class="px-4 py-2">Ringkasan</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Dibuat</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($cases as $case)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-sm">{{ $case->reference_number }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $case->template->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $case->summary() }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <x-status-badge :status="$case->status->value">
                                                {{ $case->status->label() }}
                                            </x-status-badge>
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $case->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-2 text-sm text-right">
                                            <a href="{{ route('cases.show', ['case' => $case->getRouteKey()]) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $cases->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
