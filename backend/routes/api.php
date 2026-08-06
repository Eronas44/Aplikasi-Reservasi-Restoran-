<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReservationItemController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['web', 'throttle:api'])
    ->group(function (): void {
        Route::post('/auth/login', [AuthController::class, 'login'])->middleware('guest');
        Route::post('/auth/register', [AuthController::class, 'register'])->middleware('guest');

        Route::middleware('auth')->group(function (): void {
            Route::get('/auth/me', [AuthController::class, 'me']);
            Route::post('/auth/logout', [AuthController::class, 'logout']);

            Route::apiResource('categories', CategoryController::class)
                ->only(['index', 'show']);

            Route::apiResource('menus', MenuController::class)
                ->only(['index', 'show']);

            Route::apiResource('tables', TableController::class)
                ->only(['index', 'show']);

            Route::apiResource('reservations', ReservationController::class)
                ->except(['destroy']);

            Route::apiResource('reservation-items', ReservationItemController::class)
                ->except(['destroy'])
                ->parameters(['reservation-items' => 'reservationItem']);

            Route::middleware('role:staff,admin')->group(function (): void {
                Route::apiResource('categories', CategoryController::class)
                    ->only(['store', 'update', 'destroy']);

                Route::apiResource('menus', MenuController::class)
                    ->only(['store', 'update', 'destroy']);

                Route::apiResource('tables', TableController::class)
                    ->only(['store', 'update', 'destroy']);

                Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
                Route::delete('/reservation-items/{reservationItem}', [ReservationItemController::class, 'destroy']);
            });

            Route::middleware('role:admin')->group(function (): void {
                Route::apiResource('users', UserController::class);
            });
        });
    });
