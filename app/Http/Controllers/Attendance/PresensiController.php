<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance\ScanlogDetail;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Attendance-E - Presensi",
            'page' => 'attendance-presensi',
            'departemens' => $departemens 
        ];
        return view('pages.attendance-e.presensi.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $limit = $request->input('length');
        $start = $request->input('start');
        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $dataFilter = [];


        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }
        $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
        $dataFilter['periode'] = '2025-01';
        $presensis = ScanlogDetail::getPresensiPerbulan($dataFilter, $settings);
        $totalFiltered = ScanlogDetail::countData($dataFilter);
        $totalData = ScanlogDetail::getPresensiPerbulan($dataFilter, $settings)->count();

        $dataTable = [];
        if (!empty($presensis)) {
            foreach ($presensis as $data) {
                $nestedData['ni_karyawan'] = $data?->ni_karyawan;
                $nestedData['karyawan'] = $data?->karyawan;
                $nestedData['departemen'] = $data?->departemen;
                $nestedData['pin'] = $data?->pin;
                $nestedData['in_1'] = $data?->in_1;
                $nestedData['in_status_1'] = $data?->in_status_1;
                $nestedData['out_1'] = $data?->out_1;
                $nestedData['out_status_1'] = $data?->out_status_1;
                $nestedData['in_2'] = $data?->in_2;
                $nestedData['in_status_2'] = $data?->in_status_2;
                $nestedData['out_2'] = $data?->out_2;
                $nestedData['out_status_2'] = $data?->out_status_2;
                $nestedData['in_3'] = $data?->in_3;
                $nestedData['in_status_3'] = $data?->in_status_3;
                $nestedData['out_3'] = $data?->out_3;
                $nestedData['out_status_3'] = $data?->out_status_3;
                $nestedData['in_4'] = $data?->in_4;
                $nestedData['in_status_4'] = $data?->in_status_4;
                $nestedData['out_4'] = $data?->out_4;
                $nestedData['out_status_4'] = $data?->out_status_4;
                $nestedData['in_5'] = $data?->in_5;
                $nestedData['in_status_5'] = $data?->in_status_5;
                $nestedData['out_5'] = $data?->out_5;
                $nestedData['out_status_5'] = $data?->out_status_5;
                $nestedData['in_6'] = $data?->in_6;
                $nestedData['in_status_6'] = $data?->in_status_6;
                $nestedData['out_6'] = $data?->out_6;
                $nestedData['out_status_6'] = $data?->out_status_6;
                $nestedData['in_7'] = $data?->in_7;
                $nestedData['in_status_7'] = $data?->in_status_7;
                $nestedData['out_7'] = $data?->out_7;
                $nestedData['out_status_7'] = $data?->out_status_7;
                $nestedData['in_8'] = $data?->in_8;
                $nestedData['in_status_8'] = $data?->in_status_8;
                $nestedData['out_8'] = $data?->out_8;
                $nestedData['out_status_8'] = $data?->out_status_8;
                $nestedData['in_9'] = $data?->in_9;
                $nestedData['in_status_9'] = $data?->in_status_9;
                $nestedData['out_9'] = $data?->out_9;
                $nestedData['out_status_9'] = $data?->out_status_9;
                $nestedData['in_10'] = $data?->in_10;
                $nestedData['in_status_10'] = $data?->in_status_10;
                $nestedData['out_10'] = $data?->out_10;
                $nestedData['out_status_10'] = $data?->out_status_10;
                $nestedData['in_11'] = $data?->in_11;
                $nestedData['in_status_11'] = $data?->in_status_11;
                $nestedData['out_11'] = $data?->out_11;
                $nestedData['out_status_11'] = $data?->out_status_11;
                $nestedData['in_12'] = $data?->in_12;
                $nestedData['in_status_12'] = $data?->in_status_12;
                $nestedData['out_12'] = $data?->out_12;
                $nestedData['out_status_12'] = $data?->out_status_12;
                $nestedData['in_13'] = $data?->in_13;
                $nestedData['in_status_13'] = $data?->in_status_13;
                $nestedData['out_13'] = $data?->out_13;
                $nestedData['out_status_13'] = $data?->out_status_13;
                $nestedData['in_14'] = $data?->in_14;
                $nestedData['in_status_14'] = $data?->in_status_14;
                $nestedData['out_14'] = $data?->out_14;
                $nestedData['out_status_14'] = $data?->out_status_14;
                $nestedData['in_15'] = $data?->in_15;
                $nestedData['in_status_15'] = $data?->in_status_15;
                $nestedData['out_15'] = $data?->out_15;
                $nestedData['out_status_15'] = $data?->out_status_15;
                $nestedData['in_16'] = $data?->in_16;
                $nestedData['in_status_16'] = $data?->in_status_16;
                $nestedData['out_16'] = $data?->out_16;
                $nestedData['out_status_16'] = $data?->out_status_16;
                $nestedData['in_17'] = $data?->in_17;
                $nestedData['in_status_17'] = $data?->in_status_17;
                $nestedData['out_17'] = $data?->out_17;
                $nestedData['out_status_17'] = $data?->out_status_17;
                $nestedData['in_18'] = $data?->in_18;
                $nestedData['in_status_18'] = $data?->in_status_18;
                $nestedData['out_18'] = $data?->out_18;
                $nestedData['out_status_18'] = $data?->out_status_18;
                $nestedData['in_19'] = $data?->in_19;
                $nestedData['in_status_19'] = $data?->in_status_19;
                $nestedData['out_19'] = $data?->out_19;
                $nestedData['out_status_19'] = $data?->out_status_19;
                $nestedData['in_20'] = $data?->in_20;
                $nestedData['in_status_20'] = $data?->in_status_20;
                $nestedData['out_20'] = $data?->out_20;
                $nestedData['out_status_20'] = $data?->out_status_20;
                $nestedData['in_21'] = $data?->in_21;
                $nestedData['in_status_21'] = $data?->in_status_21;
                $nestedData['out_21'] = $data?->out_21;
                $nestedData['out_status_21'] = $data?->out_status_21;
                $nestedData['in_22'] = $data?->in_22;
                $nestedData['in_status_22'] = $data?->in_status_22;
                $nestedData['out_22'] = $data?->out_22;
                $nestedData['out_status_22'] = $data?->out_status_22;
                $nestedData['in_23'] = $data?->in_23;
                $nestedData['in_status_23'] = $data?->in_status_23;
                $nestedData['out_23'] = $data?->out_23;
                $nestedData['out_status_23'] = $data?->out_status_23;
                $nestedData['in_24'] = $data?->in_24;
                $nestedData['in_status_24'] = $data?->in_status_24;
                $nestedData['out_24'] = $data?->out_24;
                $nestedData['out_status_24'] = $data?->out_status_24;
                $nestedData['in_25'] = $data?->in_25;
                $nestedData['in_status_25'] = $data?->in_status_25;
                $nestedData['out_25'] = $data?->out_25;
                $nestedData['out_status_25'] = $data?->out_status_25;
                $nestedData['in_26'] = $data?->in_26;
                $nestedData['in_status_26'] = $data?->in_status_26;
                $nestedData['out_26'] = $data?->out_26;
                $nestedData['out_status_26'] = $data?->out_status_26;
                $nestedData['in_27'] = $data?->in_27;
                $nestedData['in_status_27'] = $data?->in_status_27;
                $nestedData['out_27'] = $data?->out_27;
                $nestedData['out_status_27'] = $data?->out_status_27;
                $nestedData['in_28'] = $data?->in_28;
                $nestedData['in_status_28'] = $data?->in_status_28;
                $nestedData['out_28'] = $data?->out_28;
                $nestedData['out_status_28'] = $data?->out_status_28;
                $nestedData['in_29'] = $data?->in_29;
                $nestedData['in_status_29'] = $data?->in_status_29;
                $nestedData['out_29'] = $data?->out_29;
                $nestedData['out_status_29'] = $data?->out_status_29;
                $nestedData['in_30'] = $data?->in_30;
                $nestedData['in_status_30'] = $data?->in_status_30;
                $nestedData['out_30'] = $data?->out_30;
                $nestedData['out_status_30'] = $data?->out_status_30;
                $nestedData['in_31'] = $data?->in_31;
                $nestedData['in_status_31'] = $data?->in_status_31;
                $nestedData['out_31'] = $data?->out_31;
                $nestedData['out_status_31'] = $data?->out_status_31;

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            // "order" => $order,
            "statusFilter" => !empty($dataFilter['statusFilter']) ? $dataFilter['statusFilter'] : "Kosong",
            // "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function get_presensi_per_bulan($periode)
    {
        try {
            $sql = "
            WITH RankedScans AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (PARTITION BY karyawan, scan_date ORDER BY scan_date, scan_type) AS rn
                FROM attendance_scanlog_details
            ),
            DailyScans AS (
                SELECT
                    karyawan,
                    pin,
                    scan_date,
                    status_masuk,
                    status_keluar,
                    CASE WHEN scan_type = 'IN' AND EXTRACT(HOUR FROM scan_date) >= 22 THEN scan_date + INTERVAL '1 day' ELSE scan_date END AS adjusted_date,
                    scan_type,
                    CASE WHEN rn = 1 THEN '1_' ELSE '2_' END || scan_type AS scan_column
                FROM RankedScans
            )
            SELECT
                karyawan,
                pin,";
    
            $startDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $periode)->endOfMonth();

            $i = 0;
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $i++;
                $sql .= "
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_IN' THEN CAST(EXTRACT(HOUR FROM adjusted_date) AS TEXT) || ':' || LPAD(EXTRACT(MINUTE FROM adjusted_date)::TEXT, 2, '0') END) AS \"" . $i . "_in\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_IN' THEN status_masuk END) AS \"" . $i . "_in_status\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_OUT' THEN CAST(EXTRACT(HOUR FROM adjusted_date) AS TEXT) || ':' || LPAD(EXTRACT(MINUTE FROM adjusted_date)::TEXT, 2, '0') END) AS \"" . $i . "_out\",
                    MAX(CASE WHEN DATE(adjusted_date) = '" . $date->toDateString() . "' AND scan_column = '1_OUT' THEN status_keluar END) AS \"" . $i . "_out_status\"";
                    if ($date->notEqualTo($endDate->toDateString())) {
                        $sql .= ",";
                    }
            }
            $sql .= "
            FROM DailyScans
            GROUP BY karyawan, pin
            ORDER BY karyawan, pin;";
    
            $results = DB::select($sql);
            return response()->json(['message' => 'Data Presensi Per Bulan Berhasil Ditemukan', 'data' => $results], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
