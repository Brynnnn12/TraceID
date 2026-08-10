<div>
    <div>
        <x-input-label for="target_name" :value="__('Nama Target')" />
        <x-text-input id="target_name" name="target_name" type="text" class="mt-1 block w-full"
                      :value="old('target_name', $case->target_name ?? null)" required autofocus />
        <x-input-error :messages="$errors->get('target_name')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="bank_name" :value="__('Nama Bank')" />
        <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full"
                      :value="old('bank_name', $case->bank_name ?? null)" required />
        <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="account_number" :value="__('Nomor Rekening')" />
        <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full"
                      :value="old('account_number', $case->account_number ?? null)" required />
        <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="amount" :value="__('Jumlah Transfer (Rp)')" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full"
                      :value="old('amount', $case->amount ?? null)" required />
        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="notes" :value="__('Catatan')" />
        <textarea id="notes" name="notes" rows="4"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $case->notes ?? null) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>
