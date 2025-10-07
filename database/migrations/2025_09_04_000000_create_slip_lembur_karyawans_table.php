<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('slip_lembur_karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_id', 255); // varchar(255)
            $table->integer('organisasi_id'); // int
            $table->string('periode', 7); // format: YYYY-MM
            $table->decimal('total_lembur', 18, 2)->default(0); // total nominal lembur
            $table->decimal('total_uang_makan', 18, 2)->default(0);
            $table->decimal('total_jam', 8, 2)->default(0);
            $table->decimal('total_konversi_jam', 8, 2)->default(0);
            $table->integer('pph_persen')->default(0); // persentase PPH
            $table->decimal('total_pph', 18, 2)->default(0);
            $table->decimal('total_diterima', 18, 2)->default(0);
            $table->boolean('is_locked')->default(false); // slip sudah final/lock
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode', 'organisasi_id'], 'unique_slip_lembur');
            // Relasi opsional, jika ingin foreign key:
            // $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->onDelete('cascade');
            // $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('slip_lembur_karyawans');
    }
};
