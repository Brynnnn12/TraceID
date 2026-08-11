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
        <div x-show="templateId == '{{ $template->id }}'" x-cloak class="mt-4">
            <p class="text-sm font-medium text-gray-500">{{ __('Detail '.$template->name) }}</p>

            @foreach ($template->fields() as $field)
                @php
                    $fieldKey = $field['key'] ?? $field['name'] ?? '';
                    $fieldLabel = $field['label'] ?? $fieldKey;
                    $fieldType = $field['type'] ?? 'text';
                @endphp

                @if ($fieldKey)
                <div class="mt-4">
                    <x-input-label for="field-{{ $template->id }}-{{ $fieldKey }}" :value="$fieldLabel" />

                    @if ($fieldType === 'textarea')
                        <textarea id="field-{{ $template->id }}-{{ $fieldKey }}"
                                  name="fields[{{ $fieldKey }}]"
                                  x-model="fields['{{ $fieldKey }}']"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                    @elseif ($fieldType === 'date')
                        <x-text-input id="field-{{ $template->id }}-{{ $fieldKey }}"
                                      name="fields[{{ $fieldKey }}]"
                                      type="date"
                                      x-model="fields['{{ $fieldKey }}']"
                                      class="mt-1 block w-full" />
                    @elseif ($fieldType === 'number')
                        <x-text-input id="field-{{ $template->id }}-{{ $fieldKey }}"
                                      name="fields[{{ $fieldKey }}]"
                                      type="number"
                                      x-model="fields['{{ $fieldKey }}']"
                                      class="mt-1 block w-full"
                                      step="0.01"
                                      min="0" />
                    @elseif ($fieldType === 'email')
                        <x-text-input id="field-{{ $template->id }}-{{ $fieldKey }}"
                                      name="fields[{{ $fieldKey }}]"
                                      type="email"
                                      x-model="fields['{{ $fieldKey }}']"
                                      class="mt-1 block w-full" />
                    @elseif ($fieldType === 'select' && isset($field['options']))
                        <select id="field-{{ $template->id }}-{{ $fieldKey }}"
                                name="fields[{{ $fieldKey }}]"
                                x-model="fields['{{ $fieldKey }}']"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Pilih...') }}</option>
                            @foreach ($field['options'] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @else
                        <x-text-input id="field-{{ $template->id }}-{{ $fieldKey }}"
                                      name="fields[{{ $fieldKey }}]"
                                      type="{{ $fieldType }}"
                                      x-model="fields['{{ $fieldKey }}']"
                                      class="mt-1 block w-full" />
                    @endif

                    <x-input-error :messages="$errors->get('fields.' . $fieldKey)" class="mt-2" />
                </div>
                @endif
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
