<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
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
            'table_number' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'location_area' => ['required', 'in:indoor,outdoor,smoking,vip'],
            'status' => ['sometimes', 'in:available,reserved,occupied,maintenance'],
        ];
    }
}
