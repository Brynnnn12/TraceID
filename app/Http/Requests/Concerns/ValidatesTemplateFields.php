<?php

namespace App\Http\Requests\Concerns;

use App\Models\VerificationTemplate;

trait ValidatesTemplateFields
{
    /**
     * @return array<string, mixed>
     */
    private function templateFieldRules(): array
    {
        $template = VerificationTemplate::find($this->input('template_id'));

        $rules = [
            'template_id' => ['required', 'integer', 'exists:verification_templates,id'],
            'fields' => ['sometimes', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        foreach ($template?->fields() ?? [] as $field) {
            $rules['fields.'.$field['key']] = array_merge(['required'], $this->fieldValidationRules($field['type']));
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    private function fieldValidationRules(string $type): array
    {
        return match ($type) {
            'number' => ['numeric', 'min:0'],
            'date' => ['date'],
            'textarea' => ['string', 'max:1000'],
            default => ['string', 'max:255'],
        };
    }
}
