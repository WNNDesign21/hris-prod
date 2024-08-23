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
            $table->unsignedInteger('posisi_id')->nullable();
            $table->string('nama_posisi')->nullable();
            $table->string('no_surat')->nullable();
            $table->enum('jenis', ['PKWT', 'MAGANG', 'THL', 'PKWTT']);
            $table->enum('status', ['WAITING', 'EXTENDED', 'CUTOFF'])->default('WAITING');
            $table->integer('durasi')->nullable();
            $table->integer('salary')->nullable();
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->date('issued_date')->nullable();
            $table->string('tempat_administrasi')->nullable()->default('Karawang');
            $table->string('status_change_by')->nullable();
            $table->string('status_change_date')->nullable();
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
