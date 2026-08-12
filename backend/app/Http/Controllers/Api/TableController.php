<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;

class TableController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min(max((int) request()->query('limit', 20), 1), 500);

        $query = RestaurantTable::query()->with('restaurant');

        if ($restaurantId = (int) request()->query('restaurant_id', 0)) {
            $query->where('restaurant_id', $restaurantId);
        }
        if (in_array(request()->query('location_area'), ['indoor', 'outdoor', 'smoking', 'vip'], true)) {
            $query->where('location_area', request()->query('location_area'));
        }
        if (in_array(request()->query('status'), ['available', 'reserved', 'occupied', 'maintenance'], true)) {
            $query->where('status', request()->query('status'));
        }

        return response()->json([
            'data' => $query->orderBy('table_number')->orderBy('table_id')->paginate($perPage),
        ]);
    }

    /**
     * Cari meja yang tersedia untuk slot tanggal + jam tertentu (alur flowchart:
     * "Sistem Mencari Meja yang Tersedia -> Cek: Meja Tersedia?").
     *
     * Query params:
     *   - restaurant_id (opsional)
     *   - date          (Y-m-d)
     *   - time          (H:i)
     *   - guests        (jumlah tamu)
     *   - area          (indoor/outdoor/smoking/vip, opsional)
     *
     * Sebuah meja dianggap tersedia bila memenuhi kapasitas, bukan maintenance,
     * dan belum ada reservasi aktif (pending/confirmed/completed) di slot tsb.
     */
    public function available(): JsonResponse
    {
        $restaurantId = (int) request()->query('restaurant_id', 0);
        $date = (string) request()->query('date', '');
        $time = (string) request()->query('time', '');
        $guests = max((int) request()->query('guests', 1), 1);
        $area = (string) request()->query('area', '');

        $query = RestaurantTable::query();

        if ($restaurantId > 0) {
            $query->where('restaurant_id', $restaurantId);
        }

        if (in_array($area, ['indoor', 'outdoor', 'smoking', 'vip'], true)) {
            $query->where('location_area', $area);
        }

        $query->where('status', '!=', 'maintenance');
        $query->where('capacity', '>=', $guests);

        if ($date !== '' && $time !== '') {
            $time = date('H:i:s', strtotime($time));

            $busyTableIds = Reservation::query()
                ->where('reservation_date', $date)
                ->where('reservation_time', $time)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->pluck('table_id');

            if ($busyTableIds->isNotEmpty()) {
                $query->whereNotIn('table_id', $busyTableIds);
            }
        }

        $tables = $query->orderBy('table_number')->get();

        return response()->json([
            'data' => $tables,
        ]);
    }

    public function store(StoreTableRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Auto-assign restaurant_id ke restaurant pertama jika tidak dikirim dari frontend
        if (empty($data['restaurant_id'])) {
            $firstRestaurant = Restaurant::query()->first();
            if ($firstRestaurant) {
                $data['restaurant_id'] = $firstRestaurant->restaurant_id;
            }
        }

        // Meja baru tetap mendapat posisi yang stabil bila admin tidak
        // menentukan posisi secara eksplisit di form.
        if (empty($data['layout_row']) || empty($data['layout_column'])) {
            $lastPosition = RestaurantTable::query()
                ->where('restaurant_id', $data['restaurant_id'] ?? null)
                ->orderByDesc('layout_row')
                ->orderByDesc('layout_column')
                ->first(['layout_row', 'layout_column']);

            $nextIndex = $lastPosition
                ? (($lastPosition->layout_row - 1) * 4) + $lastPosition->layout_column + 1
                : 1;

            $data['layout_row'] ??= intdiv($nextIndex - 1, 4) + 1;
            $data['layout_column'] ??= (($nextIndex - 1) % 4) + 1;
        }

        $table = RestaurantTable::query()->create($data);

        return response()->json([
            'message' => 'Table created.',
            'data'    => $table,
        ], 201);
    }

    public function show(int $table): JsonResponse
    {
        return response()->json([
            'data' => RestaurantTable::query()->with('restaurant')->findOrFail($table),
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
