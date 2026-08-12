<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWaitingListRequest;
use App\Models\WaitingList;
use Illuminate\Http\JsonResponse;

class WaitingListController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => WaitingList::query()
                ->orderByRaw("CASE status WHEN 'waiting' THEN 0 WHEN 'seated' THEN 1 ELSE 2 END")
                ->orderBy('created_at')
                ->get(),
        ]);
    }

    public function store(StoreWaitingListRequest $request): JsonResponse
    {
        $waiting = WaitingList::query()->create($request->validated());

        return response()->json([
            'message' => 'Added to waiting list.',
            'data' => $waiting,
        ], 201);
    }

    public function update(StoreWaitingListRequest $request, int $waiting): JsonResponse
    {
        $model = WaitingList::query()->findOrFail($waiting);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Waiting list updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $waiting): JsonResponse
    {
        $model = WaitingList::query()->findOrFail($waiting);
        $model->delete();

        return response()->json([
            'message' => 'Waiting list entry removed.',
        ]);
    }
}
