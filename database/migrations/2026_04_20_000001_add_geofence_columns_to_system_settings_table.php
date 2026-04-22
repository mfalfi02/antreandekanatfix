<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tambahan kolom ini dipakai untuk membatasi aksi antrean berdasarkan lokasi fisik.
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('system_settings', 'center_latitude')) {
                $table->decimal('center_latitude', 10, 7)->nullable()->after('queue_status');
            }
            if (!Schema::hasColumn('system_settings', 'center_longitude')) {
                $table->decimal('center_longitude', 10, 7)->nullable()->after('center_latitude');
            }
            if (!Schema::hasColumn('system_settings', 'radius_meters')) {
                $table->unsignedInteger('radius_meters')->default(300)->after('center_longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            if (Schema::hasColumn('system_settings', 'center_latitude')) {
                $table->dropColumn('center_latitude');
            }
            if (Schema::hasColumn('system_settings', 'center_longitude')) {
                $table->dropColumn('center_longitude');
            }
            if (Schema::hasColumn('system_settings', 'radius_meters')) {
                $table->dropColumn('radius_meters');
            }
        });
    }
};
