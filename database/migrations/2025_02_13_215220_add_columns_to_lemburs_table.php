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
        Schema::table('lemburs', function (Blueprint $table) {
            $table->string('plan_reviewed_by')->nullable();
            $table->dateTime('plan_reviewed_at')->nullable();
            $table->string('actual_reviewed_by')->nullable();
            $table->dateTime('actual_reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropColumn('plan_reviewed_by');
            $table->dropColumn('plan_reviewed_at');
            $table->dropColumn('actual_reviewed_by');
            $table->dropColumn('actual_reviewed_at');
        });
    }
};
