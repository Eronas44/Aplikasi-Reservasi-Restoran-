<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseDumpSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Import snapshot data yang persis dengan kondisi database produksi
     * saat ini (data-only dump dari mysqldump).
     *
     * File dibuat ulang dengan:
     *   mysqldump --no-create-info --skip-triggers --complete-insert \
     *     --skip-lock-tables --skip-add-locks --single-transaction \
     *     --set-gtid-purged=OFF --default-character-set=utf8mb4 \
     *     reservasi categories menus opening_hours policies payments \
     *     reservation_items reservations restaurant_payment_methods \
     *     restaurants tables table_status_logs users waiting_list
     */
    public function run(): void
    {
        $sql = File::get(database_path('data/app_data.sql'));

        DB::unprepared($sql);
    }
}