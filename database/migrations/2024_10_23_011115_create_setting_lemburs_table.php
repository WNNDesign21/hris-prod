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
        Schema::create('setting_lemburs', function (Blueprint $table) {
            $table->increments('id_setting_lembur');
            $table->unsignedInteger('organisasi_id');
            $table->string('setting_name');
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_lemburs');
    }
};
