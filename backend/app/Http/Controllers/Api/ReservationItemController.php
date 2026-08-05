<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationItemRequest;
use App\Http\Requests\UpdateReservationItemRequest;
use App\Models\ReservationItem;
use Illuminate\Http\JsonResponse;

class ReservationItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ReservationItem::query()
                ->with(['reservation', 'menu'])
                ->orderBy('reservation_item_id')
                ->paginate(20),
        ]);
    }

    public function store(StoreReservationItemRequest $request): JsonResponse
    {
        $item = ReservationItem::query()->create($request->validated());

        return response()->json([
            'message' => 'Reservation item created.',
            'data' => $item->load(['reservation', 'menu']),
        ], 201);
    }

    public function show(int $reservationItem): JsonResponse
    {
        return response()->json([
            'data' => ReservationItem::query()
                ->with(['reservation', 'menu'])
                ->findOrFail($reservationItem),
        ]);
    }

    public function update(UpdateReservationItemRequest $request, int $reservationItem): JsonResponse
    {
        $model = ReservationItem::query()->findOrFail($reservationItem);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Reservation item updated.',
            'data' => $model->fresh()->load(['reservation', 'menu']),
        ]);
    }

    public function destroy(int $reservationItem): JsonResponse
    {
        $model = ReservationItem::query()->findOrFail($reservationItem);
        $model->delete();

        return response()->json([
            'message' => 'Reservation item deleted.',
        ]);
    }
}
