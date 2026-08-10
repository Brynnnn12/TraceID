<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesTemplateFields;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCaseRequest extends FormRequest
{
    use ValidatesTemplateFields;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->templateFieldRules();
    }
}
