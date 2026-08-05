<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Menu::query()->with('category')->orderBy('menu_id')->paginate(20),
        ]);
    }

    public function store(StoreMenuRequest $request): JsonResponse
    {
        $menu = Menu::query()->create($request->validated());

        return response()->json([
            'message' => 'Menu created.',
            'data' => $menu->load('category'),
        ], 201);
    }

    public function show(int $menu): JsonResponse
    {
        return response()->json([
            'data' => Menu::query()->with('category')->findOrFail($menu),
        ]);
    }

    public function update(UpdateMenuRequest $request, int $menu): JsonResponse
    {
        $model = Menu::query()->findOrFail($menu);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Menu updated.',
            'data' => $model->fresh()->load('category'),
        ]);
    }

    public function destroy(int $menu): JsonResponse
    {
        $model = Menu::query()->findOrFail($menu);
        $model->delete();

        return response()->json([
            'message' => 'Menu deleted.',
        ]);
    }
}
