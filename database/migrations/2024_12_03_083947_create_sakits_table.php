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
        Schema::create('sakits', function (Blueprint $table) {
            $table->increments('id_sakit');
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->integer('durasi')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('karyawan_pengganti_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('legalized_at')->nullable();
            $table->string('legalized_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->text('rejected_note')->nullable();
            $table->string('attachment')->nullable();

            $table->timestamps();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sakits');
    }
};
