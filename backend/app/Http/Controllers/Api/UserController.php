<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = min((int) request()->query('limit', 100), 500);

        return response()->json([
            'data' => User::query()->orderBy('user_id')->paginate($perPage),
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::query()->create($request->validated());

        return response()->json([
            'message' => 'User created.',
            'data' => $user,
        ], 201);
    }

    public function show(int $user): JsonResponse
    {
        return response()->json([
            'data' => User::query()->findOrFail($user),
        ]);
    }

    public function update(UpdateUserRequest $request, int $user): JsonResponse
    {
        $model = User::query()->findOrFail($user);
        $model->update($request->validated());

        return response()->json([
            'message' => 'User updated.',
            'data' => $model->fresh(),
        ]);
    }

    public function destroy(int $user): JsonResponse
    {
        $model = User::query()->findOrFail($user);

        $blocked = $model->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($blocked) {
            return response()->json([
                'message' => 'User cannot be deleted because active reservations exist.',
            ], 409);
        }

        $model->delete();

        return response()->json([
            'message' => 'User deleted.',
        ]);
    }
}
