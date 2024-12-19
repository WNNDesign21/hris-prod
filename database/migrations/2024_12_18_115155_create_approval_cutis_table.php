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
        Schema::create('approval_cutis', function (Blueprint $table) {
            $table->increments('id_approval_cuti');
            $table->unsignedInteger('cuti_id');
            $table->unsignedInteger('checked1_for');
            $table->unsignedInteger('checked1_by')->nullable();
            $table->string('checked1_karyawan_id')->nullable();
            $table->unsignedInteger('checked2_for');
            $table->unsignedInteger('checked2_by')->nullable();
            $table->string('checked2_karyawan_id')->nullable();
            $table->unsignedInteger('approved_for');
            $table->unsignedInteger('approved_by')->nullable();
            $table->string('approved_karyawan_id')->nullable();

            $table->timestamps();
            $table->foreign('cuti_id')->references('id_cuti')->on('cutis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_cutis');
    }
};
