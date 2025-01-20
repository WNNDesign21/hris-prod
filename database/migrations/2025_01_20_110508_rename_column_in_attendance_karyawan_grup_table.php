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
        Schema::table('attendance_karyawan_grup', function (Blueprint $table) {
            $table->dropColumn('pin');
            $table->renameColumn('pin_new', 'pin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_karyawan_grup', function (Blueprint $table) {
            $table->renameColumn('pin', 'pin_new');
            $table->integer('pin')->nullable();
        });
    }
};
