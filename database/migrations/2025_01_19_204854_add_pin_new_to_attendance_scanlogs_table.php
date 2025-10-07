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
        Schema::table('attendance_scanlogs', function (Blueprint $table) {
            $table->string('pin_new')->nullable();
        });

        DB::statement('UPDATE attendance_scanlogs SET pin_new = pin');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_scanlogs', function (Blueprint $table) {
            $table->dropColumn('pin_new');
        });
    }
};
