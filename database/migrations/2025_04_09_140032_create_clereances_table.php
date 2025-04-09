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
        Schema::create('cleareances', function (Blueprint $table) {
            $table->string('id_cleareance')->primary();
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('divisi_id')->nullable();
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('jabatan_id')->nullable();
            $table->unsignedInteger('posisi_id')->nullable();
            $table->string('nama_divisi')->nullable();
            $table->string('nama_departemen')->nullable();
            $table->string('nama_jabatan')->nullable();
            $table->string('nama_posisi')->nullable();
            $table->date('tanggal_akhir_bekerja')->nullable();
            $table->string('status', 1)->default('N');

            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->onDelete('cascade');
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleareances');
    }
};
