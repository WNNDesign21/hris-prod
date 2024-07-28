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
            $table->string('no_ktp')->unique();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('email')->unique();
            $table->string('no_telp')->unique();
            $table->enum('gol_darah', ['A', 'B', 'AB', 'O']);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('status_keluarga', ['MENIKAH', 'LAJANG']);
            $table->string('npwp')->unique();
            $table->string('no_bpjs_ks')->unique();
            $table->string('no_bpjs_kt')->unique();
            $table->enum('jenis_kontrak', ['PKWT', 'MAGANG', 'THL', 'PKWTT']);
            $table->enum('status_karyawan', ['AKTIF', 'RESIGN', 'PENSIUN']);
            $table->integer('sisa_cuti');
            $table->year('tahun_masuk');
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
