<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Snapshot persis database saat ini (menggantikan pembuatan data demo).
        // Seeder legacy (RestaurantReservationSeeder / RestaurantPaymentMethodSeeder)
        // tetap tersedia dan bisa dijalankan manual via --class jika dibutuhkan.
        $this->call([
            DatabaseDumpSeeder::class,
        ]);
    }
}
