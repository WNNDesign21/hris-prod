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
        Schema::create('karyawan_posisi', function (Blueprint $table) {
            $table->id();
            $table->string('karyawan_id');
            $table->unsignedInteger('posisi_id');
            $table->foreign('karyawan_id')->references("id_karyawan")->on("karyawans")->constrained()->onDelete('cascade');
            $table->foreign('posisi_id')->references("id_posisi")->on("posisis")->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_posisi');
    }
};
