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
        Schema::create('cleareance_settings', function (Blueprint $table) {
            $table->id('id_cleareance_setting');
            $table->unsignedInteger('organisasi_id');
            $table->string('type');
            $table->string('karyawan_id');
            $table->string('ni_karyawan');
            $table->string('nama_karyawan');
            $table->string('signature')->nullable();

            $table->timestamps();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleareance_settings');
    }
};
