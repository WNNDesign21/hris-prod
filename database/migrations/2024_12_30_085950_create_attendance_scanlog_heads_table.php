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
        Schema::create('attendance_scanlog_heads', function (Blueprint $table) {
            $table->increments('id_scanlog_head');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('device_id');
            $table->string('cloud_id');
            $table->string('device_sn');
            $table->date('start_date');
            $table->date('end_date');

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
        Schema::dropIfExists('attendance_scanlog_heads');
    }
};
