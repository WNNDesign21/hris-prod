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
        Schema::create('turnovers', function (Blueprint $table) {
            $table->increments('id_turnover');
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id')->nullable();
            $table->enum('status_karyawan', ['MD', 'PS', 'HK', 'TM']);
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('jumlah_aktif_karyawan_terakhir')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnovers');
    }
};
