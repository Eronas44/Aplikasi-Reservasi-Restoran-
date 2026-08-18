<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ============================================================
     * PEMBAYARAN - SIMULASI + SIAP GATEWAY
     * ============================================================
     *  1. payments  : tambah kolom VA / phone / detail / expiry agar
     *     alur pembayaran tiap metode (BCA, E-Wallet, QRIS, Bayar di
     *     Restoran) tercatat dan status-nya dapat diverifikasi backend.
     *  2. restaurant_payment_methods : konfigurasi merchant per cabang
     *     (rekening BCA / VA, nomor e-wallet, gambar QRIS) sehingga tiap
     *     restoran bisa punya rekening & QRIS sendiri.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('va_number', 50)->nullable()->after('gateway');
            $table->string('phone_number', 30)->nullable()->after('va_number');
            $table->json('payment_details')->nullable()->after('phone_number');
            $table->dateTime('expires_at')->nullable()->after('payment_details');
        });

        Schema::create('restaurant_payment_methods', function (Blueprint $table) {
            $table->increments('payment_method_id');
            $table->unsignedInteger('restaurant_id');
            $table->enum('method', ['bank_transfer', 'ewallet', 'qris']);
            $table->string('label', 100)->default('Pembayaran');
            $table->string('account_name', 150)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('qris_image', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('restaurant_id')
                ->references('restaurant_id')
                ->on('restaurants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unique(['restaurant_id', 'method'], 'ux_resto_payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_payment_methods');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['va_number', 'phone_number', 'payment_details', 'expires_at']);
        });
    }
};
