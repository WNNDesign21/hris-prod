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
        Schema::create('export_slip_lemburs', function (Blueprint $table) {
            $table->increments('id_export_slip_lembur');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->date('periode');
            $table->string('status', 2)->default('IP'); // IP = IN PROGRESS, CO = COMPLETED , FL = FAILED
            $table->string('attachment')->nullable();
            $table->string('message')->nullable();

            $table->timestamps();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_slip_lemburs');
    }
};
