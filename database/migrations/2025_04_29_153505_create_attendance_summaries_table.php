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
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id('id_att_summary');
            $table->string('karyawan_id');
            $table->string('pin');
            $table->date('periode');
            $table->unsignedInteger('organisasi_id');
            $table->unsignedInteger('divisi_id')->nullable();
            $table->unsignedInteger('departemen_id')->nullable();
            $table->unsignedInteger('seksi_id')->nullable();
            $table->unsignedInteger('jabatan_id')->nullable();
            $table->integer('total_absen')->default(0);
            $table->integer('total_sakit')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->integer('keterlambatan')->default(0);
            $table->string('is_cutoff', 1)->default('N'); // Y = Yes, N = No
            $table->integer('tanggal1_selisih')->default(0);
            $table->string('tanggal1_status', 1)->default('A'); // H = Hadir, S = Sakit, I = Izin, A = Absen, C = Cuti
            $table->string('tanggal1_in')->nullable();
            $table->string('tanggal1_out')->nullable();

            $table->integer('tanggal2_selisih')->default(0);
            $table->string('tanggal2_status', 1)->default('A');
            $table->string('tanggal2_in')->nullable();
            $table->string('tanggal2_out')->nullable();

            $table->integer('tanggal3_selisih')->default(0);
            $table->string('tanggal3_status', 1)->default('A');
            $table->string('tanggal3_in')->nullable();
            $table->string('tanggal3_out')->nullable();

            $table->integer('tanggal4_selisih')->default(0);
            $table->string('tanggal4_status', 1)->default('A');
            $table->string('tanggal4_in')->nullable();
            $table->string('tanggal4_out')->nullable();

            $table->integer('tanggal5_selisih')->default(0);
            $table->string('tanggal5_status', 1)->default('A');
            $table->string('tanggal5_in')->nullable();
            $table->string('tanggal5_out')->nullable();

            $table->integer('tanggal6_selisih')->default(0);
            $table->string('tanggal6_status', 1)->default('A');
            $table->string('tanggal6_in')->nullable();
            $table->string('tanggal6_out')->nullable();

            $table->integer('tanggal7_selisih')->default(0);
            $table->string('tanggal7_status', 1)->default('A');
            $table->string('tanggal7_in')->nullable();
            $table->string('tanggal7_out')->nullable();

            $table->integer('tanggal8_selisih')->default(0);
            $table->string('tanggal8_status', 1)->default('A');
            $table->string('tanggal8_in')->nullable();
            $table->string('tanggal8_out')->nullable();

            $table->integer('tanggal9_selisih')->default(0);
            $table->string('tanggal9_status', 1)->default('A');
            $table->string('tanggal9_in')->nullable();
            $table->string('tanggal9_out')->nullable();

            $table->integer('tanggal10_selisih')->default(0);
            $table->string('tanggal10_status', 1)->default('A');
            $table->string('tanggal10_in')->nullable();
            $table->string('tanggal10_out')->nullable();

            $table->integer('tanggal11_selisih')->default(0);
            $table->string('tanggal11_status', 1)->default('A');
            $table->string('tanggal11_in')->nullable();
            $table->string('tanggal11_out')->nullable();

            $table->integer('tanggal12_selisih')->default(0);
            $table->string('tanggal12_status', 1)->default('A');
            $table->string('tanggal12_in')->nullable();
            $table->string('tanggal12_out')->nullable();

            $table->integer('tanggal13_selisih')->default(0);
            $table->string('tanggal13_status', 1)->default('A');
            $table->string('tanggal13_in')->nullable();
            $table->string('tanggal13_out')->nullable();

            $table->integer('tanggal14_selisih')->default(0);
            $table->string('tanggal14_status', 1)->default('A');
            $table->string('tanggal14_in')->nullable();
            $table->string('tanggal14_out')->nullable();

            $table->integer('tanggal15_selisih')->default(0);
            $table->string('tanggal15_status', 1)->default('A');
            $table->string('tanggal15_in')->nullable();
            $table->string('tanggal15_out')->nullable();

            $table->integer('tanggal16_selisih')->default(0);
            $table->string('tanggal16_status', 1)->default('A');
            $table->string('tanggal16_in')->nullable();
            $table->string('tanggal16_out')->nullable();

            $table->integer('tanggal17_selisih')->default(0);
            $table->string('tanggal17_status', 1)->default('A');
            $table->string('tanggal17_in')->nullable();
            $table->string('tanggal17_out')->nullable();

            $table->integer('tanggal18_selisih')->default(0);
            $table->string('tanggal18_status', 1)->default('A');
            $table->string('tanggal18_in')->nullable();
            $table->string('tanggal18_out')->nullable();

            $table->integer('tanggal19_selisih')->default(0);
            $table->string('tanggal19_status', 1)->default('A');
            $table->string('tanggal19_in')->nullable();
            $table->string('tanggal19_out')->nullable();

            $table->integer('tanggal20_selisih')->default(0);
            $table->string('tanggal20_status', 1)->default('A');
            $table->string('tanggal20_in')->nullable();
            $table->string('tanggal20_out')->nullable();

            $table->integer('tanggal21_selisih')->default(0);
            $table->string('tanggal21_status', 1)->default('A');
            $table->string('tanggal21_in')->nullable();
            $table->string('tanggal21_out')->nullable();

            $table->integer('tanggal22_selisih')->default(0);
            $table->string('tanggal22_status', 1)->default('A');
            $table->string('tanggal22_in')->nullable();
            $table->string('tanggal22_out')->nullable();

            $table->integer('tanggal23_selisih')->default(0);
            $table->string('tanggal23_status', 1)->default('A');
            $table->string('tanggal23_in')->nullable();
            $table->string('tanggal23_out')->nullable();

            $table->integer('tanggal24_selisih')->default(0);
            $table->string('tanggal24_status', 1)->default('A');
            $table->string('tanggal24_in')->nullable();
            $table->string('tanggal24_out')->nullable();

            $table->integer('tanggal25_selisih')->default(0);
            $table->string('tanggal25_status', 1)->default('A');
            $table->string('tanggal25_in')->nullable();
            $table->string('tanggal25_out')->nullable();

            $table->integer('tanggal26_selisih')->default(0);
            $table->string('tanggal26_status', 1)->default('A');
            $table->string('tanggal26_in')->nullable();
            $table->string('tanggal26_out')->nullable();

            $table->integer('tanggal27_selisih')->default(0);
            $table->string('tanggal27_status', 1)->default('A');
            $table->string('tanggal27_in')->nullable();
            $table->string('tanggal27_out')->nullable();

            $table->integer('tanggal28_selisih')->default(0);
            $table->string('tanggal28_status', 1)->default('A');
            $table->string('tanggal28_in')->nullable();
            $table->string('tanggal28_out')->nullable();

            $table->integer('tanggal29_selisih')->default(0);
            $table->string('tanggal29_status', 1)->default('A');
            $table->string('tanggal29_in')->nullable();
            $table->string('tanggal29_out')->nullable();

            $table->integer('tanggal30_selisih')->default(0);
            $table->string('tanggal30_status', 1)->default('A');
            $table->string('tanggal30_in')->nullable();
            $table->string('tanggal30_out')->nullable();

            $table->integer('tanggal31_selisih')->default(0);
            $table->string('tanggal31_status', 1)->default('A');
            $table->string('tanggal31_in')->nullable();
            $table->string('tanggal31_out')->nullable();

            $table->foreign('organisasi_id')->references('id_organisasi')->on('organisasis')->restrictOnDelete();
            $table->foreign('karyawan_id')->references('id_karyawan')->on('karyawans')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
    }
};
