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
        Schema::create('attachment_ksk_details', function (Blueprint $table) {
            $table->id('id_attachment_ksk_detail');
            $table->unsignedInteger('ksk_detail_id');
            $table->string('path');

            $table->timestamps();
            $table->foreign('ksk_detail_id')->references('id_ksk_detail')->on('ksk_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_ksk_details');
    }
};
