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
            $table->increments('id_kontrak');
            $table->string('karyawan_id');
            $table->enum('tipe', ['PKWT', 'MAGANG', 'THL', 'PKWTT']);
            $table->integer('durasi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->string('attachment');

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
