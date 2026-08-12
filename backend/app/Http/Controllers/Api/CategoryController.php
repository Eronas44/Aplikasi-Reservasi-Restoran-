<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min(max((int) request()->query('limit', 20), 1), 500);

        return response()->json([
            // Data terbaru perlu terlihat langsung di tabel pengelolaan.
            'data' => Category::query()->orderByDesc('category_id')->paginate($perPage),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::query()->create($request->validated());

        return response()->json([
            'message' => 'Category created.',
            'data' => $category,
        ], 201);
    }

    public function show(int $category): JsonResponse
    {
        return response()->json([
            'data' => Category::query()->findOrFail($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, int $category): JsonResponse
    {
        $model = Category::query()->findOrFail($category);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Category updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $category): JsonResponse
    {
        $model = Category::query()->findOrFail($category);
        $model->delete();

        return response()->json([
            'message' => 'Category deleted.',
        ]);
    }
}
