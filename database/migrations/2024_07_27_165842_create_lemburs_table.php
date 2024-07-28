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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->increments('id_lembur');
            $table->string('karyawan_id');
            $table->datetime('rencana_kerja');
            $table->datetime('aktual_kerja')->nullable();
            $table->text('deskripsi');
            $table->date('approve1_at')->nullable();
            $table->string('approved1_by')->nullable();
            $table->date('approve2_at')->nullable();
            $table->string('approved2_by')->nullable();
            $table->date('approve3_at')->nullable();
            $table->string('approved3_by')->nullable();
            $table->enum('isDone',['Y','N']);
            $table->string('attachment')->nullable();
            $table->unsignedInteger('createdBy');

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
