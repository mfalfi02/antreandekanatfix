<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('queues', function (Blueprint $table) {
    $table->id();

    $table->string('nim', 20)->nullable();
    $table->foreign('nim')
          ->references('nim')
          ->on('mahasiswas')
          ->onDelete('set null');

    $table->foreignId('service_id')
          ->constrained('services')
          ->onDelete('cascade');

    $table->string('dosen_id')->nullable();
    $table->foreign('dosen_id')
          ->references('kode_dosen')
          ->on('dosens')
          ->onDelete('set null');

    $table->enum('status', ['Menunggu', 'Sedang Dilayani', 'Selesai'])
          ->default('Menunggu');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
