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
            $table->integer('organisasi_id');
            $table->integer('departemen_id');

            //Planning
            $table->string('plan_checked_by')->nullable();
            $table->date('plan_checked_at')->nullable();
            $table->string('plan_approved_by')->nullable();
            $table->date('plan_approved_at')->nullable();
            $table->string('plan_legalized_by')->nullable();
            $table->date('plan_legalized_at')->nullable();

            //Actual
            $table->string('actual_checked_by')->nullable();
            $table->date('actual_checked_at')->nullable();
            $table->string('actual_approved_by')->nullable();
            $table->date('actual_approved_at')->nullable();
            $table->string('actual_legalized_by')->nullable();
            $table->date('actual_legalized_at')->nullable();

            $table->integer('total_jam')->default(0);
            $table->enum('status',['WAITING', 'PLANNED', 'COMPLETED', 'REJECTED'])->default('WAITING');
            $table->string('attachment')->nullable();
            $table->timestamp('issued_date')->default(now());
            $table->string('issued_by');

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('departemen_id')->references('id_departemen')->on('departemens')->restrictOnDelete();
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
