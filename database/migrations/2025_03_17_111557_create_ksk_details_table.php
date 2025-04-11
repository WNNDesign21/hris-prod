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
        Schema::create('ksk_details', function (Blueprint $table) {
            $table->increments('id_ksk_detail');
            $table->string('ksk_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('divisi_id')->nullable();
            $table->string('nama_divisi')->nullable();
            $table->unsignedInteger('departemen_id')->nullable();
            $table->string('nama_departemen')->nullable();
            $table->string('karyawan_id');
            $table->string('ni_karyawan');
            $table->string('nama_karyawan');
            $table->unsignedInteger('posisi_id');
            $table->string('nama_posisi');
            $table->unsignedInteger('jabatan_id');
            $table->string('nama_jabatan');
            $table->string('jenis_kontrak');
            $table->integer('jumlah_surat_peringatan')->default(0);
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_alpa')->default(0);
            $table->string('status_ksk')->nullable();
            $table->date('tanggal_renewal_kontrak')->nullable();
            $table->integer('durasi_renewal')->default(0);
            $table->string('cleareance_id')->nullable();
            $table->string('kontrak_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ksk_id')->references('id_ksk')->on('ksk')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ksk_details');
    }
};
