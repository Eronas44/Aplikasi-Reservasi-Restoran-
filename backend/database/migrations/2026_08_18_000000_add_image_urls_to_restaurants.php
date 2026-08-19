<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom image_urls (JSON) pada tabel restaurants agar sebuah
     * restoran dapat menyimpan BANYAK gambar untuk slideshow. Kolom image_url
     * lama tetap dipertahankan sebagai gambar sampul (cover) utama.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('restaurants', 'image_urls')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->json('image_urls')->nullable()->after('image_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('restaurants', 'image_urls')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn('image_urls');
            });
        }
    }
};
