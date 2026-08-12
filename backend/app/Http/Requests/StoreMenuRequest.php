<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'restaurant_id' => ['nullable', 'integer', 'exists:restaurants,restaurant_id'],
            'item_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }
}
