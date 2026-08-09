<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePolicyRequest;
use App\Models\Policy;
use Illuminate\Http\JsonResponse;

class PolicyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Policy::query()->orderByDesc('is_active')->get(),
        ]);
    }

    public function show(int $policy): JsonResponse
    {
        return response()->json([
            'data' => Policy::query()->findOrFail($policy),
        ]);
    }

    public function store(StorePolicyRequest $request): JsonResponse
    {
        $policy = Policy::query()->create($request->validated());

        return response()->json([
            'message' => 'Policy created.',
            'data' => $policy,
        ], 201);
    }

    public function update(StorePolicyRequest $request, int $policy): JsonResponse
    {
        $model = Policy::query()->findOrFail($policy);
        $model->update($request->validated());

        return response()->json([
            'message' => 'Policy updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $policy): JsonResponse
    {
        $model = Policy::query()->findOrFail($policy);
        $model->delete();

        return response()->json([
            'message' => 'Policy deleted.',
        ]);
    }
}
