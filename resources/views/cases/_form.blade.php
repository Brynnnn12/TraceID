<div x-data="{
    templateId: @js((string) $form['templateId']),
    fields: @js($form['fields']),
}">
    <div>
        <x-input-label for="template_id" :value="__('Template Verifikasi')" />
        <select id="template_id" name="template_id" x-model="templateId" required
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="" disabled>{{ __('Pilih template') }}</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}"
                    @if ((string) old('template_id', $case->template_id ?? '') === (string) $template->id) selected @endif>
                    {{ $template->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('template_id')" class="mt-2" />
    </div>

    @foreach ($templates as $template)
        <div x-show="templateId == {{ $template->id }}" x-cloak class="mt-4">
            <p class="text-sm font-medium text-gray-500">{{ __('Detail '.$template->name) }}</p>

            @foreach ($template->fields() as $field)
                <div class="mt-4">
                    <x-input-label for="field-{{ $template->id }}-{{ $field['key'] }}" :value="$field['label']" />

                    @if ($field['type'] === 'textarea')
                        <textarea id="field-{{ $template->id }}-{{ $field['key'] }}"
                                  name="fields[{{ $field['key'] }}]"
                                  x-model="fields['{{ $field['key'] }}']"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    @else
                        <x-text-input id="field-{{ $template->id }}-{{ $field['key'] }}"
                                      name="fields[{{ $field['key'] }}]"
                                      type="{{ $field['type'] }}"
                                      x-model="fields['{{ $field['key'] }}']"
                                      class="mt-1 block w-full"
                                      @if ($field['type'] === 'number') step="0.01" min="0" @endif />
                    @endif

                    <x-input-error :messages="$errors->get('fields.'.$field['key'])" class="mt-2" />
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="mt-4">
        <x-input-label for="notes" :value="__('Catatan')" />
        <textarea id="notes" name="notes" rows="4"
                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $case->notes ?? null) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>
