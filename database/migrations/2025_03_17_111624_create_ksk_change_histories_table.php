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
        Schema::create('ksk_change_histories', function (Blueprint $table) {
            $table->id('id_ksk_change_history');
            $table->unsignedInteger('ksk_detail_id');
            $table->string('changed_by_id');
            $table->string('changed_by');
            $table->dateTime('changed_at');
            $table->text('reason')->nullable();
            $table->string('status_ksk_before');
            $table->string('status_ksk_after');
            $table->integer('durasi_before');
            $table->integer('durasi_after');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ksk_detail_id')->references('id_ksk_detail')->on('ksk_details')->restrictOnDelete();
            $table->foreign('changed_by_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ksk_change_histories');
    }
};
