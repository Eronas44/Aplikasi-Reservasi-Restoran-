<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
            'category_id' => ['sometimes', 'integer', 'exists:categories,category_id'],
            'restaurant_id' => ['sometimes', 'nullable', 'integer', 'exists:restaurants,restaurant_id'],
            'item_name' => ['sometimes', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }
}
