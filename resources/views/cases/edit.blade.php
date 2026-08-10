<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kasus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('cases.update', ['case' => $case->getRouteKey()]) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        @include('cases._form', [
                            'case' => $case,
                            'form' => [
                                'templateId' => (string) old('template_id', $case->template_id ?? ''),
                                'fields' => old('fields', $case->fields ?? []),
                            ],
                        ])

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('cases.show', ['case' => $case->getRouteKey()]) }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
