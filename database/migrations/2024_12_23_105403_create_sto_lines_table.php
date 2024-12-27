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
            $table->string('inputed_by')->nullable();
            $table->string('inputed_name')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_name')->nullable();
            $table->integer('sto_header_id');
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('location_area')->nullable();
            $table->unsignedInteger('wh_id')->nullable();
            $table->string('wh_name')->nullable();
            $table->unsignedInteger('locator_id')->nullable();
            $table->string('locator_value')->nullable();
            $table->string('no_label')->unique();
            $table->string('spec_size')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('part_code')->nullable();
            $table->string('part_name')->nullable();
            $table->string('part_desc')->nullable();
            $table->string('model')->nullable();
            $table->string('identitas_lot')->nullable();
            $table->string('quantity')->nullable();
            $table->string('status')->nullable();
            $table->string('processed', 1)->default('N');
            $table->timestamps();

            $table->softDeletes();
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
