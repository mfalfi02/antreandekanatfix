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
     Schema::create('dosens', function (Blueprint $table) {
        $table->string('kode_dosen')->primary(); // Primary key
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('status')->default('Aktif'); // Aktif / Sibuk / Tidak Aktif
        $table->string('role'); // Dekan / Wakil Dekan / Lainnya
        $table->string('room')->nullable();
        $table->string('phone')->nullable();
    $table->timestamps();
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
