<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruang_antri', function (Blueprint $table) {
            if (!Schema::hasColumn('ruang_antri', 'status_ruang')) {
                $table->string('status_ruang', 20)->default('closed')->after('kode_dosen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ruang_antri', function (Blueprint $table) {
            if (Schema::hasColumn('ruang_antri', 'status_ruang')) {
                $table->dropColumn('status_ruang');
            }
        });
    }
};
