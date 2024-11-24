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
        Schema::create('gaji_departemens', function (Blueprint $table) {
            $table->increments('id_gaji_departemen');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->date('periode');
            $table->integer('total_gaji')->default(0);
            $table->integer('nominal_batas_lembur')->default(0);

            $table->foreign('departemen_id')->references('id_departemen')->on('departemens')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_departemens');
    }
};
