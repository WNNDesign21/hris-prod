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
        Schema::create('attendance_scanlog_lines', function (Blueprint $table) {
            $table->increments('id_scanlog_line');
            $table->unsignedInteger('scanlog_head_id');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('device_id');
            $table->integer('pin');
            $table->dateTime('scan_date');
            $table->integer('scan_status');
            $table->integer('verify');

            $table->timestamps();

            $table->foreign('scanlog_head_id')->references('id_scanlog_head')->on('attendance_scanlog_heads')->onDelete('cascade');
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('device_id')->references('id_device')->on('attendance_devices')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_scanlog_lines');
    }
};
