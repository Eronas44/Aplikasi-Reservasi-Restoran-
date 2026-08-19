<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHolidayRequest;
use App\Models\Holiday;
use Illuminate\Http\JsonResponse;

class HolidayController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Holiday::query()
            ->with(['restaurant'])
            ->orderByDesc('holiday_date');

        if ($restoId = (int) request()->query('restaurant_id', 0)) {
            $query->where('restaurant_id', $restoId);
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(StoreHolidayRequest $request): JsonResponse
    {
        $holiday = Holiday::query()->create($request->validated());

        return response()->json([
            'message' => 'Hari libur khusus berhasil ditambahkan.',
            'data' => $holiday->load('restaurant'),
        ], 201);
    }

    public function show(int $holiday): JsonResponse
    {
        return response()->json([
            'data' => Holiday::query()->with('restaurant')->findOrFail($holiday),
        ]);
    }

    public function update(StoreHolidayRequest $request, int $holiday): JsonResponse
    {
        $model = Holiday::query()->findOrFail($holiday);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Hari libur khusus diperbarui.',
            'data' => $model->fresh()->load('restaurant'),
        ]);
    }

    public function destroy(int $holiday): JsonResponse
    {
        $model = Holiday::query()->findOrFail($holiday);
        $model->delete();

        return response()->json([
            'message' => 'Hari libur khusus dihapus.',
        ]);
    }
}