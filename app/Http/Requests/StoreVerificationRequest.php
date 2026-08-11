<?php

namespace App\Http\Requests;

use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Enums\VerificationType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'latitude' => $this->nullIfEmpty('latitude'),
            'longitude' => $this->nullIfEmpty('longitude'),
            'accuracy' => $this->nullIfEmpty('accuracy'),
            'timezone' => $this->nullIfEmpty('timezone'),
            'screen_resolution' => $this->nullIfEmpty('screen_resolution'),
            'photo_status' => $this->nullIfEmpty('photo_status'),
            'location_status' => $this->nullIfEmpty('location_status'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(VerificationType::class)],
            'photo' => ['nullable', 'array'],
            'photo_status' => ['nullable', new Enum(PhotoStatus::class)],
            'location_status' => ['nullable', new Enum(LocationStatus::class)],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'screen_resolution' => ['nullable', 'string', 'max:64'],
        ];
    }

    private function nullIfEmpty(string $key): mixed
    {
        $value = $this->input($key);

        return $value === '' ? null : $value;
    }
}
