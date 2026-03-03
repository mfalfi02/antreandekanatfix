<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('services', 'est')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unsignedInteger('est')->default(10)->after('deskripsi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'est')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('est');
            });
        }
    }
};
