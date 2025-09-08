<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rekap_lembur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('organisasi_id');
            $table->string('departemen');
            $table->string('jabatan');
            $table->string('periode');
            $table->bigInteger('gaji_pokok')->nullable();
            $table->bigInteger('upah_lembur_per_jam')->nullable();
            $table->decimal('total_jam_lembur', 8, 2)->nullable();
            $table->decimal('konversi_jam_lembur', 8, 2)->nullable();
            $table->bigInteger('gaji_lembur')->nullable();
            $table->bigInteger('uang_makan')->nullable();
            $table->bigInteger('total_gaji_lembur')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            $table->unique(['karyawan_id', 'periode']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekap_lembur');
    }
};
