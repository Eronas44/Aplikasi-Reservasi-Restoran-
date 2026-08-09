<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpeningHourRequest;
use App\Models\OpeningHour;
use Illuminate\Http\JsonResponse;

class OpeningHourController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => OpeningHour::query()
                ->orderBy('day_of_week')
                ->get(),
        ]);
    }

    public function show(int $openingHour): JsonResponse
    {
        return response()->json([
            'data' => OpeningHour::query()->findOrFail($openingHour),
        ]);
    }

    public function store(StoreOpeningHourRequest $request): JsonResponse
    {
        $openingHour = OpeningHour::query()->create($request->validated());

        return response()->json([
            'message' => 'Opening hour created.',
            'data' => $openingHour,
        ], 201);
    }

    public function update(StoreOpeningHourRequest $request, int $openingHour): JsonResponse
    {
        $model = OpeningHour::query()->findOrFail($openingHour);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Opening hour updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $openingHour): JsonResponse
    {
        $model = OpeningHour::query()->findOrFail($openingHour);
        $model->delete();

        return response()->json([
            'message' => 'Opening hour deleted.',
        ]);
    }
}
