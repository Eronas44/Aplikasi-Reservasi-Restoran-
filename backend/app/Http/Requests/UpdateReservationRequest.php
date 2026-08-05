<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
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
        $reservationId = (string) $this->route('reservation');

        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,user_id'],
            'table_id' => ['sometimes', 'integer', 'exists:tables,table_id'],
            'booking_code' => ['sometimes', 'string', 'max:50', 'unique:reservations,booking_code,' . $reservationId . ',reservation_id'],
            'reservation_date' => ['sometimes', 'date'],
            'reservation_time' => ['sometimes', 'date_format:H:i:s'],
            'number_of_guest' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:pending,confirmed,completed,cancelled,no_show'],
            'total_price' => ['sometimes', 'numeric', 'min:0'],
            'deposit_amount' => ['sometimes', 'numeric', 'min:0'],
            'payment_status' => ['sometimes', 'in:unpaid,partial,paid'],
            'staff_id' => ['sometimes', 'nullable', 'integer', 'exists:users,user_id'],
            'special_request' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
