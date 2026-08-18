<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min(max((int) request()->query('limit', 20), 1), 500);

        return response()->json([
            'data' => Payment::query()
                ->with('reservation')
                ->orderByDesc('payment_id')
                ->paginate($perPage),
        ]);
    }

    public function show(int $payment): JsonResponse
    {
        return response()->json([
            'data' => Payment::query()->with('reservation')->findOrFail($payment),
        ]);
    }

    /**
     * Catat transaksi pembayaran.
     *
     * Status default = "pending" (pembayaran belum dibayar). Frontend
     * memanggil endpoint verify (simulasi) atau webhook callback (gateway
     * asli) untuk menandai sukses; tidak ada klien yang menandai sukses
     * kecuali backend sendiri.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $reservation = Reservation::query()->findOrFail($request->input('reservation_id'));

        $status = $request->input('status', 'pending');

        $payment = Payment::query()->create([
            'reservation_id' => $reservation->reservation_id,
            'type' => $request->input('type'),
            'amount' => $request->input('amount'),
            'method' => $request->input('method'),
            'status' => $status,
            'transaction_code' => $request->input('transaction_code'),
            'gateway' => $request->input('gateway'),
            'va_number' => $request->input('va_number'),
            'phone_number' => $request->input('phone_number'),
            'payment_details' => $request->input('payment_details'),
            'expires_at' => $request->input('expires_at'),
            'paid_at' => $status === 'success' ? now() : null,
        ]);

        if ($status === 'success') {
            $this->markSuccess($payment);
        }

        return response()->json([
            'message' => 'Payment recorded.',
            'data' => $payment->load('reservation'),
        ], 201);
    }

    /**
     * Instruksi pembayaran untuk pelanggan (VA / rekening, QRIS, e-wallet,
     * tenggat Bayar di Restoran). Dipakai halaman instruksi_pembayaran.php.
     */
    public function instructions(int $payment): JsonResponse
    {
        $model = Payment::query()
            ->with(['reservation.table.restaurant'])
            ->findOrFail($payment);

        $this->authorizeOwnerOrStaff($model);

        $resto = $model->reservation?->table?->restaurant;
        $config = $resto?->paymentMethods()
            ->where('method', $model->method)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'data' => [
                'payment' => $model,
                'instructions' => $this->instructionPayload($model, $config),
            ],
        ]);
    }

    /**
     * Verifikasi pembayaran (mode SIMULASI).
     *
     * Sisi "pelanggan menekan 'Saya Sudah Bayar'" → backend menandai
     * payment success & reservasi confirmed. Idempoten: jika sudah sukses,
     * hanya mengembalikan status terkini. Saat gateway asli dipasang,
     * endpoint ini tidak dipakai klien; verifikasi berasal dari webhook
     * callback dari gateway.
     */
    public function verify(Request $request, int $payment): JsonResponse
    {
        $model = Payment::query()->findOrFail($payment);

        $this->authorizeOwnerOrStaff($model);

        if ($request->filled('phone_number')) {
            $model->update(['phone_number' => $request->input('phone_number')]);
        }

        if ($model->status === 'success') {
            return response()->json([
                'message' => 'Payment already verified.',
                'data' => $model->load('reservation'),
            ]);
        }

        $this->markSuccess($model);

        return response()->json([
            'message' => 'Payment verified (simulation).',
            'data' => $model->fresh()->load('reservation'),
        ]);
    }

    /**
     * Ubah data pembayaran yang masih pending (mis. pelanggan mengganti metode
     * dari "Review" setelah reservasi dibuat). Hanya berlaku selama status
     * masih pending; begitu sukses, tidak bisa diubah.
     */
    public function update(Request $request, int $payment): JsonResponse
    {
        $model = Payment::query()->findOrFail($payment);

        $this->authorizeOwnerOrStaff($model);

        if ($model->status !== 'pending') {
            return response()->json([
                'message' => 'Payment already processed; cannot be updated.',
            ], 422);
        }

        $validated = $request->validate([
            'method' => ['sometimes', 'in:bank_transfer,ewallet,qris,cash,card'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'gateway' => ['sometimes', 'nullable', 'string', 'max:50'],
            'va_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:30'],
            'payment_details' => ['sometimes', 'nullable', 'array'],
            'expires_at' => ['sometimes', 'nullable', 'date'],
        ]);

        $model->update($validated);

        return response()->json([
            'message' => 'Payment updated.',
            'data' => $model->fresh()->load('reservation'),
        ]);
    }

    /**
     * Webhook callback gateway (SIAP GATEWAY ASLI).
     *
     * Saat terhubung Midtrans/Xendit, gateway memanggil endpoint ini dengan
     * token rahasia (header X-Payment-Token = PAYMENT_WEBHOOK_TOKEN) dan
     * body berisi status pembayaran. Token kosong berarti callback dinonaktifkan.
     */
    public function callback(Request $request, int $payment): JsonResponse
    {
        $token = Config::get('app.payment_webhook_token', env('PAYMENT_WEBHOOK_TOKEN', ''));

        if ($token === '' || !hash_equals($token, (string) $request->header('X-Payment-Token', ''))) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $model = Payment::query()->findOrFail($payment);

        $gatewayStatus = (string) $request->input('status', 'success');

        if ($gatewayStatus === 'success' && $model->status !== 'success') {
            $this->markSuccess($model);
        } elseif ($gatewayStatus === 'failed' && $model->status === 'pending') {
            $model->update(['status' => 'failed']);
        }

        return response()->json([
            'message' => 'Callback processed.',
            'data' => $model->fresh()->load('reservation'),
        ]);
    }

    public function destroy(int $payment): JsonResponse
    {
        $model = Payment::query()->findOrFail($payment);
        $model->delete();

        return response()->json([
            'message' => 'Payment deleted.',
        ]);
    }

    /**
     * Tandai pembayaran sukses + sinkronkan status reservasi (confirmed).
     */
    private function markSuccess(Payment $payment): void
    {
        $payment->update([
            'status' => 'success',
            'paid_at' => $payment->paid_at ?? now(),
        ]);

        $reservation = $payment->reservation;
        if ($reservation) {
            $reservation->update([
                'status' => 'confirmed',
                'payment_status' => $reservation->payment_status === 'paid' ? 'paid' : 'partial',
            ]);
        }
    }

    /**
     * Hanya pemilik reservasi atau staf/admin yang boleh melihat/verifikasi.
     */
    private function authorizeOwnerOrStaff(Payment $payment): void
    {
        $user = auth('web')->user();
        $reservation = $payment->reservation;

        if (!$user || !$reservation) {
            abort(403, 'Forbidden.');
        }

        if (in_array($user->role, ['staff', 'admin'], true)) {
            return;
        }

        if ((int) $reservation->user_id !== (int) $user->user_id) {
            abort(403, 'Forbidden.');
        }
    }

    /**
     * Susun payload instruksi per metode untuk ditampilkan frontend.
     */
    private function instructionPayload(Payment $payment, ?object $config): array
    {
        $details = (array) ($payment->payment_details ?? []);

        return match ($payment->method) {
            'bank_transfer' => [
                'label' => $config?->label ?? 'Transfer Bank BCA',
                'account_name' => $config?->account_name ?? ($details['account_name'] ?? 'Kafiber Resto'),
                'account_number' => $payment->va_number ?? $config?->account_number ?? '1234567890',
                'bank' => $details['bank'] ?? 'BCA',
                'amount' => (float) $payment->amount,
                'expires_at' => optional($payment->expires_at)->toDateTimeString(),
            ],
            'ewallet' => [
                'label' => $config?->label ?? 'E-Wallet (OVO / GoPay / DANA)',
                'merchant_phone' => $config?->phone_number ?? ($details['merchant_phone'] ?? ''),
                'customer_phone' => $payment->phone_number,
                'amount' => (float) $payment->amount,
            ],
            'qris' => [
                'label' => $config?->label ?? 'QRIS',
                'qris_image' => $config?->qris_image,
                'qr_payload' => $details['qr_payload'] ?? null,
                'amount' => (float) $payment->amount,
            ],
            default => [
                'label' => 'Bayar di Restoran (Langsung)',
                'amount' => (float) $payment->amount,
                'expires_at' => optional($payment->expires_at)->toDateTimeString(),
            ],
        };
    }
}