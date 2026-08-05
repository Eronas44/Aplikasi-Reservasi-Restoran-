<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationItemRequest extends FormRequest
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
            'reservation_id' => ['required', 'integer', 'exists:reservations,reservation_id'],
            'menu_id' => ['required', 'integer', 'exists:menus,menu_id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'subtotal_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
