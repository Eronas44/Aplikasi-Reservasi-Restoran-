<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;

class RestaurantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Restaurant::query()
                ->withCount(['tables'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(int $restaurant): JsonResponse
    {
        return response()->json([
            'data' => Restaurant::query()
                ->with(['tables', 'openingHours', 'policies'])
                ->findOrFail($restaurant),
        ]);
    }

    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $restaurant = Restaurant::query()->create($request->validated());

        return response()->json([
            'message' => 'Restaurant created.',
            'data' => $restaurant,
        ], 201);
    }

    public function update(StoreRestaurantRequest $request, int $restaurant): JsonResponse
    {
        $model = Restaurant::query()->findOrFail($restaurant);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Restaurant updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $restaurant): JsonResponse
    {
        $model = Restaurant::query()->findOrFail($restaurant);
        $model->delete();

        return response()->json([
            'message' => 'Restaurant deleted.',
        ]);
    }
}
