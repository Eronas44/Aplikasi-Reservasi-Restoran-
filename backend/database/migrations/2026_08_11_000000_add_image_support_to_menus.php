<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memastikan tabel menus memiliki kolom image_url untuk menampung
     * path gambar makanan yang diunggah dari halaman kelola menu.
     * Idempotent: kolom hanya ditambahkan bila belum ada.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'image_url')) {
                $table->string('image_url', 255)->nullable()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'image_url')) {
                $table->dropColumn('image_url');
            }
        });
    }
};
