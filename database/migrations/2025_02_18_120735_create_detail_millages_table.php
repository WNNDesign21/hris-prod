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
        Schema::create('detail_millages', function (Blueprint $table) {
            $table->bigIncrements('id_detail_millage');
            $table->unsignedInteger('organisasi_id');
            $table->string('millage_id');
            $table->string('type');
            $table->string('attachment');
            $table->integer('nominal')->default(0);
            $table->string('is_active')->default('Y');

            $table->timestamps();
            
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('millage_id')->references('id_millage')->on('millages')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_millages');
    }
};
