<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreExternalCalendarConnectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['provider' => ['required', 'in:airbnb,bookingcom'], 'feed_url' => ['required', 'url:https', 'max:2048']]; }
}
