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
        Schema::table('detail_lemburs', function (Blueprint $table) {
            $table->integer('durasi_istirahat')->default(0);
            $table->integer('durasi_konversi_lembur')->default(0);
            $table->integer('gaji_lembur')->default(0);
            $table->integer('uang_makan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_lemburs', function (Blueprint $table) {
            //
        });
    }
};
