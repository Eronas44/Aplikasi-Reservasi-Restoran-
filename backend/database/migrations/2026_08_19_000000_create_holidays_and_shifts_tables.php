<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ============================================================
     * TABEL PENDUKUNG JAM OPERASIONAL LANJUTAN
     * ============================================================
     * Menambahkan:
     *   - holidays  (hari libur khusus per restoran)
     *   - shifts    (shift pegawai per restoran)
     */
    public function up(): void
    {
        // 1. HARI LIBUR KHUSUS (tanggal tertentu, mis. tanggal merah / hari besar)
        Schema::create('holidays', function (Blueprint $table) {
            $table->increments('holiday_id');
            $table->unsignedInteger('restaurant_id');
            $table->string('name', 150);
            $table->date('holiday_date');
            $table->boolean('is_closed')->default(true); // true = tutup penuh, false = buka (beda jam)

            $table->foreign('restaurant_id')
                ->references('restaurant_id')
                ->on('restaurants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->index(['restaurant_id', 'holiday_date'], 'ix_holidays_resto_date');
        });

        // 2. SHIFT PEGAWAI (day_of_week null = setiap hari / daily)
        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('shift_id');
            $table->unsignedInteger('restaurant_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->tinyInteger('day_of_week')->nullable(); // 0=Minggu .. 6=Sabtu, null=setiap hari
            $table->string('shift_name', 50);
            $table->time('start_time');
            $table->time('end_time');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('restaurant_id')
                ->references('restaurant_id')
                ->on('restaurants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('holidays');
    }
};
