<?php

namespace App\Http\Requests;

use App\Enums\ConfigStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateSocialMediaRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'platform' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'profile_url' => ['nullable', 'url', 'max:2048'],
            'caption' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', new Enum(ConfigStatus::class)],
        ];
    }
}
