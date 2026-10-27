<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'uuid', 'exists:rooms,id'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'purpose' => ['required', 'string', Rule::in(['lecture', 'exam', 'event', 'meeting'])],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);

            if (!$start->isSameDay($end)) {
                $validator->errors()->add('end_time', 'Reservations cannot span across multiple days.');
            }

            if ($start->diffInHours($end) > 8) {
                $validator->errors()->add('end_time', 'A single reservation cannot exceed 8 hours.');
            }
        });
    }
}