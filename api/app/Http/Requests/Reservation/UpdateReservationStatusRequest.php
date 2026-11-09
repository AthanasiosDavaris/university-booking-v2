<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReservationStatusRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(['confirmed', 'rejected'])],
            'rejection_reason' => [
                'exclude_unless:status,rejected', // ignored completely if the status is not rejected
                'required_if:status,rejected',    // required if rejected
                'string',
                'min:5',
                'max:500',
            ],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'status.in'                    => 'The status must be either "confirmed" or "rejected".',
            'rejection_reason.required_if' => 'A rejection reason must be provided when rejecting a reservation.',
            'rejection_reason.min'         => 'The rejection reason must be at least 5 characters long.',
        ];
    }
}