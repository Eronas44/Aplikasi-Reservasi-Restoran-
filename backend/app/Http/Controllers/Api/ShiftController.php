<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShiftRequest;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Shift::query()
            ->with(['restaurant', 'user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if ($restoId = (int) request()->query('restaurant_id', 0)) {
            $query->where('restaurant_id', $restoId);
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(StoreShiftRequest $request): JsonResponse
    {
        $shift = Shift::query()->create($request->validated());

        return response()->json([
            'message' => 'Shift pegawai berhasil ditambahkan.',
            'data' => $shift->load(['restaurant', 'user']),
        ], 201);
    }

    public function show(int $shift): JsonResponse
    {
        return response()->json([
            'data' => Shift::query()->with(['restaurant', 'user'])->findOrFail($shift),
        ]);
    }

    public function update(StoreShiftRequest $request, int $shift): JsonResponse
    {
        $model = Shift::query()->findOrFail($shift);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Shift pegawai diperbarui.',
            'data' => $model->fresh()->load(['restaurant', 'user']),
        ]);
    }

    public function destroy(int $shift): JsonResponse
    {
        $model = Shift::query()->findOrFail($shift);
        $model->delete();

        return response()->json([
            'message' => 'Shift pegawai dihapus.',
        ]);
    }
}