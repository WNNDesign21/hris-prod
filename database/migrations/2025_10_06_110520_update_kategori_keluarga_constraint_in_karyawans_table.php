<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Hapus constraint yang lama (nama constraint bisa berbeda, sesuaikan jika perlu)
            // Nama default di PostgreSQL adalah: nama_tabel_nama_kolom_check
            DB::statement('ALTER TABLE karyawans DROP CONSTRAINT IF EXISTS karyawans_kategori_keluarga_check');

            // Update data yang ada ke format baru (dengan '/') sebelum menambahkan constraint baru
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK/0' WHERE kategori_keluarga = 'TK0'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK/1' WHERE kategori_keluarga = 'TK1'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK/2' WHERE kategori_keluarga = 'TK2'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK/3' WHERE kategori_keluarga = 'TK3'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K/0' WHERE kategori_keluarga = 'K0'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K/1' WHERE kategori_keluarga = 'K1'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K/2' WHERE kategori_keluarga = 'K2'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K/3' WHERE kategori_keluarga = 'K3'");

            // Tambahkan constraint baru dengan format yang diinginkan
            DB::statement("ALTER TABLE karyawans ADD CONSTRAINT karyawans_kategori_keluarga_check CHECK (kategori_keluarga IN ('TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/0', 'K/1', 'K/2', 'K/3'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Hapus constraint baru
            DB::statement('ALTER TABLE karyawans DROP CONSTRAINT IF EXISTS karyawans_kategori_keluarga_check');

            // Kembalikan data ke format lama (tanpa '/') sebelum menambahkan constraint lama
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK0' WHERE kategori_keluarga = 'TK/0'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK1' WHERE kategori_keluarga = 'TK/1'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK2' WHERE kategori_keluarga = 'TK/2'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'TK3' WHERE kategori_keluarga = 'TK/3'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K0' WHERE kategori_keluarga = 'K/0'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K1' WHERE kategori_keluarga = 'K/1'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K2' WHERE kategori_keluarga = 'K/2'");
            DB::statement("UPDATE karyawans SET kategori_keluarga = 'K3' WHERE kategori_keluarga = 'K/3'");

            // Kembalikan constraint lama (jika diperlukan)
            DB::statement("ALTER TABLE karyawans ADD CONSTRAINT karyawans_kategori_keluarga_check CHECK (kategori_keluarga IN ('TK0', 'TK1', 'TK2', 'TK3', 'K0', 'K1', 'K2', 'K3'))");
        });
    }
};
