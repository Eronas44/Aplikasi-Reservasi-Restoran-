<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function index(): JsonResponse
    {
        $reservations = Reservation::query()
            ->with(['user', 'staff', 'table', 'items.menu'])
            ->orderByDesc('reservation_date')
            ->orderByDesc('reservation_time')
            ->paginate(20);

        return response()->json([
            'data' => $reservations,
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
            'data' => $reservation->load(['user', 'staff', 'table']),
        ], 201);
    }

    public function show(int $reservation): JsonResponse
    {
        return response()->json([
            'data' => Reservation::query()
                ->with(['user', 'staff', 'table', 'items.menu'])
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
            'data' => $model->fresh()->load(['user', 'staff', 'table', 'items.menu']),
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
}
