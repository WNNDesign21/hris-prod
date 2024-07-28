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
        Schema::create('seksis', function (Blueprint $table) {
            $table->increments('id_seksi');
            $table->unsignedInteger('departemen_id');
            $table->string('nama');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('departemen_id')->references('id_departemen')->on('departemens')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seksis');
    }
};
