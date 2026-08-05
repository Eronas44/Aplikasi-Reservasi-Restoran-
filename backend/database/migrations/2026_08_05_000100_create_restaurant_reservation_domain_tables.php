<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->increments('table_id');
            $table->string('table_number', 50);
            $table->integer('capacity');
            $table->enum('location_area', ['indoor', 'outdoor', 'smoking', 'vip']);
            $table->enum('status', ['available', 'reserved', 'occupied', 'maintenance'])->default('available');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('category_id');
            $table->string('category_name', 100);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->increments('menu_id');
            $table->unsignedInteger('category_id');
            $table->string('item_name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_available')->default(true);

            $table->foreign('category_id')
                ->references('category_id')
                ->on('categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->increments('reservation_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('table_id');
            $table->string('booking_code', 50)->unique();
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('number_of_guest');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->unsignedInteger('staff_id')->nullable();
            $table->text('special_request')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['reservation_date', 'reservation_time']);
            $table->unique(['table_id', 'reservation_date', 'reservation_time'], 'ux_table_slot');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('table_id')
                ->references('table_id')
                ->on('tables')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('staff_id')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::create('reservation_items', function (Blueprint $table) {
            $table->increments('reservation_item_id');
            $table->unsignedInteger('reservation_id');
            $table->unsignedInteger('menu_id');
            $table->integer('quantity');
            $table->decimal('subtotal_price', 10, 2);

            $table->foreign('reservation_id')
                ->references('reservation_id')
                ->on('reservations')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('menu_id')
                ->references('menu_id')
                ->on('menus')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tables');
    }
};
