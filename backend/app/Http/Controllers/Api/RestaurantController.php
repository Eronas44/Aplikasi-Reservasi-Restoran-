<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Restaurant::query()->withCount(['tables', 'menus']);

        // Admin butuh melihat semua restoran (termasuk yang nonaktif).
        if (filter_var(request()->query('all', false), FILTER_VALIDATE_BOOLEAN)) {
            $query->orderBy('name');
        } else {
            $query->where('is_active', true)->orderBy('name');
        }

        return response()->json([
            'data' => $query->get(),
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
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $restaurant = Restaurant::query()->create($data);

        return response()->json([
            'message' => 'Restaurant created.',
            'data' => $restaurant,
        ], 201);
    }

    public function update(StoreRestaurantRequest $request, int $restaurant): JsonResponse
    {
        $model = Restaurant::query()->findOrFail($restaurant);
        $data = $request->validated();
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $model->update($data);

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

    /**
     * Simpan file gambar restoran ke storage public dan kembalikan
     * path yang dapat diakses publik (contoh: /media/restaurant_images/xxx.jpg).
     */
    private function storeImage(UploadedFile $file): string
    {
        $path = Storage::disk('public')->putFile('restaurant_images', $file);

        return '/media/' . $path;
    }
}
