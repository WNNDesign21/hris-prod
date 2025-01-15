<?php

namespace App\Models\Attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanlogDetail extends Model
{
    use HasFactory;

    protected $table = 'attendance_scanlog_details_v3';

    private static function _query($dataFilter)
    {
        $sql = "
            WITH RankedScans AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (PARTITION BY karyawan, scan_date ORDER BY scan_date, scan_type) AS rn
                FROM attendance_scanlog_details_v3
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
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_OUT' THEN status_keluar END) AS \"out_status_" . $i . "\"";
                    if ($date->notEqualTo($endDate->toDateString())) {
                        $sql .= ",";
                    }
            }
            $sql .= "
            FROM DailyScans
            WHERE organisasi_id = ".$dataFilter['organisasi_id']."
            GROUP BY karyawan, pin, id_karyawan, organisasi_id, departemen_id, departemen, ni_karyawan)";

            $sql .= "
            SELECT *,
            ";
            $sumSelisih = [];
            for ($j = 1; $j <= 31; $j++) {
                $sumSelisih[] = "COALESCE(in_selisih_" . $j . ", INTERVAL '0')";
            }
            $sql .= implode(" + ", $sumSelisih) . " AS total_in_selisih
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

}
