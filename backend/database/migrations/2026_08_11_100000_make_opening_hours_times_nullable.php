<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jam operasional untuk hari tutup/libur dikirim tanpa open_time & close_time
     * (null). Kolom harus nullable agar penyimpanan tidak memicu NOT NULL
     * constraint (SQLSTATE 23000) di database SQLite.
     */
    public function up(): void
    {
        Schema::table('opening_hours', function (Blueprint $table) {
            $table->time('open_time')->nullable()->change();
            $table->time('close_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('opening_hours', function (Blueprint $table) {
            $table->time('open_time')->nullable(false)->change();
            $table->time('close_time')->nullable(false)->change();
        });
    }
};