<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,restaurant_id'],
            'deposit_percent' => ['required', 'numeric', 'between:0,100'],
            'deposit_min_amount' => ['nullable', 'numeric', 'min:0'],
            'refund_full_hours' => ['required', 'integer', 'min:0'],
            'refund_partial_hours' => ['required', 'integer', 'min:0'],
            'refund_partial_percent' => ['required', 'numeric', 'between:0,100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
