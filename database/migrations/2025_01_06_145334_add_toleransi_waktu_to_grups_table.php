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
        Schema::table('grups', function (Blueprint $table) {
            $table->dropColumn('jam_masuk');
            $table->dropColumn('jam_keluar');
            $table->time('toleransi_waktu')->default('00:00:00');
            $table->time('jam_masuk')->default('00:00:00');
            $table->time('jam_keluar')->default('00:00:00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grups', function (Blueprint $table) {
            $table->dropColumn('toleransi_waktu');
            $table->dropColumn('jam_masuk');
            $table->dropColumn('jam_keluar');
            $table->string('jam_masuk')->nullable();
            $table->string('jam_keluar')->nullable();
        });
    }
};
