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
        Schema::create('grup_patterns', function (Blueprint $table) {
            $table->increments('id_grup_pattern');
            $table->unsignedInteger('organisasi_id');
            $table->string('nama')->unique();
            $table->json('urutan');

            $table->timestamps();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grup_patterns');
    }
};
