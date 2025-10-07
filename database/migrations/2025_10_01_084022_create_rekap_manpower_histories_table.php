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
        Schema::create('rekap_manpower_histories', function (Blueprint $table) {
            $table->id();
            $table->date('period');
            $table->unsignedBigInteger('organisasi_id')->nullable();
            $table->json('data');
            $table->timestamps();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->onDelete('cascade');
            $table->unique(['period', 'organisasi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_manpower_histories');
    }
};