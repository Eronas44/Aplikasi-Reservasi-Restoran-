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
        return response()->json([
            'data' => Category::query()->orderBy('category_id')->paginate(20),
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
