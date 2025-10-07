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
        Schema::create('attendance_gps', function (Blueprint $table) {
            $table->increments('id_att_gps');
            $table->string('karyawan_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();
            $table->string('pin');
            $table->string('latitude');
            $table->string('longitude');
            $table->date('attendance_date');
            $table->dateTime('attendance_time');
            $table->string('attachment');
            $table->string('type', 2);
            $table->enum('status', ['IN', 'OUT']);
            $table->unsignedInteger('scanlog_id')->nullable();

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
        Schema::dropIfExists('attendance_gps');
    }
};
