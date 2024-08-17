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
        Schema::create('kontraks', function (Blueprint $table) {
            $table->string('id_kontrak')->primary();
            $table->string('karyawan_id');
            $table->string('nama_posisi')->nullale();
            $table->enum('jenis', ['PKWT', 'MAGANG', 'THL', 'PKWTT']);
            $table->enum('status', ['WAITING', 'EXTENDED', 'CUTTOFF'])->default('WAITING');
            $table->integer('durasi')->nullable();
            $table->integer('salary')->nullable();
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('isAccepted', ['Y', 'N'])->nullable();
            $table->string('attachment')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontraks');
    }
};
