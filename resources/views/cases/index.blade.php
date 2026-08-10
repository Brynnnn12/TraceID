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
                    @if ($cases->isEmpty())
                        <p class="text-gray-500">Belum ada kasus.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-2">No. Referensi</th>
                                    <th class="px-4 py-2">Nama Target</th>
                                    <th class="px-4 py-2">Bank</th>
                                    <th class="px-4 py-2">Jumlah</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Dibuat</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($cases as $case)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-sm">{{ $case->reference_number }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $case->target_name }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $case->bank_name }}</td>
                                        <td class="px-4 py-2 text-sm">Rp {{ number_format($case->amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $case->status->value }}
                                            </span>
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
