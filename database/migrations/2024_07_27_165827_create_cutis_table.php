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
            $table->string('jenis_cuti')->nullable();
            $table->integer('jenis_cuti_id')->nullable();
            $table->integer('durasi_cuti')->default(1);
            $table->date('rencana_mulai_cuti');
            $table->date('rencana_selesai_cuti');
            $table->date('aktual_mulai_cuti')->nullable();
            $table->date('aktual_selesai_cuti')->nullable();
            $table->text('alasan_cuti')->nullable();
            $table->string('karyawan_pengganti_id')->nullable();
            $table->timestamp('checked1_at')->nullable();
            $table->string('checked1_by')->nullable();
            $table->timestamp('checked2_at')->nullable();
            $table->string('checked2_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('legalized_at')->nullable();
            $table->string('legalized_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->text('rejected_note')->nullable();
            $table->enum('status_dokumen', ['WAITING', 'APPROVED', 'REJECTED'])->default('WAITING');
            $table->enum('status_cuti',['SCHEDULED', 'ON LEAVE' ,'COMPLETED', 'CANCELED'])->nullable();
            $table->string('attachment')->nullable();

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
