<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
                ->withCount(['tables', 'menus'])
                ->with(['tables', 'openingHours', 'policies', 'paymentMethods'])
                ->findOrFail($restaurant),
        ]);
    }

    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['image'], $data['images']);

        // Kolom rating NOT NULL (default 0) — jangan kirim null eksplisit.
        if (array_key_exists('rating', $data) && $data['rating'] === null) {
            unset($data['rating']);
        }

        $imageUrls = $this->collectImageUrls($request);

        if (!empty($imageUrls)) {
            $data['image_urls'] = $imageUrls;
            // Gambar pertama dijadikan sampul agar kompatibel dengan tampilan lama.
            $data['image_url'] = $imageUrls[0];
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
        unset($data['image'], $data['images']);

        // Kolom rating NOT NULL (default 0) — jangan kirim null eksplisit.
        if (array_key_exists('rating', $data) && $data['rating'] === null) {
            unset($data['rating']);
        }

        $imageUrls = $this->collectImageUrls($request);

        if (!empty($imageUrls)) {
            $data['image_urls'] = $imageUrls;
            $data['image_url'] = $imageUrls[0];
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

        // Hapus file gambar (cover + daftar slideshow) agar tidak tersisa di storage.
        $paths = array_values(array_unique(array_filter(array_merge(
            [$model->image_url],
            (array) $model->image_urls,
        ))));

        foreach ($paths as $path) {
            $relative = ltrim(preg_replace('#^/?media/#', '', (string) $path), '/');
            if ($relative !== '') {
                Storage::disk('public')->delete($relative);
            }
        }

        $model->delete();

        return response()->json([
            'message' => 'Restaurant deleted.',
        ]);
    }

    /**
     * Kumpulkan semua file gambar (single field `image` DAN multi field
     * `images[]`) lalu simpan ke storage. Mengembalikan daftar path publik.
     */
    private function collectImageUrls(Request $request): array
    {
        $urls = [];

        if ($request->hasFile('image')) {
            $urls[] = $this->storeImage($request->file('image'));
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $urls[] = $this->storeImage($file);
            }
        }

        return array_values(array_unique(array_filter($urls)));
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
