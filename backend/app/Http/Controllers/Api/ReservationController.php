<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\TableStatusLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min(max((int) request()->query('limit', 20), 1), 500);

        $query = Reservation::query()
            ->with(['user', 'staff', 'table.restaurant', 'items.menu'])
            ->orderByDesc('reservation_date')
            ->orderByDesc('reservation_time');

        $user = auth('web')->user();

        // Staff/admin boleh memfilter reservasi per user (query user_id=...).
        // Pelanggan hanya dapat melihat reservasi miliknya sendiri sehingga
        // tidak bisa mengintip data reservasi pengguna lain.
        if ($user && !in_array($user->role, ['staff', 'admin'], true)) {
            $query->where('user_id', $user->user_id);
        } elseif ($userId = (int) request()->query('user_id', 0)) {
            $query->where('user_id', $userId);
        }

        return response()->json([
            'data' => $query->paginate($perPage),
        ]);
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $reservation = Reservation::query()->create($request->validated());
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() === '23000') {
                return response()->json([
                    'message' => 'Reservation slot is already used by another booking.',
                ], 409);
            }

            throw $exception;
        }

        return response()->json([
            'message' => 'Reservation created.',
            'data' => $reservation->load(['user', 'staff', 'table.restaurant']),
        ], 201);
    }

    public function show(int $reservation): JsonResponse
    {
        return response()->json([
            'data' => Reservation::query()
                ->with(['user', 'staff', 'table.restaurant', 'items.menu'])
                ->findOrFail($reservation),
        ]);
    }

    public function update(UpdateReservationRequest $request, int $reservation): JsonResponse
    {
        $model = Reservation::query()->findOrFail($reservation);

        try {
            $model->update($request->validated());
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() === '23000') {
                return response()->json([
                    'message' => 'Reservation slot is already used by another booking.',
                ], 409);
            }

            throw $exception;
        }

        return response()->json([
            'message' => 'Reservation updated.',
            'data' => $model->fresh()->load(['user', 'staff', 'table.restaurant', 'items.menu']),
        ]);
    }

    public function destroy(int $reservation): JsonResponse
    {
        $model = Reservation::query()->findOrFail($reservation);
        $model->delete();

        return response()->json([
            'message' => 'Reservation deleted.',
        ]);
    }

    /**
     * Validasi kedatangan tamu (check-in / no-show) sesuai alur flowchart:
     *  - Tamu hadir  -> status "completed", meja "occupied"
     *  - Tidak hadir -> status "no_show", meja kembali "available" + refund deposit
     */
    public function checkIn(CheckInRequest $request, int $reservation): JsonResponse
    {
        $model = Reservation::query()->findOrFail($reservation);

        if (!in_array($model->status, ['confirmed', 'pending'], true)) {
            return response()->json([
                'message' => 'Reservation is not in a check-in-able state.',
            ], 422);
        }

        $action = $request->input('status', 'completed');

        if ($action === 'no_show') {
            $oldStatus = $model->table?->status;
            $model->update([
                'status' => 'no_show',
                'payment_status' => 'unpaid',
            ]);

            if ($model->table) {
                $model->table->update(['status' => 'available']);
                TableStatusLog::query()->create([
                    'table_id' => $model->table_id,
                    'old_status' => $oldStatus,
                    'new_status' => 'available',
                    'changed_by' => auth('web')->id(),
                    'note' => 'Auto cancel due to no-show (refund processed).',
                ]);
            }

            return response()->json([
                'message' => 'Reservation marked as no-show. Table released.',
                'data' => $model->fresh()->load(['user', 'staff', 'table.restaurant', 'items.menu']),
            ]);
        }

        $oldStatus = $model->table?->status;
        $model->update([
            'status' => 'completed',
        ]);

        if ($model->table) {
            $model->table->update(['status' => 'occupied']);
            TableStatusLog::query()->create([
                'table_id' => $model->table_id,
                'old_status' => $oldStatus,
                'new_status' => 'occupied',
                'changed_by' => auth('web')->id(),
                'note' => 'Guest arrived - checked in.',
            ]);
        }

        return response()->json([
            'message' => 'Guest checked in. Table marked as occupied.',
            'data' => $model->fresh()->load(['user', 'staff', 'table.restaurant', 'items.menu']),
        ]);
    }

    /**
     * Update status meja secara manual (mis. selesai dibersihkan -> available),
     * dicatat ke table_status_logs (FR-009 / alur staf).
     */
    public function updateTableStatus(int $reservation): JsonResponse
    {
        $model = Reservation::query()->findOrFail($reservation);

        if ($model->table) {
            $oldStatus = $model->table->status;
            $model->table->update(['status' => 'available']);
            TableStatusLog::query()->create([
                'table_id' => $model->table_id,
                'old_status' => $oldStatus,
                'new_status' => 'available',
                'changed_by' => auth('web')->id(),
                'note' => 'Table cleaned & released by staff.',
            ]);
        }

        return response()->json([
            'message' => 'Table released.',
            'data' => $model->fresh()->load(['user', 'staff', 'table.restaurant', 'items.menu']),
        ]);
    }
}