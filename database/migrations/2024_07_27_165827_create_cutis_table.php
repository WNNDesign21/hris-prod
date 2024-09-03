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
            $table->string('jenis_cuti_id')->nullable();
            $table->integer('durasi_cuti')->default(1);
            $table->date('rencana_mulai_cuti');
            $table->date('rencana_selesai_cuti');
            $table->date('aktual_mulai_cuti');
            $table->date('aktual_selesai_cuti');
            $table->text('alasan_cuti');
            $table->unsignedInteger('karyawan_pengganti_id')->nullable();
            $table->date('checked_at')->nullable();
            $table->string('checked_by')->nullable();
            $table->date('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('legalize_at')->nullable();
            $table->string('legalize_by')->nullable();
            $table->date('rejected_at')->nullable();
            $table->string('rejected_by')->nullable();
            $table->text('rejected_note')->nullable();
            $table->enum('status_cuti', ['WAITING', 'APPROVED', 'REJECTED'])->default('WAITING');
            $table->enum('isCompleted',['Y', 'N'])->default('N');
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
