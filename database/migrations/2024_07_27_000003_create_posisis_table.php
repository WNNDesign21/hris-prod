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
        Schema::create('posisis', function (Blueprint $table) {
            $table->increments('id_posisi');
            $table->unsignedInteger('jabatan_id');
            $table->unsignedInteger('organisasi_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('seksi_id')->nullable();
            $table->string('nama');
            $table->integer('parent_id');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('jabatan_id')->references('id_jabatan')->on('jabatans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posisis');
    }
};
