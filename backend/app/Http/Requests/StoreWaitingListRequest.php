<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaitingListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,restaurant_id'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'number_of_guest' => ['required', 'integer', 'min:1'],
            'area' => ['nullable', 'in:indoor,outdoor,smoking,vip'],
            'status' => ['nullable', 'in:waiting,seated,cancelled'],
            'assigned_table_id' => ['nullable', 'integer', 'exists:tables,table_id'],
        ];
    }
}
