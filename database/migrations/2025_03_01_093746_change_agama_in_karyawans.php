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
            $table->string('agama')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->enum('agama', ['ISLAM', 'KATOLIK', 'KRISTEN', 'KONGHUCU', 'HINDU', 'BUDHA', 'PROTESTAN', 'LAINNYA'])->nullable()->change();
        });
    }
};
