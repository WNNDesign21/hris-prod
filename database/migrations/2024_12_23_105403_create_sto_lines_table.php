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
        Schema::create('sto_lines', function (Blueprint $table) {
            $table->increments('id_sto_line');
            $table->integer('sto_header_id');
            $table->string('customer')->nullable();
            $table->integer('wh_id')->nullable();
            $table->string('wh_name')->nullable();
            $table->string('no_label')->unique();
            $table->string('spec_size')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('part_code')->nullable();
            $table->string('part_name')->nullable();
            $table->string('part_desc')->nullable();
            $table->string('model')->nullable();
            $table->string('identitas_lot')->nullable();
            $table->string('quantity')->nullable();
            $table->string('status')->nullable();
            $table->string('processed')->nullable();
            $table->timestamps();

            $table->foreign('sto_header_id')->references('id_sto_header')->on('sto_headers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_lines');
    }
};
