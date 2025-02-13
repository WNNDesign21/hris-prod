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
        Schema::table('detail_lemburs', function (Blueprint $table) {
            $table->string('rencana_last_changed_by')->nullable();
            $table->dateTime('rencana_last_changed_at')->nullable();
            $table->string('aktual_last_changed_by')->nullable();
            $table->dateTime('aktual_last_changed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_lemburs', function (Blueprint $table) {
            $table->dropColumn('rencana_last_changed_by');
            $table->dropColumn('rencana_last_changed_at');
            $table->dropColumn('aktual_last_changed_by');
            $table->dropColumn('aktual_last_changed_at');
        });
    }
};
