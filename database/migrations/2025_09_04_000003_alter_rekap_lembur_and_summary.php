<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Ubah karyawan_id di rekap_lembur menjadi string
        Schema::table('rekap_lembur', function (Blueprint $table) {
            $table->string('karyawan_id')->change();
        });

        // Tambah is_locked di rekap_lembur_summary
        Schema::table('rekap_lembur_summary', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('total_gaji_lembur');
        });
    }

    public function down()
    {
        // Kembalikan karyawan_id ke unsignedBigInteger
        Schema::table('rekap_lembur', function (Blueprint $table) {
            $table->unsignedBigInteger('karyawan_id')->change();
        });

        // Hapus is_locked dari rekap_lembur_summary
        Schema::table('rekap_lembur_summary', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
