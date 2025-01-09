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
        Schema::create('attendance_karyawan_grup', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('karyawan_id')->nullable();
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('pin')->nullable();
            $table->unsignedInteger('grup_id');
            $table->dateTime('active_date');
            $table->time('toleransi_waktu')->default('00:00:00');
            $table->time('jam_masuk')->default('00:00:00');
            $table->time('jam_keluar')->default('00:00:00');

            $table->timestamps();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('grup_id')->references('id_grup')->on('grups')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_karyawan_grup');
    }
};
