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
            $table->renameColumn('no_bpjs_ks_v1', 'no_bpjs_kt');
            $table->renameColumn('no_bpjs_kt_v1', 'no_bpjs_ks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->renameColumn('no_bpjs_ks_v1', 'no_bpjs_kt');
            $table->renameColumn('no_bpjs_kt_v1', 'no_bpjs_ks');
        });
    }
};
