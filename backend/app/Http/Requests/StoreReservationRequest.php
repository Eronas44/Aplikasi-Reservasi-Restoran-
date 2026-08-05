<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,user_id'],
            'table_id' => ['required', 'integer', 'exists:tables,table_id'],
            'booking_code' => ['required', 'string', 'max:50', 'unique:reservations,booking_code'],
            'reservation_date' => ['required', 'date'],
            'reservation_time' => ['required', 'date_format:H:i:s'],
            'number_of_guest' => ['required', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:pending,confirmed,completed,cancelled,no_show'],
            'total_price' => ['sometimes', 'numeric', 'min:0'],
            'deposit_amount' => ['sometimes', 'numeric', 'min:0'],
            'payment_status' => ['sometimes', 'in:unpaid,partial,paid'],
            'staff_id' => ['sometimes', 'nullable', 'integer', 'exists:users,user_id'],
            'special_request' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
