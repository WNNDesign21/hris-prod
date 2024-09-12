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
            $table->string('id_karyawan')->primary();
            $table->string('ni_karyawan')->unique()->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
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
            $table->enum('agama', ['ISLAM', 'KATOLIK', 'KRISTEN', 'KONGHUCU', 'HINDU', 'BUDHA', 'PROTESTAN', 'LAINNYA'])->nullable();
            $table->enum('status_keluarga', ['MENIKAH', 'LAJANG'])->nullable();
            $table->string('npwp')->unique()->nullable();
            $table->string('no_bpjs_ks')->unique()->nullable();
            $table->string('no_bpjs_kt')->unique()->nullable();
            $table->enum('jenis_kontrak', ['PKWT', 'MAGANG', 'THL', 'PKWTT'])->nullable();
            $table->enum('status_karyawan', ['AKTIF', 'RESIGN', 'PENSIUN', 'TERMINASI'])->nullable();
            $table->integer('sisa_cuti')->default(12);
            $table->integer('hutang_cuti')->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Relasi foreign key
            // $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
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
