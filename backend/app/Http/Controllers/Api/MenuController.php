<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min(max((int) request()->query('limit', 100), 1), 500);

        $query = Menu::query()->with(['category', 'restaurant']);

        // Filter per restoran. Menu dengan restaurant_id NULL dianggap
        // menu bersama sehingga tampil di semua restoran.
        if ($restaurantId = (int) request()->query('restaurant_id', 0)) {
            $query->where(function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId)
                    ->orWhereNull('restaurant_id');
            });
        }

        if ($categoryId = (int) request()->query('category_id', 0)) {
            $query->where('category_id', $categoryId);
        }

        if (request()->has('available')) {
            $query->where('is_available', filter_var(request()->query('available'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json([
            'data' => $query->orderBy('category_id')->orderBy('item_name')->paginate($perPage),
        ]);
    }

    public function store(StoreMenuRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['image']);

        if (empty($data['restaurant_id'])) {
            $data['restaurant_id'] = Restaurant::query()->value('restaurant_id');
        }

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $menu = Menu::query()->create($data);

        return response()->json([
            'message' => 'Menu created.',
            'data' => $menu->load(['category', 'restaurant']),
        ], 201);
    }

    public function show(int $menu): JsonResponse
    {
        return response()->json([
            'data' => Menu::query()->with(['category', 'restaurant'])->findOrFail($menu),
        ]);
    }

    public function update(UpdateMenuRequest $request, int $menu): JsonResponse
    {
        $model = Menu::query()->findOrFail($menu);
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $model->update($data);

        return response()->json([
            'message' => 'Menu updated.',
            'data' => $model->fresh()->load(['category', 'restaurant']),
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

    /**
     * Simpan file gambar makanan ke storage public dan kembalikan
     * path yang dapat diakses publik (contoh: /storage/menu_images/xxx.jpg).
     */
    private function storeImage(UploadedFile $file): string
    {
        $path = Storage::disk('public')->putFile('menu_images', $file);

        return '/media/' . $path;
    }
}
