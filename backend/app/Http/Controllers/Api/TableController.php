<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => RestaurantTable::query()->orderBy('table_id')->paginate(20),
        ]);
    }

    public function store(StoreTableRequest $request): JsonResponse
    {
        $table = RestaurantTable::query()->create($request->validated());

        return response()->json([
            'message' => 'Table created.',
            'data' => $table,
        ], 201);
    }

    public function show(int $table): JsonResponse
    {
        return response()->json([
            'data' => RestaurantTable::query()->findOrFail($table),
        ]);
    }

    public function update(UpdateTableRequest $request, int $table): JsonResponse
    {
        $model = RestaurantTable::query()->findOrFail($table);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Table updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $table): JsonResponse
    {
        $model = RestaurantTable::query()->findOrFail($table);
        $model->delete();

        return response()->json([
            'message' => 'Table deleted.',
        ]);
    }
}
