<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ============================================================
     * TABEL TAMBAHAN - AGAR WEB RESERVASI LENGKAP
     * ============================================================
     * Menambahkan tabel pendukung fitur flowchart:
     *   - restaurants   (multi-restoran: Resto A/B/C/D)
     *   - menambahkan restaurant_id ke tables & menus
     *   - opening_hours (jam operasional per hari - FR-015)
     *   - policies      (kebijakan deposit & refund - FR-007/FR-014)
     *   - payments      (transaksi deposit / pelunasan / refund)
     *   - notifications (notifikasi Email / WhatsApp)
     *   - waiting_list  (walk-in / waiting list)
     *   - table_status_logs (riwayat perubahan status meja)
     */
    public function up(): void
    {
        // 1. TABEL RESTORAN (multi-cabang)
        Schema::create('restaurants', function (Blueprint $table) {
            $table->increments('restaurant_id');
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('address', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // 2. HUBUNGKAN MEJA & MENU KE RESTORAN
        if (!Schema::hasColumn('tables', 'restaurant_id')) {
            Schema::table('tables', function (Blueprint $table) {
                $table->unsignedInteger('restaurant_id')->nullable()->after('table_number');
                $table->foreign('restaurant_id')
                    ->references('restaurant_id')
                    ->on('restaurants')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        if (!Schema::hasColumn('menus', 'restaurant_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->unsignedInteger('restaurant_id')->nullable()->after('category_id');
                $table->foreign('restaurant_id')
                    ->references('restaurant_id')
                    ->on('restaurants')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        // 3. JAM OPERASIONAL PER HARI (FR-015 & FR-004)
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->increments('opening_hour_id');
            $table->unsignedInteger('restaurant_id');
            $table->tinyInteger('day_of_week'); // 0=Minggu .. 6=Sabtu
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_closed')->default(false);

            $table->foreign('restaurant_id')
                ->references('restaurant_id')
                ->on('restaurants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unique(['restaurant_id', 'day_of_week'], 'ux_opening_hours_day');
        });

        // 4. KEBIJAKAN DEPOSIT & REFUND (FR-007 & FR-014)
        Schema::create('policies', function (Blueprint $table) {
            $table->increments('policy_id');
            $table->unsignedInteger('restaurant_id');
            $table->decimal('deposit_percent', 5, 2)->default(20);   // % dari total
            $table->decimal('deposit_min_amount', 10, 2)->default(0); // nominal minimum
            $table->integer('refund_full_hours')->default(24);   // >= 24 jam -> 100%
            $table->integer('refund_partial_hours')->default(6); // 6-24 jam -> 50%
            $table->decimal('refund_partial_percent', 5, 2)->default(50);
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('restaurant_id')
                ->references('restaurant_id')
                ->on('restaurants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        // 5. TRANSAKSI PEMBAYARAN (deposit / pelunasan / refund)
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('payment_id');
            $table->unsignedInteger('reservation_id');
            $table->enum('type', ['deposit', 'settlement', 'refund']);
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['bank_transfer', 'ewallet', 'qris', 'cash', 'card']);
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_code', 100)->nullable()->unique();
            $table->string('gateway', 50)->nullable(); // midtrans / xendit / dll
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('reservation_id')
                ->references('reservation_id')
                ->on('reservations')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->index(['reservation_id', 'type']);
        });

        // 6. NOTIFIKASI (Email / WhatsApp / In-App)
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('notification_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('reservation_id')->nullable();
            $table->enum('channel', ['email', 'whatsapp', 'in_app']);
            $table->string('subject', 200)->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('reservation_id')
                ->references('reservation_id')
                ->on('reservations')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        // 7. WAITING LIST / WALK-IN
        Schema::create('waiting_list', function (Blueprint $table) {
            $table->increments('waiting_id');
            $table->unsignedInteger('restaurant_id');
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->integer('number_of_guest');
            $table->enum('area', ['indoor', 'outdoor', 'smoking', 'vip'])->default('indoor');
            $table->enum('status', ['waiting', 'seated', 'cancelled'])->default('waiting');
            $table->unsignedInteger('assigned_table_id')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('seated_at')->nullable();

            $table->foreign('restaurant_id')
                ->references('restaurant_id')
                ->on('restaurants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('assigned_table_id')
                ->references('table_id')
                ->on('tables')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        // 8. RIWAYAT PERUBAHAN STATUS MEJA
        Schema::create('table_status_logs', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('table_id');
            $table->enum('old_status', ['available', 'reserved', 'occupied', 'maintenance']);
            $table->enum('new_status', ['available', 'reserved', 'occupied', 'maintenance']);
            $table->unsignedInteger('changed_by')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('table_id')
                ->references('table_id')
                ->on('tables')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('changed_by')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_status_logs');
        Schema::dropIfExists('waiting_list');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('opening_hours');

        if (Schema::hasColumn('menus', 'restaurant_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropForeign(['restaurant_id']);
                $table->dropColumn('restaurant_id');
            });
        }

        if (Schema::hasColumn('tables', 'restaurant_id')) {
            Schema::table('tables', function (Blueprint $table) {
                $table->dropForeign(['restaurant_id']);
                $table->dropColumn('restaurant_id');
            });
        }

        Schema::dropIfExists('restaurants');
    }
};
