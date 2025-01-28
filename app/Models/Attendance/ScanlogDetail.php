<?php

namespace App\Models\Attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanlogDetail extends Model
{
    use HasFactory;

    // protected $table = 'attendance_scanlog_details';

    private static function _query($dataFilter)
    {
        $sql = "
            WITH AttendanceScanlogDetail AS (
                WITH karyawan_posisi_filtered AS (
                    SELECT karyawan_posisi.id,
                        karyawan_posisi.karyawan_id,
                        karyawan_posisi.posisi_id,
                        karyawan_posisi.created_at,
                        karyawan_posisi.updated_at,
                        karyawan_posisi.deleted_at,
                        row_number() OVER (PARTITION BY karyawan_posisi.karyawan_id ORDER BY karyawan_posisi.posisi_id DESC) AS rn
                    FROM karyawan_posisi
                    ), activegroup AS (
                    SELECT s_1.id_scanlog,
                        k_1.pin,
                        sg.id_grup,
                        sg.nama,
                        k_1.jam_masuk,
                        k_1.jam_keluar,
                        k_1.toleransi_waktu,
                        k_1.active_date,
                        row_number() OVER (PARTITION BY s_1.pin, s_1.scan_date ORDER BY k_1.active_date DESC) AS rn,
                            CASE
                                WHEN sg.jam_masuk <= sg.jam_keluar THEN
                                CASE
                                    WHEN s_1.scan_date >= (s_1.scan_date::date + sg.jam_masuk::interval - '02:00:00'::interval) AND s_1.scan_date <= (s_1.scan_date::date + sg.jam_masuk::interval + sg.toleransi_waktu::interval + '04:00:00'::interval) THEN 'IN'::text
                                    ELSE 'OUT'::text
                                END
                                ELSE
                                CASE
                                    WHEN to_char(s_1.scan_date, 'HH24:MI:SS'::text)::time without time zone > (to_char(sg.jam_masuk::interval, 'HH24:MI:SS'::text)::time without time zone + '02:00:00'::interval) THEN
                                    CASE
                                        WHEN s_1.scan_date >= (s_1.scan_date::date + sg.jam_masuk::interval - '02:00:00'::interval) AND s_1.scan_date <= (s_1.scan_date::date + sg.jam_masuk::interval + sg.toleransi_waktu::interval + '04:00:00'::interval) THEN 'IN'::text
                                        ELSE 'OUT'::text
                                    END
                                    ELSE
                                    CASE
                                        WHEN s_1.scan_date >= (s_1.scan_date::date - '1 day'::interval + sg.jam_masuk::interval - '02:00:00'::interval) AND s_1.scan_date <= (s_1.scan_date::date - '1 day'::interval + sg.jam_masuk::interval + sg.toleransi_waktu::interval + '04:00:00'::interval) THEN 'IN'::text
                                        ELSE 'OUT'::text
                                    END
                                END
                            END AS scan_type
                    FROM attendance_scanlogs s_1
                    JOIN attendance_karyawan_grup k_1 ON s_1.pin::text = k_1.pin::text AND k_1.active_date <= s_1.scan_date
                    LEFT JOIN grups sg ON k_1.grup_id = sg.id_grup
                    )
            SELECT k.ni_karyawan,
                k.id_karyawan,
                s.pin,
                ag.id_grup AS grup_id,
                dv.id_divisi AS divisi_id,
                dp.id_departemen AS departemen_id,
                jb.id_jabatan AS jabatan_id,
                k.organisasi_id,
                k.nama AS karyawan,
                dv.nama AS divisi,
                dp.nama AS departemen,
                jb.nama AS jabatan,
                ag.nama AS grup,
                s.scan_date,
                ag.jam_masuk AS jam_masuk_active,
                ag.jam_keluar AS jam_keluar_active,
                ag.toleransi_waktu,
                ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval AS jam_masuk_toleransi,
                ag.scan_type,
                    CASE
                        WHEN ag.jam_masuk <= ag.jam_keluar THEN
                        CASE
                            WHEN ag.scan_type = 'IN'::text AND age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) <= '00:00:00'::interval THEN 'ONTIME'::text
                            WHEN ag.scan_type = 'IN'::text THEN 'LATE'::text
                            ELSE NULL::text
                        END
                        ELSE
                        CASE
                            WHEN to_char(s.scan_date, 'HH24:MI:SS'::text)::time without time zone > (to_char(ag.jam_masuk::interval, 'HH24:MI:SS'::text)::time without time zone + '02:00:00'::interval) THEN
                            CASE
                                WHEN ag.scan_type = 'IN'::text AND age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) <= '00:00:00'::interval THEN 'ONTIME'::text
                                WHEN ag.scan_type = 'IN'::text THEN 'LATE'::text
                                ELSE NULL::text
                            END
                            ELSE
                            CASE
                                WHEN ag.scan_type = 'IN'::text AND (age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) + '24:00:00'::interval) <= '00:00:00'::interval THEN 'ONTIME'::text
                                WHEN ag.scan_type = 'IN'::text THEN 'LATE'::text
                                ELSE NULL::text
                            END
                        END
                    END AS status_masuk,
                    CASE
                        WHEN ag.jam_masuk <= ag.jam_keluar THEN
                        CASE
                            WHEN ag.scan_type = 'IN'::text THEN age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval))
                            ELSE NULL::interval
                        END
                        ELSE
                        CASE
                            WHEN to_char(s.scan_date, 'HH24:MI:SS'::text)::time without time zone > (to_char(ag.jam_masuk::interval, 'HH24:MI:SS'::text)::time without time zone + '02:00:00'::interval) THEN
                            CASE
                                WHEN ag.scan_type = 'IN'::text THEN age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval))
                                ELSE NULL::interval
                            END
                            ELSE
                            CASE
                                WHEN ag.scan_type = 'IN'::text THEN age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) + '24:00:00'::interval
                                ELSE NULL::interval
                            END
                        END
                    END AS selisih_menit_masuk,
                    CASE
                        WHEN ag.jam_keluar = '16:30:00'::time without time zone AND date_part('dow'::text, s.scan_date::date) = 5::numeric::double precision THEN
                        CASE
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + '16:00:00'::interval) <= '00:00:00'::interval THEN 'EARLY'::text
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + '16:00:00'::interval) > '00:15:00'::interval THEN 'OVERTIME'::text
                            WHEN ag.scan_type = 'OUT'::text THEN 'ONTIME'::text
                            ELSE NULL::text
                        END
                        ELSE
                        CASE
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + ag.jam_keluar::interval) <= '00:00:00'::interval THEN 'EARLY'::text
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + ag.jam_keluar::interval) > '00:15:00'::interval THEN 'OVERTIME'::text
                            WHEN ag.scan_type = 'OUT'::text THEN 'ONTIME'::text
                            ELSE NULL::text
                        END
                    END AS status_keluar,
                    CASE
                        WHEN ag.scan_type = 'OUT'::text THEN age(s.scan_date, s.scan_date::date + ag.jam_keluar::interval)
                        ELSE NULL::interval
                    END AS selisih_menit_keluar
            FROM attendance_scanlogs s
                LEFT JOIN karyawans k ON s.pin::text = k.pin::text AND s.organisasi_id = ".$dataFilter['organisasi_id']."
                LEFT JOIN karyawan_posisi_filtered kpf ON k.id_karyawan::text = kpf.karyawan_id::text AND kpf.rn = 1
                LEFT JOIN posisis p ON kpf.posisi_id = p.id_posisi
                LEFT JOIN departemens dp ON p.departemen_id = dp.id_departemen
                LEFT JOIN divisis dv ON p.divisi_id = dv.id_divisi
                LEFT JOIN jabatans jb ON p.jabatan_id = jb.id_jabatan
                LEFT JOIN activegroup ag ON s.id_scanlog = ag.id_scanlog AND ag.rn = 1
            ORDER BY k.nama, (date(s.scan_date))
            ), 
            RankedScans AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (PARTITION BY karyawan, organisasi_id, scan_date ORDER BY scan_date, scan_type) AS rn
                FROM AttendanceScanlogDetail
            ),
            DailyScans AS (
                SELECT
                    karyawan,
                    organisasi_id,
                    id_karyawan,
                    ni_karyawan,
                    departemen_id,
                    departemen,
                    pin,
                    scan_date,
                    status_masuk,
                    status_keluar,
                    selisih_menit_masuk,
                    selisih_menit_keluar,
                    CASE WHEN scan_type = 'IN' AND EXTRACT(HOUR FROM scan_date) >= 22 THEN scan_date + INTERVAL '1 day' ELSE scan_date END AS adjusted_date,
                    scan_type,
                    CASE WHEN rn = 1 THEN '1_' ELSE '2_' END || scan_type AS scan_column
                FROM RankedScans
                WHERE organisasi_id = ".$dataFilter['organisasi_id']."
            ),
            AggregatedData AS (SELECT
                karyawan,
                id_karyawan,
                ni_karyawan,
                organisasi_id,
                departemen,
                departemen_id,
                pin,";
    
            $startDate = Carbon::createFromFormat('Y-m', $dataFilter['periode'])->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $dataFilter['periode'])->endOfMonth();

            $i = 0;
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $i++;
                $sql .= "
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_IN' THEN CAST(EXTRACT(HOUR FROM adjusted_date) AS TEXT) || ':' || LPAD(EXTRACT(MINUTE FROM adjusted_date)::TEXT, 2, '0') END) AS \"in_" . $i . "\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_IN' THEN status_masuk END) AS \"in_status_" . $i . "\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_IN' AND selisih_menit_masuk > INTERVAL '0' THEN selisih_menit_masuk ELSE INTERVAL '0' END) AS \"in_selisih_" . $i . "\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_OUT' THEN CAST(EXTRACT(HOUR FROM adjusted_date) AS TEXT) || ':' || LPAD(EXTRACT(MINUTE FROM adjusted_date)::TEXT, 2, '0') END) AS \"out_" . $i . "\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_OUT' THEN status_keluar END) AS \"out_status_" . $i . "\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND (scan_column = '1_IN' OR scan_column = '1_OUT') THEN 1 ELSE 0 END) AS \"kehadiran_" . $i . "\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_OUT' AND selisih_menit_keluar > INTERVAL '0' THEN selisih_menit_keluar ELSE INTERVAL '0' END) AS \"out_selisih_" . $i . "\"";
                    if ($date->notEqualTo($endDate->toDateString())) {
                        $sql .= ",";
                    }
            }
            $sql .= "
            FROM DailyScans
            WHERE organisasi_id = ".$dataFilter['organisasi_id']."
            GROUP BY karyawan, pin, id_karyawan, organisasi_id, departemen_id, departemen, ni_karyawan)";

            // $sql .= "
            // SELECT *,
            // ";
            // $sumSelisihMasuk = [];
            // $sumSelisihKeluar = [];
            // for ($j = 1; $j <= 31; $j++) {
            //     $sumSelisihMasuk[] = "COALESCE(in_selisih_" . $j . ", INTERVAL '0')";
            //     $sumSelisihKeluar[] = "COALESCE(out_selisih_" . $j . ", INTERVAL '0')";
            // }
            // $sql .= implode(" + ", $sumSelisihMasuk) . " AS total_in_selisih
            // , " . implode(" + ", $sumSelisihKeluar) . " AS total_out_selisih
            // FROM AggregatedData
            // ORDER BY karyawan, pin, organisasi_id, departemen_id, departemen, ni_karyawan
            // ";

            // $results = DB::table(DB::raw("($sql) as sub"));
            // return $results;
            $sql .= "
            SELECT *,
            ";

            $sumKehadiran = [];
            for ($j = 1; $j <= 31; $j++) {
                $sumKehadiran[] = "kehadiran_" . $j;
            }

            $sql .= implode(" + ", $sumKehadiran) . " AS total_kehadiran,
                ";

            $sumSelisihMasuk = [];
            $sumSelisihKeluar = [];
            for ($j = 1; $j <= 31; $j++) {
                $sumSelisihMasuk[] = "COALESCE(in_selisih_" . $j . ", INTERVAL '0')";
                $sumSelisihKeluar[] = "COALESCE(out_selisih_" . $j . ", INTERVAL '0')";
            }
            $sql .= implode(" + ", $sumSelisihMasuk) . " AS total_in_selisih
                , " . implode(" + ", $sumSelisihKeluar) . " AS total_out_selisih
                FROM AggregatedData
                ORDER BY karyawan, pin, organisasi_id, departemen_id, departemen, ni_karyawan
                ";

            $results = DB::table(DB::raw("($sql) as sub"));
            return $results;
    }

    public static function getPresensiPerbulan($dataFilter, $settings)
    {
        $results = self::_query($dataFilter); 

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $results->where(function ($query) use ($search) {
                $query->where('karyawan', 'ILIKE', "%{$search}%");
                $query->orWhere('pin', 'ILIKE', "%{$search}%");
                $query->orWhere('departemen', 'ILIKE', "%{$search}%");
            });
        }

        if (isset($dataFilter['departemen'])) {
            $results->whereIn('departemen_id', $dataFilter['departemen']);
        }
        
        return $results->offset($settings['start']) 
            ->limit($settings['limit']) 
            ->get();
    }

    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->get()->count();
    }

    public static function getHadirByDate($dataFilter)
    {
        $sql = "
            WITH AttendanceScanlogDetail AS (
                WITH karyawan_posisi_filtered AS (
                    SELECT karyawan_posisi.id,
                        karyawan_posisi.karyawan_id,
                        karyawan_posisi.posisi_id,
                        karyawan_posisi.created_at,
                        karyawan_posisi.updated_at,
                        karyawan_posisi.deleted_at,
                        row_number() OVER (PARTITION BY karyawan_posisi.karyawan_id ORDER BY karyawan_posisi.posisi_id DESC) AS rn
                    FROM karyawan_posisi
                    ), activegroup AS (
                    SELECT s_1.id_scanlog,
                        k_1.pin,
                        sg.id_grup,
                        sg.nama,
                        k_1.jam_masuk,
                        k_1.jam_keluar,
                        k_1.toleransi_waktu,
                        k_1.active_date,
                        row_number() OVER (PARTITION BY s_1.pin, s_1.scan_date ORDER BY k_1.active_date DESC) AS rn,
                            CASE
                                WHEN sg.jam_masuk <= sg.jam_keluar THEN
                                CASE
                                    WHEN s_1.scan_date >= (s_1.scan_date::date + sg.jam_masuk::interval - '02:00:00'::interval) AND s_1.scan_date <= (s_1.scan_date::date + sg.jam_masuk::interval + sg.toleransi_waktu::interval + '04:00:00'::interval) THEN 'IN'::text
                                    ELSE 'OUT'::text
                                END
                                ELSE
                                CASE
                                    WHEN to_char(s_1.scan_date, 'HH24:MI:SS'::text)::time without time zone > (to_char(sg.jam_masuk::interval, 'HH24:MI:SS'::text)::time without time zone + '02:00:00'::interval) THEN
                                    CASE
                                        WHEN s_1.scan_date >= (s_1.scan_date::date + sg.jam_masuk::interval - '02:00:00'::interval) AND s_1.scan_date <= (s_1.scan_date::date + sg.jam_masuk::interval + sg.toleransi_waktu::interval + '04:00:00'::interval) THEN 'IN'::text
                                        ELSE 'OUT'::text
                                    END
                                    ELSE
                                    CASE
                                        WHEN s_1.scan_date >= (s_1.scan_date::date - '1 day'::interval + sg.jam_masuk::interval - '02:00:00'::interval) AND s_1.scan_date <= (s_1.scan_date::date - '1 day'::interval + sg.jam_masuk::interval + sg.toleransi_waktu::interval + '04:00:00'::interval) THEN 'IN'::text
                                        ELSE 'OUT'::text
                                    END
                                END
                            END AS scan_type
                    FROM attendance_scanlogs s_1
                    JOIN attendance_karyawan_grup k_1 ON s_1.pin::text = k_1.pin::text AND k_1.active_date <= s_1.scan_date
                    LEFT JOIN grups sg ON k_1.grup_id = sg.id_grup
                    )
            SELECT k.ni_karyawan,
                k.id_karyawan,
                s.pin,
                ag.id_grup AS grup_id,
                dv.id_divisi AS divisi_id,
                dp.id_departemen AS departemen_id,
                jb.id_jabatan AS jabatan_id,
                k.organisasi_id,
                k.nama AS karyawan,
                dv.nama AS divisi,
                dp.nama AS departemen,
                jb.nama AS jabatan,
                ag.nama AS grup,
                s.scan_date,
                ag.jam_masuk AS jam_masuk_active,
                ag.jam_keluar AS jam_keluar_active,
                ag.toleransi_waktu,
                ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval AS jam_masuk_toleransi,
                ag.scan_type,
                    CASE
                        WHEN ag.jam_masuk <= ag.jam_keluar THEN
                        CASE
                            WHEN ag.scan_type = 'IN'::text AND age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) <= '00:00:00'::interval THEN 'ONTIME'::text
                            WHEN ag.scan_type = 'IN'::text THEN 'LATE'::text
                            ELSE NULL::text
                        END
                        ELSE
                        CASE
                            WHEN to_char(s.scan_date, 'HH24:MI:SS'::text)::time without time zone > (to_char(ag.jam_masuk::interval, 'HH24:MI:SS'::text)::time without time zone + '02:00:00'::interval) THEN
                            CASE
                                WHEN ag.scan_type = 'IN'::text AND age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) <= '00:00:00'::interval THEN 'ONTIME'::text
                                WHEN ag.scan_type = 'IN'::text THEN 'LATE'::text
                                ELSE NULL::text
                            END
                            ELSE
                            CASE
                                WHEN ag.scan_type = 'IN'::text AND (age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) + '24:00:00'::interval) <= '00:00:00'::interval THEN 'ONTIME'::text
                                WHEN ag.scan_type = 'IN'::text THEN 'LATE'::text
                                ELSE NULL::text
                            END
                        END
                    END AS status_masuk,
                    CASE
                        WHEN ag.jam_masuk <= ag.jam_keluar THEN
                        CASE
                            WHEN ag.scan_type = 'IN'::text THEN age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval))
                            ELSE NULL::interval
                        END
                        ELSE
                        CASE
                            WHEN to_char(s.scan_date, 'HH24:MI:SS'::text)::time without time zone > (to_char(ag.jam_masuk::interval, 'HH24:MI:SS'::text)::time without time zone + '02:00:00'::interval) THEN
                            CASE
                                WHEN ag.scan_type = 'IN'::text THEN age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval))
                                ELSE NULL::interval
                            END
                            ELSE
                            CASE
                                WHEN ag.scan_type = 'IN'::text THEN age(s.scan_date, s.scan_date::date + (ag.jam_masuk::interval + date_part('minute'::text, ag.toleransi_waktu) * '00:01:00'::interval)) + '24:00:00'::interval
                                ELSE NULL::interval
                            END
                        END
                    END AS selisih_menit_masuk,
                    CASE
                        WHEN ag.jam_keluar = '16:30:00'::time without time zone AND date_part('dow'::text, s.scan_date::date) = 5::numeric::double precision THEN
                        CASE
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + '16:00:00'::interval) <= '00:00:00'::interval THEN 'EARLY'::text
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + '16:00:00'::interval) > '00:15:00'::interval THEN 'OVERTIME'::text
                            WHEN ag.scan_type = 'OUT'::text THEN 'ONTIME'::text
                            ELSE NULL::text
                        END
                        ELSE
                        CASE
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + ag.jam_keluar::interval) <= '00:00:00'::interval THEN 'EARLY'::text
                            WHEN ag.scan_type = 'OUT'::text AND age(s.scan_date, s.scan_date::date + ag.jam_keluar::interval) > '00:15:00'::interval THEN 'OVERTIME'::text
                            WHEN ag.scan_type = 'OUT'::text THEN 'ONTIME'::text
                            ELSE NULL::text
                        END
                    END AS status_keluar,
                    CASE
                        WHEN ag.scan_type = 'OUT'::text THEN age(s.scan_date, s.scan_date::date + ag.jam_keluar::interval)
                        ELSE NULL::interval
                    END AS selisih_menit_keluar
            FROM attendance_scanlogs s
                LEFT JOIN karyawans k ON s.pin::text = k.pin::text AND s.organisasi_id = ".$dataFilter['organisasi_id']."
                LEFT JOIN karyawan_posisi_filtered kpf ON k.id_karyawan::text = kpf.karyawan_id::text AND kpf.rn = 1
                LEFT JOIN posisis p ON kpf.posisi_id = p.id_posisi
                LEFT JOIN departemens dp ON p.departemen_id = dp.id_departemen
                LEFT JOIN divisis dv ON p.divisi_id = dv.id_divisi
                LEFT JOIN jabatans jb ON p.jabatan_id = jb.id_jabatan
                LEFT JOIN activegroup ag ON s.id_scanlog = ag.id_scanlog AND ag.rn = 1
            ORDER BY k.nama, (date(s.scan_date))
            ), 
            RankedScans AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (PARTITION BY karyawan, organisasi_id, scan_date ORDER BY scan_date, scan_type) AS rn
                FROM AttendanceScanlogDetail
            ),
            DailyScans AS (
                SELECT
                    karyawan,
                    organisasi_id,
                    id_karyawan,
                    ni_karyawan,
                    departemen_id,
                    departemen,
                    pin,
                    scan_date,
                    status_masuk,
                    status_keluar,
                    selisih_menit_masuk,
                    selisih_menit_keluar,
                    CASE WHEN scan_type = 'IN' AND EXTRACT(HOUR FROM scan_date) >= 22 THEN scan_date + INTERVAL '1 day' ELSE scan_date END AS adjusted_date,
                    scan_type,
                    CASE WHEN rn = 1 THEN '1_' ELSE '2_' END || scan_type AS scan_column
                FROM RankedScans
            )
            SELECT
                karyawan as nama,
                pin,
                id_karyawan,
                organisasi_id,
                departemen_id,
                departemen,
                ni_karyawan
            FROM DailyScans
            WHERE organisasi_id = ".$dataFilter['organisasi_id']."
            AND DATE(adjusted_date) = '".$dataFilter['date']."'
            AND scan_column = '1_IN'"
        ;

        if (isset($dataFilter['departemen'])) {
            $sql .= " AND departemen_id IN (".implode(',', $dataFilter['departemen']).")";
        }

        $sql .= "
            GROUP BY karyawan, pin, id_karyawan, organisasi_id, departemen_id, departemen, ni_karyawan
            ORDER BY departemen, karyawan  ASC
        ";

        return DB::select($sql);
    }

    public static function getHadirCountByDate($dataFilter)
    {
        return count(self::getHadirByDate($dataFilter));
    }
}
