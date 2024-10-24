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
        Schema::create('setting_lembur_karyawans', function (Blueprint $table) {
            $table->increments('id_setting_lembur_karyawan');
            $table->string('karyawan_id')->unique();
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('jabatan_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->integer('gaji')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('jabatan_id')->references('id_jabatan')->on('jabatans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_lembur_karyawans');
    }
};
