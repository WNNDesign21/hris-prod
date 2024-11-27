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
        Schema::create('attachment_lemburs', function (Blueprint $table) {
            $table->increments('id_attachment_lembur');
            $table->string('lembur_id');
            $table->string('path');

            $table->timestamps();
            $table->foreign('lembur_id')->references('id_lembur')->on('lemburs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_lemburs');
    }
};
