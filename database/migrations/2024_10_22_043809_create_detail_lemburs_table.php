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
        Schema::create('detail_lemburs', function (Blueprint $table) {
            $table->increments('id_detail_lembur');
            $table->string('lembur_id');
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->dateTime('rencana_mulai_lembur');
            $table->dateTime('rencana_selesai_lembur');
            $table->enum('is_rencana_approved ', ['Y', 'N'])->default('Y');
            $table->dateTime('aktual_mulai_lembur')->nullable();
            $table->dateTime('aktual_selesai_lembur')->nullable();
            $table->enum('is_aktual_approved ', ['Y', 'N'])->default('Y');
            $table->integer('durasi')->default(0);
            $table->text('deskripsi_pekerjaan');
            $table->text('keterangan')->nullable();
            $table->integer('nominal')->default(0);

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('lembur_id')->references('id_lembur')->on('lemburs')->restrictOnDelete();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_lemburs');
    }
};
