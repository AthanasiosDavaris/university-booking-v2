<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_accessible' => $this->has('is_accessible') ? filter_var($this->is_accessible, FILTER_VALIDATE_BOOLEAN) : null,
            'has_hvac' => $this->has('has_hvac') ? filter_var($this->has_hvac, FILTER_VALIDATE_BOOLEAN) : null,
        ]);
    }

    public function rules(): array{
        return [
            'start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
            'capacity' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'exam_capacity' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'room_type' => ['sometimes', 'string', 'max:50'],
            'is_accessible' => ['sometimes', 'boolean', 'nullable'],
            'has_hvac' => ['sometimes', 'boolean', 'nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'The end time must be strictly after the start time.',
        ];
    }
}