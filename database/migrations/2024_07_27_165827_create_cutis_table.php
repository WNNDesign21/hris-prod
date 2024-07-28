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
        Schema::create('cutis', function (Blueprint $table) {
            $table->increments('id_cuti');
            $table->string('karyawan_id');
            $table->enum('jenis_cuti',['Hamil', 'Pribadi', 'Sakit', 'Nasional', 'Kedukaan', 'Tahunan']);
            $table->integer('lama_cuti');
            $table->date('tanggal_cuti');
            $table->text('alasan_cuti');
            $table->unsignedInteger('karyawan_pengganti_id')->nullable();
            $table->date('approve1_at')->nullable();
            $table->string('approved1_by')->nullable();
            $table->date('approve2_at')->nullable();
            $table->string('approved2_by')->nullable();
            $table->date('approve3_at')->nullable();
            $table->string('approved3_by')->nullable();

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
        Schema::dropIfExists('cutis');
    }
};
