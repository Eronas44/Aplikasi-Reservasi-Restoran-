<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationItemRequest extends FormRequest
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
            'reservation_id' => ['sometimes', 'integer', 'exists:reservations,reservation_id'],
            'menu_id' => ['sometimes', 'integer', 'exists:menus,menu_id'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'subtotal_price' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
