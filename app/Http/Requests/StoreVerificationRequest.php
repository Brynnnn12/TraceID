<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVerificationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'screen_resolution' => ['nullable', 'string', 'max:64'],
            'photo_status' => ['nullable', 'string', 'max:32'],
            'location_status' => ['nullable', 'string', 'max:32'],
        ];
    }
}
