<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rekap_lembur_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisasi_id');
            $table->string('departemen');
            $table->string('periode');
            $table->integer('jumlah_karyawan')->nullable();
            $table->decimal('total_jam_lembur', 8, 2)->nullable();
            $table->decimal('konversi_jam_lembur', 8, 2)->nullable();
            $table->bigInteger('gaji_lembur')->nullable();
            $table->bigInteger('uang_makan')->nullable();
            $table->bigInteger('total_gaji_lembur')->nullable();
            $table->timestamps();
            $table->unique(['departemen', 'periode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekap_lembur_summary');
    }
};
