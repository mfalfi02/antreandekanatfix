<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruang_antri', function (Blueprint $table) {
            if (!Schema::hasColumn('ruang_antri', 'service_id')) {
                $table->foreignId('service_id')
                    ->nullable()
                    ->after('kode_dosen')
                    ->constrained('services')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ruang_antri', function (Blueprint $table) {
            if (Schema::hasColumn('ruang_antri', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }
        });
    }
};
