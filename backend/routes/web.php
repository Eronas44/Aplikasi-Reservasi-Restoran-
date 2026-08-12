<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Restaurant Reservation API is running.',
    ]);
});

// Sajikan gambar yang diunggah (menu_images, dll.) dari storage publik
// tanpa bergantung pada symlink public/storage (bind mount Windows).
// Memakai prefix /media agar tidak bersinggungan dengan route default
// framework (storage.local) yang sudah mengambil alih /storage/{path}.
Route::get('/media/{path}', function (string $path) {
    $base = realpath(storage_path('app/public'));
    $full = realpath(storage_path('app/public/' . $path));

    if ($base === false || $full === false || ! str_starts_with($full, $base)) {
        abort(404);
    }

    if (! is_file($full)) {
        abort(404);
    }

    return response()->file($full);
})->where('path', '.*');

Route::prefix('frontend')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', fn () => response()->json(['route' => 'frontend.login']));
        Route::get('/register', fn () => response()->json(['route' => 'frontend.register']));
    });

    Route::middleware('auth')->group(function (): void {
        Route::get('/dashboard', fn () => response()->json(['route' => 'frontend.dashboard']));
        Route::get('/reservations', fn () => response()->json(['route' => 'frontend.reservations']));
        Route::get('/menu', fn () => response()->json(['route' => 'frontend.menu']));

        Route::middleware('role:staff,admin')->group(function (): void {
            Route::get('/staff/check-in', fn () => response()->json(['route' => 'frontend.staff.check-in']));
            Route::get('/staff/reservation-monitor', fn () => response()->json(['route' => 'frontend.staff.reservation-monitor']));
        });

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/admin/users', fn () => response()->json(['route' => 'frontend.admin.users']));
            Route::get('/admin/tables', fn () => response()->json(['route' => 'frontend.admin.tables']));
            Route::get('/admin/menu-management', fn () => response()->json(['route' => 'frontend.admin.menu-management']));
            Route::get('/admin/reports', fn () => response()->json(['route' => 'frontend.admin.reports']));
        });
    });
});
