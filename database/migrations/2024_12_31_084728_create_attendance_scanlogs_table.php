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
        Schema::create('attendance_scanlogs', function (Blueprint $table) {
            $table->increments('id_scanlog');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('device_id');
            $table->integer('pin');
            $table->date('start_date_scan');
            $table->date('end_date_scan');
            $table->dateTime('scan_date');
            $table->integer('scan_status');
            $table->integer('verify');

            $table->timestamps();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('device_id')->references('id_device')->on('attendance_devices')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_scanlogs');
    }
};
