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
        Schema::table('sto_upload', function (Blueprint $table) {
            $table->integer('organization_id')->after('id_sto_upload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sto_upload', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
};
