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
        Schema::create('detail_tugasluars', function (Blueprint $table) {
            $table->increments('id_detail_tugasluar');
            $table->string('tugasluar_id');
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->string('ni_karyawan')->nullable();
            $table->string('pin')->nullable();
            $table->date('date')->default(now()->format('Y-m-d'));
            $table->string('is_active')->default('Y');

            $table->timestamps();

            $table->foreign('tugasluar_id')->references('id_tugasluar')->on('tugasluars')->restrictOnDelete();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_tugasluars');
    }
};
