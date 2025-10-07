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
        Schema::create('sto_headers', function (Blueprint $table) {
            $table->increments('id_sto_header');
            $table->string('year');
            $table->string('issued_by');
            $table->string('issued_name');
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('wh_id');
            $table->string('wh_name');
            $table->date('doc_date')->default(now());
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_headers');
    }
};
