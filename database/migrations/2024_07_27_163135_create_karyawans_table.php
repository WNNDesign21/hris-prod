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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->string('id_karyawan', 6)->primary();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organisasi_id');
            $table->unsignedBigInteger('posisi_id');
            $table->unsignedBigInteger('divisi_id')->nullable();
            $table->unsignedBigInteger('departemen_id')->nullable();
            $table->unsignedBigInteger('seksi_id')->nullable();
            $table->unsignedBigInteger('grup_id')->nullable();
            $table->string('no_ktp')->unique()->nullable();
            $table->string('nik')->unique()->nullable();
            $table->string('nama')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('no_telp')->unique()->nullable();
            $table->enum('gol_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->enum('status_keluarga', ['MENIKAH', 'LAJANG'])->nullable();
            $table->string('npwp')->unique()->nullable();
            $table->string('no_bpjs_ks')->unique()->nullable();
            $table->string('no_bpjs_kt')->unique()->nullable();
            $table->enum('jenis_kontrak', ['PKWT', 'MAGANG', 'THL', 'PKWTT'])->nullable();
            $table->enum('status_karyawan', ['AKTIF', 'RESIGN', 'PENSIUN'])->nullable();
            $table->integer('sisa_cuti')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_keluar')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Relasi foreign key
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('posisi_id')->references('id_posisi')->on('posisis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
