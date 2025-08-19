<?php

namespace App\Http\Controllers\Attendance;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Izine;
use App\Models\Sakite;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance\KaryawanGrup;
use App\Models\Attendance\ScanlogDetail;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance\AttendanceSummary;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $dataFilter = [];
        // $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
        // $dataFilter['date'] = Carbon::now()->format('Y-m-d');
        // $dataFilter['jenis_izin'] = ['TM'];
        // $dataFilter['statusCuti'] = 'ON LEAVE';
        // $dataFilter['statusKaryawan'] = 'AT';

        if (auth()->user()->hasRole('personalia')) {
            $departemens = Departemen::all();
        } else {
            $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
            $departemens = Departemen::where('id_departemen', $departemen)->pluck('id_departemen')->toArray();
            $dataFilter['departemens'] = $departemens;
        }

        // $hadir = ScanlogDetail::getHadirCountByDate($dataFilter);
        // $sakit = Sakite::countData($dataFilter);
        // $izin = Izine::countData($dataFilter);
        // $cuti = Cutie::countData($dataFilter);
        // $total_karyawan = Karyawan::countData($dataFilter);
        $dataPage = [
            'pageTitle' => "Attendance-E - Presensi",
            'page' => 'attendance-presensi',
            'departemens' => $departemens,
            // 'hadir' => $hadir,
            // 'sakit' => $sakit,
            // 'izin' => $izin,
            // 'cuti' => $cuti,
            // 'total_karyawan' => $total_karyawan,
        ];
        return view('pages.attendance-e.presensi.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'karyawans.nama',
            1 => 'departemens.nama',
            2 => 'attendance_summaries.periode',
            3 => 'menit_keterlambatan',
        );

        $totalData = AttendanceSummary::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        if (auth()->user()->hasAnyRole(['admin-dept', 'personalia'])) {
            $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
        }

        $departemen = $request->departemen;
        if (!empty($departemen)) {
            $dataFilter['departemens'] = $departemen;
        } else {
            if (auth()->user()->hasRole('admin-dept')) {
                $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
                $dataFilter['departemens'] = [$departemen];
            } elseif (auth()->user()->hasRole('atasan')) {
                $posisis = auth()->user()->karyawan->posisi;
                $memberPosisi = Approval::GetMemberPosisi($posisis);
                $karyawanDistinct = Karyawan::leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
                    ->whereIn('karyawan_posisi.posisi_id', $memberPosisi)
                    ->distinct()
                    ->pluck('karyawan_posisi.karyawan_id')->toArray();
                $dataFilter['karyawan_ids'] = $karyawanDistinct;
            }
        }

        $periode = $request->periode;
        if (!empty($periode)) {
            $dataFilter['periode'] = Carbon::createFromFormat('Y-m', $periode)->format('Y-m');
            $dataFilter['year'] = Carbon::createFromFormat('Y-m', $periode)->format('Y');
            $dataFilter['month'] = Carbon::createFromFormat('Y-m', $periode)->format('m');
        } else {
            $dataFilter['periode'] = Carbon::now()->format('Y-m');
            $dataFilter['year'] = Carbon::now()->format('Y');
            $dataFilter['month'] = Carbon::now()->format('m');
        }

        $summaries = AttendanceSummary::getData($dataFilter, $settings);
        $totalFiltered = AttendanceSummary::countData($dataFilter);

        $dataTable = [];

        if (!empty($summaries)) {
            foreach ($summaries as $data) {
                $nestedData['karyawan'] = $data?->karyawan;
                $nestedData['departemen'] = $data?->departemen;
                $nestedData['periode'] = Carbon::createFromFormat('Y-m-d', $data?->periode)->format('F Y');
                $nestedData['menit_keterlambatan'] = $data?->menit_keterlambatan . ' Menit';

                for ($i = 1; $i <= 28; $i++) {
                    if ($data->{"tanggal{$i}_status"} == 'H') {
                        $nestedData["in_$i"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">' . ($data?->{"tanggal{$i}_in"} ?? 'UNDEFINED') . '</button>';
                        $nestedData["out_$i"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">' . ($data?->{"tanggal{$i}_out"} ?? 'UNDEFINED') . '</button>';
                    } elseif ($data->{"tanggal{$i}_status"} == 'S') {
                        $nestedData["in_$i"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                        $nestedData["out_$i"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                    } elseif ($data->{"tanggal{$i}_status"} == 'I') {
                        $nestedData["in_$i"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                        $nestedData["out_$i"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                    } elseif ($data->{"tanggal{$i}_status"} == 'C') {
                        $nestedData["in_$i"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                        $nestedData["out_$i"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                    } else {
                        $nestedData["in_$i"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">Absen</button>';
                        $nestedData["out_$i"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">Absen</button>';
                    }

                    $nestedData["in_status_$i"] = $data?->{"tanggal{$i}_selisih"} > 0 ? 'LATE' : '';
                    $nestedData["out_status_$i"] = $data?->{"tanggal{$i}_status"};
                }

                // KONDISI UNTUK BULAN YANG MEMILIKI TANGGAL 29, 30, 31
                $daysInMonth = Carbon::createFromFormat('Y-m', $dataFilter['periode'])->daysInMonth;
                if ($daysInMonth >= 29) {
                    if ($data->{"tanggal29_status"} == 'H') {
                        $nestedData["in_29"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '"  data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">' . $data->tanggal29_in . '</button>';
                        $nestedData["out_29"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '"  data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">' . $data->tanggal29_out . '</button>';
                    } elseif ($data->{"tanggal29_status"} == 'S') {
                        $nestedData["in_29"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                        $nestedData["out_29"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                    } elseif ($data->{"tanggal29_status"} == 'I') {
                        $nestedData["in_29"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                        $nestedData["out_29"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                    } elseif ($data->{"tanggal29_status"} == 'C') {
                        $nestedData["in_29"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                        $nestedData["out_29"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                    } else {
                        $nestedData["in_29"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">Absen</button>';
                        $nestedData["out_29"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">Absen</button>';
                    }

                    $nestedData["in_status_29"] = $data?->tanggal29_selisih > 0 ? 'LATE' : '';
                    $nestedData["out_status_29"] = $data?->tanggal29_status;
                } else {
                    $nestedData['in_29'] = '';
                    $nestedData['in_status_29'] = '';
                    $nestedData['out_29'] = '';
                    $nestedData['out_status_29'] = '';
                }

                if ($daysInMonth >= 30) {
                    if ($data->{"tanggal30_status"} == 'H') {
                        $nestedData["in_30"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">' . $data->tanggal30_in . '</button>';
                        $nestedData["out_30"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">' . $data->tanggal30_out . '</button>';
                    } elseif ($data->{"tanggal30_status"} == 'S') {
                        $nestedData["in_30"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                        $nestedData["out_30"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                    } elseif ($data->{"tanggal30_status"} == 'I') {
                        $nestedData["in_30"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                        $nestedData["out_30"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                    } elseif ($data->{"tanggal30_status"} == 'C') {
                        $nestedData["in_30"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                        $nestedData["out_30"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                    } else {
                        $nestedData["in_30"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">Absen</button>';
                        $nestedData["out_30"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">Absen</button>';
                    }
                    $nestedData["in_status_30"] = $data?->tanggal30_selisih > 0 ? 'LATE' : '';
                    $nestedData["out_status_30"] = $data?->tanggal30_status;
                } else {
                    $nestedData['in_30'] = '';
                    $nestedData['in_status_30'] = '';
                    $nestedData['out_30'] = '';
                    $nestedData['out_status_30'] = '';
                }

                if ($daysInMonth == 31) {
                    if ($data->{"tanggal31_status"} == 'H') {
                        $nestedData["in_31"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="IN">' . $data->tanggal31_in . '</button>';
                        $nestedData["out_31"] = '<button class="btn btn-sm btn-success btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '" data-type="OUT">' . $data->tanggal31_out . '</button>';
                    } elseif ($data->{"tanggal31_status"} == 'S') {
                        $nestedData["in_31"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                        $nestedData["out_31"] = '<button class="btn btn-sm btn-dark btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Sakit</button>';
                    } elseif ($data->{"tanggal31_status"} == 'I') {
                        $nestedData["in_31"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                        $nestedData["out_31"] = '<button class="btn btn-sm btn-primary btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Izin</button>';
                    } elseif ($data->{"tanggal31_status"} == 'C') {
                        $nestedData["in_31"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                        $nestedData["out_31"] = '<button class="btn btn-sm btn-info btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Cuti</button>';
                    } else {
                        $nestedData["in_31"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Absen</button>';
                        $nestedData["out_31"] = '<button class="btn btn-sm btn-danger btnCheck" data-id="' . $data->id_att_summary . '" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->karyawan_id . '" data-pin="' . $data?->pin . '">Absen</button>';
                    }

                    $nestedData["in_status_31"] = $data?->tanggal31_selisih > 0 ? 'LATE' : '';
                    $nestedData["out_status_31"] = $data?->tanggal31_status;
                } else {
                    $nestedData['in_31'] = '';
                    $nestedData['in_status_31'] = '';
                    $nestedData['out_31'] = '';
                    $nestedData['out_status_31'] = '';
                }

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "statusFilter" => !empty($dataFilter['statusFilter']) ? $dataFilter['statusFilter'] : "Kosong",
            "dir" => $dir,
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

    public function get_summary_presensi_html(Request $request)
    {
        $dataValidate = [
            'tanggal' => ['required', 'date_format:Y-m-d'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $departemen = $request->departemen;
        $tanggal = $request->tanggal;

        try {
            $dataFilter = [];
            $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
            $dataFilter['jenis_izin'] = ['TM'];
            $dataFilter['statusCuti'] = 'ON LEAVE';
            $dataFilter['statusKaryawan'] = 'AT';

            if (!empty($departemen)) {
                $dataFilter['departemens'] = $departemen;
            } else {
                if (auth()->user()->hasRole('admin-dept')) {
                    $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
                    $dataFilter['departemens'] = [$departemen];
                }
            }

            if (!empty($tanggal)) {
                $dataFilter['date'] = $tanggal;
            } else {
                $dataFilter['date'] = Carbon::now()->format('Y-m-d');
            }

            $hadir = ScanlogDetail::getHadirCountByDate($dataFilter);
            $sakit = Sakite::countData($dataFilter);
            $izin = Izine::countData($dataFilter);
            $cuti = Cutie::countData($dataFilter);
            $total_karyawan = Karyawan::countData($dataFilter);

            $html = view('layouts.partials.attendance-summary-presensi')->with(compact('hadir', 'sakit', 'izin', 'cuti', 'total_karyawan'))->render();
            return response()->json(['data' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_detail_presensi(Request $request)
    {
        $dataValidate = [
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'type' => ['required', 'in:1,2,3,4'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $departemen = $request->departemen;
        $tanggal = $request->tanggal;
        $type = $request->type;

        try {
            $dataFilter = [];
            $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
            $dataFilter['jenis_izin'] = ['TM'];
            $dataFilter['statusCuti'] = 'ON LEAVE';
            $dataFilter['statusKaryawan'] = 'AT';

            if (!empty($departemen)) {
                $dataFilter['departemens'] = $departemen;
            } else {
                if (auth()->user()->hasRole('admin-dept')) {
                    $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
                    $dataFilter['departemens'] = [$departemen];
                }
            }

            if (!empty($tanggal)) {
                $dataFilter['date'] = $tanggal;
            } else {
                $dataFilter['date'] = Carbon::now()->format('Y-m-d');
            }

            if ($type == 1) {
                $presensis = ScanlogDetail::getHadirByDate($dataFilter);
            } elseif ($type == 2) {
                $presensis = Sakite::getDataSakit($dataFilter);
            } elseif ($type == 3) {
                $presensis = Izine::getDataIzin($dataFilter);
            } elseif ($type == 4) {
                $presensis = Cutie::getDataCuti($dataFilter);
            }

            return response()->json(['message' => 'Data Presensi Berhasil Ditemukan', 'data' => $presensis], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function check_presensi(Request $request)
    {
        $dataValidate = [
            'date' => ['required', 'date_format:Y-m-d'],
            'karyawan_id' => ['required'],
            'pin' => ['required']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $date = $request->date;
        $karyawan_id = $request->karyawan_id;
        $pin = $request->pin;

        try {
            $karyawan = Karyawan::find($karyawan_id);
            $isPersonalia = auth()->user()->hasRole('personalia');
            $cuti = Cutie::where('organisasi_id', $karyawan->organisasi_id)
                ->where('karyawan_id', $karyawan_id)
                ->where('status_dokumen', '!=', 'REJECTED')
                ->whereDate('rencana_mulai_cuti', '<=', $date)
                ->whereDate('rencana_selesai_cuti', '>=', $date)
                ->get();
            $izin = Izine::where('organisasi_id', $karyawan->organisasi_id)
                ->where('karyawan_id', $karyawan_id)
                ->where('jenis_izin', 'TM')
                ->whereDate('rencana_mulai_or_masuk', '<=', $date)
                ->whereDate('rencana_selesai_or_keluar', '>=', $date)
                ->whereNull('rejected_by')
                ->get();
            $sakit = Sakite::where('organisasi_id', $karyawan->organisasi_id)->where('karyawan_id', $karyawan_id)->whereDate('tanggal_mulai', '<=', $date)->whereDate('tanggal_selesai', '>=', $date)->whereNull('rejected_by')->whereNotNull('legalized_by')->whereNotNull('attachment')->get();
            $scanlog = Scanlog::where('organisasi_id', $karyawan->organisasi_id)->where('pin', $pin)->whereDate('scan_date', $date)->get();
            $datas = [];
            if ($scanlog->isNotEmpty()) {
                $datas = ['data' => $scanlog, 'jenis' => 'scanlog', 'isPersonalia' => $isPersonalia];
            } elseif ($cuti->isNotEmpty()) {
                $datas = ['data' => $cuti, 'jenis' => 'cuti', 'isPersonalia' => $isPersonalia];
            } elseif ($sakit->isNotEmpty()) {
                $datas = ['data' => $sakit, 'jenis' => 'sakit', 'isPersonalia' => $isPersonalia];
            } elseif ($izin->isNotEmpty()) {
                $datas = ['data' => $izin, 'jenis' => 'izin', 'isPersonalia' => $isPersonalia];
            } else {
                $datas = ['data' => null, 'jenis' => '', 'isPersonalia' => $isPersonalia];
            }

            return response()->json(['message' => 'Pengecekan Data Presensi berhasil dilakukan', 'data' => $datas], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function apply_presensi(Request $request)
    {
        $dataValidate = [
            'type' => ['required', 'in:scanlog,cuti,izin,sakit'],
            'date' => ['required', 'date_format:Y-m-d'],
            'checkType' => ['nullable', 'in:IN,OUT']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id = $request->id;
        $type = $request->type;
        $year = Carbon::createFromFormat('Y-m-d', $request->date)->year;
        $month = Carbon::createFromFormat('Y-m-d', $request->date)->month;
        $day = Carbon::createFromFormat('Y-m-d', $request->date)->day;
        DB::beginTransaction();
        try {
            if ($type == 'scanlog') {
                $scanlog = Scanlog::find($id);
                if ($scanlog) {
                    $jam_presensi = Carbon::createFromFormat('Y-m-d H:i:s', $scanlog->scan_date)->format('H:i');
                    $karyawan = Karyawan::where('pin', $scanlog->pin)->where('organisasi_id', $scanlog->organisasi_id)->first();
                    if ($karyawan) {
                        $karyawanGrup = KaryawanGrup::where('active_date', '<=', $scanlog->scan_date)->where('karyawan_id', $karyawan->id_karyawan)->orderByDesc('active_date')->first();

                        if ($karyawanGrup) {
                            if ($request->checkType == 'IN') {
                                $jam_masuk_shift = Carbon::createFromFormat('H:i:s', $karyawanGrup->jam_masuk)->format('H:i:s');
                                $jam_masuk_aktual = Carbon::createFromFormat('Y-m-d H:i:s', $scanlog->scan_date)->format('H:i:s');
                                $selisih_menit = intval(round(Carbon::parse($jam_masuk_shift)->diffInMinutes(Carbon::parse($jam_masuk_aktual), false)));
                            } else {
                                $selisih_menit = 0;
                            }
                        } else {
                            $selisih_menit = 0;
                        }

                        $presensiSummary = AttendanceSummary::whereYear('periode', $year)->whereMonth('periode', $month)->where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $karyawan->id_karyawan)->first();
                        if ($presensiSummary) {
                            $checkType = strtolower($request->checkType);
                            $statusField = "tanggal{$day}_status";
                            $timeField = "tanggal{$day}_{$checkType}";
                            $selisihField = "tanggal{$day}_selisih";
                            $presensiSummary->{$statusField} = 'H';
                            $presensiSummary->{$timeField} = $jam_presensi;
                            if ($checkType == 'in') {
                                $presensiSummary->{$selisihField} = $selisih_menit > 0 ? $selisih_menit : 0;
                            }
                            $presensiSummary->save();
                        }
                    } else {
                        DB::rollback();
                        return response()->json(['message' => 'Karyawan tidak ditemukan'], 500);
                    }
                } else {
                    DB::rollback();
                    return response()->json(['message' => 'Scanlog tidak ditemukan'], 500);
                }
            } elseif ($type == 'cuti') {
                $cuti = Cutie::find($id);
                if ($cuti) {
                    $presensiSummary = AttendanceSummary::whereYear('periode', $year)->whereMonth('periode', $month)->where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $cuti->karyawan_id)->first();
                    if ($presensiSummary) {
                        $statusField = "tanggal{$day}_status";
                        $inField = "tanggal{$day}_in";
                        $outField = "tanggal{$day}_out";

                        $presensiSummary->{$statusField} = 'C';
                        $presensiSummary->{$inField} = '00:00';
                        $presensiSummary->{$outField} = '00:00';
                        $presensiSummary->save();
                    }
                } else {
                    DB::rollback();
                    return response()->json(['message' => 'Cuti tidak ditemukan'], 500);
                }
            } elseif ($type == 'izin') {
                $izin = Izine::find($id);
                if ($izin) {
                    $presensiSummary = AttendanceSummary::whereYear('periode', $year)->whereMonth('periode', $month)->where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $izin->karyawan_id)->first();
                    if ($presensiSummary) {
                        $statusField = "tanggal{$day}_status";
                        $inField = "tanggal{$day}_in";
                        $outField = "tanggal{$day}_out";

                        $presensiSummary->{$statusField} = 'I';
                        $presensiSummary->{$inField} = '00:00';
                        $presensiSummary->{$outField} = '00:00';
                        $presensiSummary->save();
                    }
                } else {
                    DB::rollback();
                    return response()->json(['message' => 'Izin tidak ditemukan'], 500);
                }
            } elseif ($type == 'sakit') {
                $sakit = Sakite::find($id);
                if ($sakit) {
                    $presensiSummary = AttendanceSummary::whereYear('periode', $year)->whereMonth('periode', $month)->where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $sakit->karyawan_id)->first();
                    if ($presensiSummary) {
                        $statusField = "tanggal{$day}_status";
                        $inField = "tanggal{$day}_in";
                        $outField = "tanggal{$day}_out";

                        $presensiSummary->{$statusField} = 'S';
                        $presensiSummary->{$inField} = '00:00';
                        $presensiSummary->{$outField} = '00:00';
                        $presensiSummary->save();
                    }
                } else {
                    DB::rollback();
                    return response()->json(['message' => 'Sakit tidak ditemukan'], 500);
                }
            } else {
                DB::rollback();
                return response()->json(['message' => 'Type tidak ditemukan'], 500);
            }
            DB::commit();
            return response()->json(['message' => 'Berhasil mengadjust presensi karyawan'], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reset_presensi(Request $request)
    {
        $dataValidate = [
            'id' => ['required', 'exists:attendance_summaries,id_att_summary'],
            'date' => ['required', 'date_format:Y-m-d'],
            'type' => ['nullable', 'in:IN,OUT']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id = $request->id;
        $type = $request->type;
        $year = Carbon::createFromFormat('Y-m-d', $request->date)->year;
        $month = Carbon::createFromFormat('Y-m-d', $request->date)->month;
        $day = Carbon::createFromFormat('Y-m-d', $request->date)->day;
        DB::beginTransaction();
        try {
            $presensiSummary = AttendanceSummary::find($id);
            if ($presensiSummary) {
                $dataFilter = [];
                $dataFilter['organisasi_id'] = $presensiSummary->organisasi_id;
                $dataFilter['karyawan_id'] = $presensiSummary->karyawan_id;
                $dataFilter['pin'] = $presensiSummary->pin;
                $dataFilter['tanggal'] = $request->date;
                $finalSummary = ScanlogDetail::summarizePresensi($dataFilter);
                if ($finalSummary) {
                    $keterlambatan = $finalSummary->in_selisih ? intval(Carbon::createFromFormat('H:i:s', $finalSummary->in_selisih)->minute) : 0;
                    $presensiSummary->update([
                        "tanggal" . $day . "_status" => 'H',
                        "tanggal" . $day . "_selisih" => $keterlambatan,
                        "tanggal" . $day . "_in" => $finalSummary->in_time,
                        "tanggal" . $day . "_out" => $finalSummary->out_time,
                    ]);
                } else {
                    $presensiSummary->update([
                        "tanggal" . $day . "_status" => 'A',
                        "tanggal" . $day . "_selisih" => 0,
                        "tanggal" . $day . "_in" => null,
                        "tanggal" . $day . "_out" => null,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Berhasil mereset presensi karyawan'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
