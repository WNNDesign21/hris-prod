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
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropUnique(['no_bpjs_ks']);
            $table->dropUnique(['no_bpjs_kt']);
            $table->dropUnique(['npwp']);
            $table->dropUnique(['nik']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('no_bpjs_ks')->unique()->nullable();
            $table->string('no_bpjs_kt')->unique()->nullable();
            $table->string('npwp')->unique()->nullable();
            $table->string('nik')->unique()->nullable();
        });
    }
};
