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

            <div x-data="{
                bank_name: @js(old('bank_name', $bankTransfer?->bank_name ?? '')),
                account_number: @js(old('account_number', $bankTransfer?->account_number ?? '')),
                amount: @js(old('amount', $bankTransfer?->amount ?? '')),
                notes: @js(old('notes', $bankTransfer?->notes ?? '')),
            }" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form method="POST" action="{{ route('bank-transfer.update') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <x-input-label for="bank_name" value="Nama Bank" />
                                <x-text-input id="bank_name" name="bank_name" type="text"
                                              x-model="bank_name"
                                              class="mt-1 block w-full"
                                              :value="old('bank_name', $bankTransfer?->bank_name)" />
                                <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="account_number" value="Nomor Rekening" />
                                <x-text-input id="account_number" name="account_number" type="text"
                                              x-model="account_number"
                                              class="mt-1 block w-full"
                                              :value="old('account_number', $bankTransfer?->account_number)" />
                                <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="amount" value="Jumlah Transfer" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0"
                                              x-model="amount"
                                              class="mt-1 block w-full"
                                              :value="old('amount', $bankTransfer?->amount)" />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" value="Catatan" />
                                <textarea id="notes" name="notes" rows="3" x-model="notes"
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
                                <a href="{{ route('verification.bank-transfer') }}" target="_blank"
                                   class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ __('Lihat halaman publik') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:sticky lg:top-6">
                    <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Pratinjau</h3>
                            <p class="mt-1 text-xs text-gray-400">Tampilan kartu di halaman publik saat admin melengkapi data.</p>

                            <div class="relative mt-4 overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a1a2e] via-[#16213e] to-[#0f3460] p-6 text-white shadow-2xl">
                                <div class="pointer-events-none absolute -right-8 -top-8 h-20 w-20 rounded-full bg-[#eb001b]/20 blur-xl"></div>
                                <div class="pointer-events-none absolute right-0 top-0 h-20 w-20 rounded-full bg-[#f79e1b]/20 blur-xl"></div>

                                <p class="text-xs font-medium uppercase tracking-wider text-gray-300">Bank Transfer</p>
                                <p class="mt-1 text-sm font-semibold text-white/90" x-text="bank_name || '—'"></p>

                                <div class="mt-6">
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-300">Nomor Rekening</p>
                                    <p class="mt-1 font-mono text-2xl font-medium tracking-widest" x-text="account_number || '—'"></p>
                                </div>

                                <div class="mt-6 flex items-end justify-between">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wider text-gray-300">Jumlah Transfer</p>
                                        <p class="mt-1 text-2xl font-bold text-white" x-text="amount ? 'Rp ' + new Intl.NumberFormat('id-ID').format(amount) : '—'"></p>
                                    </div>
                                    <p class="max-w-[45%] truncate text-right text-xs text-gray-300" x-text="notes"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVerifyLink() {
            var url = '{{ route('verification.bank-transfer') }}';

            navigator.clipboard.writeText(url).then(function () {
                alert('Link disalin: ' + url);
            });
        }
    </script>
</x-app-layout>
