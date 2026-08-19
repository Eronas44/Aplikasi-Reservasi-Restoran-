<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,restaurant_id'],
            'name' => ['required', 'string', 'max:150'],
            'holiday_date' => ['required', 'date_format:Y-m-d'],
            'is_closed' => ['sometimes', 'boolean'],
        ];
    }
}
