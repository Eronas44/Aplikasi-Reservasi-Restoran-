<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:deposit,settlement,refund'],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', 'in:bank_transfer,ewallet,qris,cash,card'],
            'status' => ['sometimes', 'in:pending,success,failed,refunded'],
            'transaction_code' => ['nullable', 'string', 'max:100'],
            'gateway' => ['nullable', 'string', 'max:50'],
            'va_number' => ['nullable', 'string', 'max:50'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'payment_details' => ['nullable', 'array'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
