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
        Schema::create('cleareance_details', function (Blueprint $table) {
            $table->id('id_cleareance_detail');
            $table->unsignedInteger('organisasi_id');
            $table->string('cleareance_id');
            $table->string('type');
            $table->string('is_clear', 1)->default('N');
            $table->text('keterangan')->nullable();
            $table->string('confirmed_by_id')->nullable();
            $table->string('confirmed_by')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->string('attachment')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cleareance_id')->references('id_cleareance')->on('cleareances')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleareance_details');
    }
};
