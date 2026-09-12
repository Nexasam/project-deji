<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingDatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'arrival_date' => ['required', 'date'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
