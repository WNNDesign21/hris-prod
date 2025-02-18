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
        Schema::create('tugasluars', function (Blueprint $table) {
            $table->string('id_tugasluar')->primary();
            $table->unsignedInteger('organisasi_id');
            $table->string('karyawan_id');
            $table->string('ni_karyawan')->nullable();
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->date('created_date')->default(now()->format('Y-m-d'));
            $table->dateTime('tanggal_pergi')->nullable();
            $table->dateTime('tanggal_kembali')->nullable();
            $table->string('jenis_kendaraan');
            $table->string('no_polisi');
            $table->integer('km_awal')->nullable();
            $table->integer('km_akhir')->nullable();
            $table->integer('jarak_tempuh')->default(0);
            $table->string('pengemudi_id')->nullable();
            $table->string('tempat_asal');
            $table->string('tempat_tujuan');
            $table->text('keterangan');
            $table->float('pembagi')->default(1);
            $table->integer('rate')->default(0);
            $table->integer('nominal')->default(0);
            $table->string('millage_id')->nullable();

            //Planning
            $table->string('checked_by')->nullable();
            $table->dateTime('checked_at')->nullable();
            $table->string('legalized_by')->nullable();
            $table->dateTime('legalized_at')->nullable();
            $table->string('known_by')->nullable();
            $table->dateTime('known_at')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('tugasluars');
    }
};
