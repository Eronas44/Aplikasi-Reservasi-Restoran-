<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
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
            'table_number' => ['sometimes', 'string', 'max:50'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'location_area' => ['sometimes', 'in:indoor,outdoor,smoking,vip'],
            'status' => ['sometimes', 'in:available,reserved,occupied,maintenance'],
            'layout_row' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
            'layout_column' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
