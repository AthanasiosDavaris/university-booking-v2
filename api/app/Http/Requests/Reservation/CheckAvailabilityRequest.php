<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Cast boolean query parameters from string ('true'/'false'/'1'/'0') to actual booleans.
     */
    protected function prepareForValidation(): void
    {
        $merges = [];

        if ($this->has('is_accessible')) {
            $merges['is_accessible'] = filter_var($this->input('is_accessible'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($this->has('has_projector')) {
            $merges['has_projector'] = filter_var($this->input('has_projector'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($this->has('has_ac')) {
            $merges['has_ac'] = filter_var($this->input('has_ac'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if (!empty($merges)) {
            $this->merge($merges);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'starts_at'      => ['required', 'date_format:Y-m-d H:i:s'],
            'ends_at'        => ['required', 'date_format:Y-m-d H:i:s', 'after:starts_at'],
            'purpose'        => ['sometimes', 'string', 'in:lecture,exam,meeting,event'],
            'capacity'       => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'exam_capacity'  => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'room_type'      => ['sometimes', 'string', 'max:50'],
            'is_accessible'  => ['sometimes', 'boolean', 'nullable'],
            'has_projector'  => ['sometimes', 'boolean', 'nullable'],
            'has_ac'         => ['sometimes', 'boolean', 'nullable'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'starts_at.required'    => 'The start datetime is required (format: Y-m-d H:i:s).',
            'ends_at.required'      => 'The end datetime is required (format: Y-m-d H:i:s).',
            'ends_at.after'         => 'The end time must be strictly after the start time.',
            'purpose.in'            => 'The purpose must be one of: lecture, exam, meeting, event.',
        ];
    }
}