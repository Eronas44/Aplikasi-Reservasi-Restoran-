<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Payment::query()
                ->with('reservation')
                ->orderByDesc('payment_id')
                ->paginate(20),
        ]);
    }

    public function show(int $payment): JsonResponse
    {
        return response()->json([
            'data' => Payment::query()->with('reservation')->findOrFail($payment),
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $reservation = Reservation::query()->findOrFail($request->input('reservation_id'));

        $payment = Payment::query()->create([
            'reservation_id' => $reservation->reservation_id,
            'type' => $request->input('type'),
            'amount' => $request->input('amount'),
            'method' => $request->input('method'),
            'status' => 'success',
            'transaction_code' => $request->input('transaction_code'),
            'gateway' => $request->input('gateway'),
            'paid_at' => now(),
        ]);

        // Update status pembayaran reservasi
        $reservation->update([
            'payment_status' => $reservation->payment_status === 'unpaid' ? 'partial' : $reservation->payment_status,
        ]);

        return response()->json([
            'message' => 'Payment recorded.',
            'data' => $payment->load('reservation'),
        ], 201);
    }

    public function destroy(int $payment): JsonResponse
    {
        $model = Payment::query()->findOrFail($payment);
        $model->delete();

        return response()->json([
            'message' => 'Payment deleted.',
        ]);
    }
}
