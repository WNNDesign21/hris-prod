<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW attendance_scanlog_details AS
            WITH ActiveGroup AS (
                SELECT
                    s.id_scanlog,
                    k.pin,
                    sg.id_grup,
                    sg.nama,
                    k.jam_masuk,
                    k.jam_keluar,
                    k.toleransi_waktu,
                    k.active_date,
                    ROW_NUMBER() OVER (PARTITION BY s.pin, s.scan_date ORDER BY k.active_date DESC) AS rn,
                    CASE
                        WHEN s.scan_date BETWEEN 
                            (s.scan_date::date + sg.jam_masuk::interval - INTERVAL '2 hour') 
                            AND 
                            (s.scan_date::date + sg.jam_masuk::interval + sg.toleransi_waktu::interval + INTERVAL '5 hour') 
                        THEN 'IN'
                        ELSE 'OUT'
                    END AS scan_type
                FROM
                    attendance_scanlogs s
                INNER JOIN attendance_karyawan_grup k ON s.pin = k.pin AND k.active_date <= s.scan_date
                LEFT JOIN grups sg ON k.grup_id = sg.id_grup
            )
            SELECT
                k.ni_karyawan,
                k.id_karyawan,
                s.pin,
                ag.id_grup as grup_id,
                dv.id_divisi as divisi_id,
                dp.id_departemen as departemen_id,
                jb.id_jabatan as jabatan_id,
                k.organisasi_id as organisasi_id,
                k.nama as karyawan,
                dv.nama as divisi,
                dp.nama as departemen,
                jb.nama as jabatan,
                ag.nama as grup,
                s.scan_date,
                ag.jam_masuk as jam_masuk_active,
                ag.jam_keluar as jam_keluar_active,
                ag.toleransi_waktu,
                ag.jam_masuk::interval + EXTRACT(MINUTE FROM ag.toleransi_waktu) * INTERVAL '1 minute' AS jam_masuk_toleransi,
                ag.scan_type,
                CASE
                    WHEN ag.scan_type = 'IN' AND AGE(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + EXTRACT(MINUTE FROM ag.toleransi_waktu) * INTERVAL '1 minute')) <= INTERVAL '0' THEN 'ONTIME'
                    WHEN ag.scan_type = 'IN' THEN 'LATE'
                    ELSE NULL 
                END AS status_masuk,
                CASE 
                    WHEN ag.scan_type = 'IN' THEN
                    AGE(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + EXTRACT(MINUTE FROM ag.toleransi_waktu) * INTERVAL '1 minute'))
                    ELSE NULL
                END AS selisih_menit_masuk,
                CASE
                    WHEN ag.scan_type = 'OUT' AND AGE(s.scan_date, s.scan_date::date + ag.jam_keluar::interval) <= INTERVAL '0' THEN 'EARLY'
                    WHEN ag.scan_type = 'OUT' AND AGE(s.scan_date, s.scan_date::date + ag.jam_keluar::interval) > INTERVAL '15 minutes' THEN 'OVERTIME'
                    WHEN ag.scan_type = 'OUT' THEN 'ONTIME'
                    ELSE NULL 
                END AS status_keluar,
                CASE 
                    WHEN ag.scan_type = 'OUT' THEN
                    AGE(s.scan_date, s.scan_date::date + ag.jam_keluar::interval)
                    ELSE NULL
                END AS selisih_menit_keluar
            FROM
                attendance_scanlogs s
            LEFT JOIN karyawans k ON s.pin = k.pin
            LEFT JOIN karyawan_posisi kp ON k.id_karyawan = kp.karyawan_id
            LEFT JOIN posisis p ON kp.posisi_id = p.id_posisi
            LEFT JOIN departemens dp ON p.departemen_id = dp.id_departemen
            LEFT JOIN divisis dv ON p.divisi_id = dv.id_divisi
            LEFT JOIN jabatans jb ON p.jabatan_id = jb.id_jabatan
            LEFT JOIN ActiveGroup ag ON s.id_scanlog = ag.id_scanlog
                AND ag.rn = 1
            ORDER BY k.nama, DATE(s.scan_date);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS attendance_scanlog_details');
    }
};
