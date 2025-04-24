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
        Schema::table('ksk_change_histories', function (Blueprint $table) {
            $table->string('jenis_kontrak_before')->nullable();
            $table->string('jenis_kontrak_after')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ksk_change_histories', function (Blueprint $table) {
            $table->dropColumn('jenis_kontrak_before');
            $table->dropColumn('jenis_kontrak_after');
        });
    }
};
