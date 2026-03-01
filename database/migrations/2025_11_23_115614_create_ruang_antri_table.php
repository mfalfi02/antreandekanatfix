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
        Schema::create('ruang_antri', function (Blueprint $table) {
            $table->id('id_ruang_antri');

            // Relasi ke users.kode (dosen)
            $table->string('kode_dosen', 20);

            // Jam rencana
            $table->time('expected_jam_buka_ruang_antri')->nullable();
            $table->time('expected_jam_tutup_ruang_antri')->nullable();

            // Jam real
            $table->time('jam_buka_ruang_antri')->nullable();
            $table->time('jam_tutup_ruang_antri')->nullable();

            // Tanggal buka antrean
            $table->date('tanggal_buka_ruang_antri');

            $table->timestamps();

            // Foreign key ke users.kode
            $table->foreign('kode_dosen')
                ->references('kode')
                ->on('users')
                ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruang_antri');
    }
};
