<x-guest-layout>
    <div class="text-center">
        <h2 class="text-lg font-semibold text-green-600">Sudah Diverifikasi</h2>
        <p class="mt-2 text-sm text-gray-600">Kasus dengan nomor referensi berikut telah berhasil diverifikasi.</p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4">
        @foreach ($case->template->fields() as $field)
            @if ($case->fieldValue($field['key']) !== null)
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ $field['label'] }}</dt>
                    <dd class="mt-1 text-sm {{ $field['key'] === 'amount' ? 'font-semibold' : '' }}">{{ $case->formattedField($field['key']) }}</dd>
                </div>
            @endif
        @endforeach
        <div>
            <dt class="text-sm font-medium text-gray-500">No. Referensi</dt>
            <dd class="mt-1 font-mono text-sm">{{ $case->reference_number }}</dd>
        </div>
    </dl>
</x-guest-layout>
