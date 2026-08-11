<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Konfigurasi Bank Transfer') }}
            </h2>
            <x-secondary-button onclick="copyVerifyLink()">
                {{ __('Salin Link') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-md text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bank-transfer.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="bank_name" value="Nama Bank" />
                            <x-text-input id="bank_name" name="bank_name" type="text"
                                          class="mt-1 block w-full"
                                          :value="old('bank_name', $bankTransfer?->bank_name)" />
                            <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="account_number" value="Nomor Rekening" />
                            <x-text-input id="account_number" name="account_number" type="text"
                                          class="mt-1 block w-full"
                                          :value="old('account_number', $bankTransfer?->account_number)" />
                            <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="amount" value="Jumlah Transfer" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0"
                                          class="mt-1 block w-full"
                                          :value="old('amount', $bankTransfer?->amount)" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" value="Catatan" />
                            <textarea id="notes" name="notes" rows="3"
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $bankTransfer?->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @php $status = old('status', $bankTransfer?->status?->value ?? 'aktif'); @endphp
                                <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                                <option value="ditutup" @selected($status === 'ditutup')>Ditutup</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('verification.show') }}" target="_blank"
                               class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                {{ __('Lihat halaman publik') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVerifyLink() {
            var url = '{{ route('verification.show') }}';

            navigator.clipboard.writeText(url).then(function () {
                alert('Link disalin: ' + url);
            });
        }
    </script>
</x-app-layout>
