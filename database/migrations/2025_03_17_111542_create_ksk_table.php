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
        Schema::create('ksk', function (Blueprint $table) {
            $table->string('id_ksk')->primary();
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('divisi_id')->nullable();
            $table->string('nama_divisi')->nullable();
            $table->unsignedInteger('departemen_id')->nullable();
            $table->string('nama_departemen')->nullable();
            $table->date('release_date')->default(now()->format('Y-m-d'));
            $table->unsignedInteger('parent_id')->nullable();

            // Approval
            $table->string('released_by_id')->nullable();
            $table->string('released_by')->nullable();
            $table->dateTime('released_at')->nullable();

            $table->string('checked_by_id')->nullable();
            $table->string('checked_by')->nullable();
            $table->dateTime('checked_at')->nullable();

            $table->string('approved_by_id')->nullable();
            $table->string('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();

            $table->string('reviewed_div_by_id')->nullable();
            $table->string('reviewed_div_by')->nullable();
            $table->dateTime('reviewed_div_at')->nullable();

            $table->string('reviewed_ph_by_id')->nullable();
            $table->string('reviewed_ph_by')->nullable();
            $table->dateTime('reviewed_ph_at')->nullable();

            $table->string('reviewed_dir_by_id')->nullable();
            $table->string('reviewed_dir_by')->nullable();
            $table->dateTime('reviewed_dir_at')->nullable();

            $table->string('legalized_by')->nullable();
            $table->dateTime('legalized_at')->nullable();

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
        Schema::dropIfExists('ksk');
    }
};
