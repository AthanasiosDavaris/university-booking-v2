<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreReservationRequest extends FormRequest
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
            'room_id'   => ['required', 'integer', 'exists:rooms,id'],
            'starts_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'ends_at'   => ['required', 'date_format:Y-m-d H:i:s', 'after:starts_at'],
            'title'     => ['required', 'string', 'min:3', 'max:255'],
            'purpose'   => ['sometimes', 'string', 'in:lecture,exam,meeting,event'],
            'status'    => ['sometimes', 'string', 'in:pending,confirmed'],
        ];
    }

    /**
     * Custom university domain validations:
     * 1. Same-day check (a single booking cannot span across days).
     * 2. Max duration check (no booking longer than 8 hours).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('starts_at') && $this->filled('ends_at')) {
                try {
                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $this->starts_at);
                    $end   = Carbon::createFromFormat('Y-m-d H:i:s', $this->ends_at);

                    if ($start && $end) {
                        if (!$start->isSameDay($end)) {
                            $validator->errors()->add('ends_at', 'A reservation cannot span across multiple calendar days.');
                        }

                        if ($start->diffInHours($end) > 8) {
                            $validator->errors()->add('ends_at', 'A single reservation session cannot exceed 8 hours.');
                        }
                    }
                } catch (\Exception $e) {
                    // Handled by the date_format rule
                }
            }
        });
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'room_id.exists'     => 'The selected classroom does not exist in the database.',
            'starts_at.required' => 'The start datetime is required (format: Y-m-d H:i:s).',
            'ends_at.required'   => 'The end datetime is required (format: Y-m-d H:i:s).',
            'ends_at.after'      => 'The end time must be strictly after the start time.',
            'purpose.in'         => 'The purpose must be one of: lecture, exam, meeting, event.',
        ];
    }
}