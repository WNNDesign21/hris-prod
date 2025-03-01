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
        Schema::create('millages', function (Blueprint $table) {
            $table->string('id_millage')->primary();
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->string('nama_karyawan');
            $table->string('ni_karyawan');
            $table->string('no_polisi');
            $table->enum('is_claimed', ['Y', 'N'])->default('N');

            //APPROVAL
            $table->string('checked_by')->nullable();
            $table->dateTime('checked_at')->nullable();
            $table->string('legalized_by')->nullable();
            $table->dateTime('legalized_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->dateTime('rejected_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('millages');
    }
};
