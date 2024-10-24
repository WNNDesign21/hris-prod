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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->string('id_lembur')->primary();
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('divisi_id')->nullable();

            //Planning
            $table->string('plan_checked_by')->nullable();
            $table->dateTime('plan_checked_at')->nullable();
            $table->string('plan_approved_by')->nullable();
            $table->dateTime('plan_approved_at')->nullable();
            $table->string('plan_legalized_by')->nullable();
            $table->dateTime('plan_legalized_at')->nullable();

            //Actual
            $table->string('actual_checked_by')->nullable();
            $table->dateTime('actual_checked_at')->nullable();
            $table->string('actual_approved_by')->nullable();
            $table->dateTime('actual_approved_at')->nullable();
            $table->string('actual_legalized_by')->nullable();
            $table->dateTime('actual_legalized_at')->nullable();

            $table->integer('total_durasi')->default(0);
            $table->enum('status',['WAITING', 'PLANNED', 'COMPLETED', 'REJECTED'])->default('WAITING');
            $table->string('attachment')->nullable();
            $table->dateTime('issued_date')->default(now());
            $table->string('issued_by');

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};
