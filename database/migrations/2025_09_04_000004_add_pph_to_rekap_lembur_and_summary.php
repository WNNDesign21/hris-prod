<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah kolom ke rekap_lembur
        Schema::table('rekap_lembur', function (Blueprint $table) {
            $table->decimal('pph_persen', 5, 2)->nullable()->after('total_gaji_lembur');
            $table->bigInteger('total_pph')->nullable()->after('pph_persen');
            $table->bigInteger('total_diterima')->nullable()->after('total_pph');
        });

        // Tambah kolom ke rekap_lembur_summary
        Schema::table('rekap_lembur_summary', function (Blueprint $table) {
            $table->decimal('pph_persen', 5, 2)->nullable()->after('total_gaji_lembur');
            $table->bigInteger('total_pph')->nullable()->after('pph_persen');
            $table->bigInteger('total_gaji_lembur_diterima')->nullable()->after('total_pph');
        });
    }

    public function down()
    {
        Schema::table('rekap_lembur', function (Blueprint $table) {
            $table->dropColumn(['pph_persen', 'total_pph', 'total_diterima']);
        });
        Schema::table('rekap_lembur_summary', function (Blueprint $table) {
            $table->dropColumn(['pph_persen', 'total_pph', 'total_gaji_lembur_diterima']);
        });
    }
};
