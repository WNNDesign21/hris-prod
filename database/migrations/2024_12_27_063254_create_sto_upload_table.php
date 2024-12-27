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
        Schema::create('sto_upload', function (Blueprint $table) {
            $table->id("id_sto_upload");
            $table->integer('wh_id');
            $table->string('wh_name');
            $table->integer('locator_id');
            $table->string('locator_name');
            $table->integer('customer_id');
            $table->string('customer_name');
            $table->integer('product_id');
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_desc')->nullable();
            $table->string('model')->nullable();
            $table->string('qty_book')->default(0);
            $table->string('qty_count')->default(0);
            $table->string('balance');
            $table->date('doc_date')->default(now());
            $table->string('processed')->default('N');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sto_upload');
    }
};
