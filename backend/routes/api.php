<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OpeningHourController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReservationItemController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WaitingListController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['web', 'throttle:api'])
    ->group(function (): void {
        Route::post('/auth/login', [AuthController::class, 'login'])->middleware('guest');
        Route::post('/auth/register', [AuthController::class, 'register'])->middleware('guest');

        // Restoran publik (list & detail) tanpa harus login
        Route::get('/restaurants', [RestaurantController::class, 'index']);
        Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show']);

        // Webhook callback gateway pembayaran (publik, dilindungi token
        // X-Payment-Token = PAYMENT_WEBHOOK_TOKEN). Dinonaktifkan bila token kosong.
        Route::post('/payments/{payment}/callback', [PaymentController::class, 'callback']);

        Route::middleware('auth')->group(function (): void {
            Route::get('/auth/me', [AuthController::class, 'me']);
            Route::post('/auth/logout', [AuthController::class, 'logout']);

            Route::apiResource('categories', CategoryController::class)
                ->only(['index', 'show']);

            Route::apiResource('menus', MenuController::class)
                ->only(['index', 'show']);

            // Cari meja tersedia utk slot (tanggal + jam) tertentu - alur flowchart
            Route::get('/tables/available', [TableController::class, 'available']);

            Route::apiResource('tables', TableController::class)
                ->only(['index', 'show']);

            // Pelanggan dapat mencatat pembayaran deposit/pre-order sendiri
            Route::post('/payments', [PaymentController::class, 'store']);
            // Instruksi pembayaran & verifikasi (simulasi) untuk pelanggan
            Route::get('/payments/{payment}/instructions', [PaymentController::class, 'instructions']);
            Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify']);
            // Ubah metode pembayaran yang masih pending (re-submit dari Review)
            Route::put('/payments/{payment}', [PaymentController::class, 'update']);

            Route::apiResource('reservations', ReservationController::class)
                ->except(['destroy']);

            Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn']);
            Route::post('/reservations/{reservation}/release-table', [ReservationController::class, 'updateTableStatus']);

            Route::apiResource('reservation-items', ReservationItemController::class)
                ->except(['destroy'])
                ->parameters(['reservation-items' => 'reservationItem']);
        });

        Route::middleware('auth', 'role:staff,admin')->group(function (): void {
            Route::apiResource('categories', CategoryController::class)
                ->only(['store', 'update', 'destroy']);

            Route::apiResource('menus', MenuController::class)
                ->only(['store', 'update', 'destroy']);

            Route::apiResource('tables', TableController::class)
                ->only(['store', 'update', 'destroy']);

            Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
            Route::delete('/reservation-items/{reservationItem}', [ReservationItemController::class, 'destroy']);

            Route::get('/payments', [PaymentController::class, 'index']);
            Route::get('/payments/{payment}', [PaymentController::class, 'show']);
            Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);

            Route::apiResource('waiting-list', WaitingListController::class)
                ->only(['index', 'store', 'update', 'destroy']);

            Route::apiResource('opening-hours', OpeningHourController::class)
                ->except(['index', 'show']);

            Route::get('/opening-hours', [OpeningHourController::class, 'index']);
            Route::get('/opening-hours/{openingHour}', [OpeningHourController::class, 'show']);

            Route::apiResource('policies', PolicyController::class);
        });

        Route::middleware('auth', 'role:admin')->group(function (): void {
            Route::apiResource('users', UserController::class);

            Route::apiResource('restaurants', RestaurantController::class)
                ->except(['index', 'show']);
        });
    });
