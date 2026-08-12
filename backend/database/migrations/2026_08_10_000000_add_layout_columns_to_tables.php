<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Posisi denah disimpan per meja, sehingga urutan hasil query tidak lagi
     * menentukan letak meja pada halaman denah.
     */
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table): void {
            if (! Schema::hasColumn('tables', 'layout_row')) {
                $table->unsignedSmallInteger('layout_row')->nullable()->after('status');
            }

            if (! Schema::hasColumn('tables', 'layout_column')) {
                $table->unsignedSmallInteger('layout_column')->nullable()->after('layout_row');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table): void {
            $columns = [];

            if (Schema::hasColumn('tables', 'layout_column')) {
                $columns[] = 'layout_column';
            }

            if (Schema::hasColumn('tables', 'layout_row')) {
                $columns[] = 'layout_row';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
