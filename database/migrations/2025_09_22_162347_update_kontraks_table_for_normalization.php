<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JenisKontrak;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_kontrak_id')->nullable()->after('jenis');
            $table->string('rekomendasi')->nullable()->after('evidence');
        });

        $jenisKontraks = JenisKontrak::all();
        foreach ($jenisKontraks as $jenis) {
            DB::table('kontraks')->where('jenis', $jenis->nama)->update(['jenis_kontrak_id' => $jenis->id]);
        }

        Schema::table('kontraks', function (Blueprint $table) {
            $table->foreign('jenis_kontrak_id')->references('id')->on('jenis_kontraks');
            $table->dropColumn('jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->enum('jenis', ['PKWT', 'MAGANG', 'PKWTT', 'PENGKARYAAN', 'PK'])->nullable()->after('id_kontrak');
        });

        $jenisKontraks = JenisKontrak::all();
        foreach ($jenisKontraks as $jenis) {
            DB::table('kontraks')->where('jenis_kontrak_id', $jenis->id)->update(['jenis' => $jenis->nama]);
        }

        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropForeign(['jenis_kontrak_id']);
            $table->dropColumn('jenis_kontrak_id');
            $table->dropColumn('rekomendasi');
        });
    }
};