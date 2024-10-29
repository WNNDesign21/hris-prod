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
            $table->renameColumn('is_rencana_approved ', 'is_rencana_approved');
            $table->renameColumn('is_aktual_approved ', 'is_aktual_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_lemburs', function (Blueprint $table) {
            $table->renameColumn('is_rencana_approved', 'is_rencana_approved ');
            $table->renameColumn('is_aktual_approved', 'is_aktual_approved ');
        });
    }
};
