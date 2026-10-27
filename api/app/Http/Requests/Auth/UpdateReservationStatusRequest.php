<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(['approved', 'rejected', 'cancelled'])],
            'rejection_reason' => [
                'exclude_unless:status,rejected', // Ignore this field entirely unless status is rejected
                'required_if:status,rejected',    // Make it required if status is rejected
                'string',
                'min:5',
                'max:500'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required_if' => 'A rejection reason must be provided when rejecting a reservation.',
        ];
    }
}