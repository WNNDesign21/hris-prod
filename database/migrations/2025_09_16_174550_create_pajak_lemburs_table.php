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
        Schema::create('pajak_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_id');
            $table->date('periode');
            $table->integer('potongan_pph')->default(0);
            $table->timestamps();

            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->onDelete('cascade');
            $table->unique(['karyawan_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajak_lemburs');
    }
};
