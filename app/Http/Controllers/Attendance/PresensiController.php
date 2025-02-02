<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Izine;
use App\Models\Sakite;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance\ScanlogDetail;
use Illuminate\Support\Facades\Validator;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataFilter = [];
        $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
        $dataFilter['date'] = Carbon::now()->format('Y-m-d');
        $dataFilter['jenis_izin'] = ['TM'];
        $dataFilter['statusCuti'] = 'ON LEAVE';
        $dataFilter['statusKaryawan'] = 'AT';

        if(auth()->user()->hasRole('personalia')){
            $departemens = Departemen::all();
        } else {
            $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
            $departemens = Departemen::where('id_departemen', $departemen)->pluck('id_departemen')->toArray();
            $dataFilter['departemens'] = $departemens;
        }

        $hadir = ScanlogDetail::getHadirCountByDate($dataFilter);
        $sakit = Sakite::countData($dataFilter);
        $izin = Izine::countData($dataFilter);
        $cuti = Cutie::countData($dataFilter);
        $total_karyawan = Karyawan::countData($dataFilter);
        $dataPage = [
            'pageTitle' => "Attendance-E - Presensi",
            'page' => 'attendance-presensi',
            'departemens' => $departemens,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'cuti' => $cuti,
            'total_karyawan' => $total_karyawan,
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

        $departemen = $request->departemen;
        if (!empty($departemen)) {
            $dataFilter['departemens'] = $departemen;
        } else {
            if(auth()->user()->hasRole('admin-dept')){
                $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
                $dataFilter['departemens'] = [$departemen];
            }
        }

        $periode = $request->periode;
        if (!empty($periode)) {
            $dataFilter['periode'] = $periode;
        } else {
            $dataFilter['periode'] = Carbon::now()->format('Y-m');
        }
        
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
                $nestedData['in_1'] = $data->in_1 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-01" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_1'] = $data?->in_status_1;
                $nestedData['out_1'] = $data->out_1 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-01" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_1'] = $data?->out_status_1;
                $nestedData['in_2'] = $data->in_2 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-02" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_2'] = $data?->in_status_2;
                $nestedData['out_2'] = $data->out_2 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-02" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_2'] = $data?->out_status_2;
                $nestedData['in_3'] = $data->in_3 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-03" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_3'] = $data?->in_status_3;
                $nestedData['out_3'] = $data->out_3 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-03" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_3'] = $data?->out_status_3;
                $nestedData['in_4'] = $data->in_4 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-04" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_4'] = $data?->in_status_4;
                $nestedData['out_4'] = $data->out_4 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-04" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_4'] = $data?->out_status_4;
                $nestedData['in_5'] = $data->in_5 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-05" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_5'] = $data?->in_status_5;
                $nestedData['out_5'] = $data->out_5 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-05" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_5'] = $data?->out_status_5;
                $nestedData['in_6'] = $data->in_6 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-06" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_6'] = $data?->in_status_6;
                $nestedData['out_6'] = $data->out_6 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-06" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_6'] = $data?->out_status_6;
                $nestedData['in_7'] = $data->in_7 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-07" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_7'] = $data?->in_status_7;
                $nestedData['out_7'] = $data->out_7 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-07" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_7'] = $data?->out_status_7;
                $nestedData['in_8'] = $data->in_8 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-08" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_8'] = $data?->in_status_8;
                $nestedData['out_8'] = $data->out_8 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-08" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_8'] = $data?->out_status_8;
                $nestedData['in_9'] = $data->in_9 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-09" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_9'] = $data?->in_status_9;
                $nestedData['out_9'] = $data->out_9 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-09" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_9'] = $data?->out_status_9;
                $nestedData['in_10'] = $data->in_10 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-10" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_10'] = $data?->in_status_10;
                $nestedData['out_10'] = $data->out_10 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-10" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_10'] = $data?->out_status_10;
                $nestedData['in_11'] = $data->in_11 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-11" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_11'] = $data?->in_status_11;
                $nestedData['out_11'] = $data->out_11 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-11" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_11'] = $data?->out_status_11;
                $nestedData['in_12'] = $data->in_12 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-12" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_12'] = $data?->in_status_12;
                $nestedData['out_12'] = $data->out_12 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-12" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_12'] = $data?->out_status_12;
                $nestedData['in_13'] = $data->in_13 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-13" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_13'] = $data?->in_status_13;
                $nestedData['out_13'] = $data->out_13 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-13" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_13'] = $data?->out_status_13;
                $nestedData['in_14'] = $data->in_14 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-14" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_14'] = $data?->in_status_14;
                $nestedData['out_14'] = $data->out_14 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-14" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_14'] = $data?->out_status_14;
                $nestedData['in_15'] = $data->in_15 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-15" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_15'] = $data?->in_status_15;
                $nestedData['out_15'] = $data->out_15 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-15" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_15'] = $data?->out_status_15;
                $nestedData['in_16'] = $data->in_16 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-16" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_16'] = $data?->in_status_16;
                $nestedData['out_16'] = $data->out_16 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-16" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>'; 
                $nestedData['out_status_16'] = $data?->out_status_16;
                $nestedData['in_17'] = $data->in_17 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-17" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_17'] = $data?->in_status_17;
                $nestedData['out_17'] = $data->out_17 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-17" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_17'] = $data?->out_status_17;
                $nestedData['in_18'] = $data->in_18 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-18" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_18'] = $data?->in_status_18;
                $nestedData['out_18'] = $data->out_18 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-18" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_18'] = $data?->out_status_18;
                $nestedData['in_19'] = $data->in_19 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-19" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_19'] = $data?->in_status_19;
                $nestedData['out_19'] = $data->out_19 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-19" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_19'] = $data?->out_status_19;
                $nestedData['in_20'] = $data->in_20 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-20" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_20'] = $data?->in_status_20;
                $nestedData['out_20'] = $data->out_20 ?? '<buton class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-20" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_20'] = $data?->out_status_20;
                $nestedData['in_21'] = $data->in_21 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-21" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_21'] = $data?->in_status_21;
                $nestedData['out_21'] = $data->out_21 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-21" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_21'] = $data?->out_status_21;
                $nestedData['in_22'] = $data->in_22 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-22" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_22'] = $data?->in_status_22;
                $nestedData['out_22'] = $data->out_22 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-22" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_22'] = $data?->out_status_22;
                $nestedData['in_23'] = $data->in_23 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-23" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_23'] = $data?->in_status_23;
                $nestedData['out_23'] = $data->out_23 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-23" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_23'] = $data?->out_status_23;
                $nestedData['in_24'] = $data->in_24 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-24" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_24'] = $data?->in_status_24;
                $nestedData['out_24'] = $data->out_24 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-24" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_24'] = $data?->out_status_24;
                $nestedData['in_25'] = $data->in_25 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-25" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_25'] = $data?->in_status_25;
                $nestedData['out_25'] = $data->out_25 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-25" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_25'] = $data?->out_status_25;
                $nestedData['in_26'] = $data->in_26 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-26" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_26'] = $data?->in_status_26;
                $nestedData['out_26'] = $data->out_26 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-26" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_26'] = $data?->out_status_26;
                $nestedData['in_27'] = $data->in_27 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-27" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_27'] = $data?->in_status_27;
                $nestedData['out_27'] = $data->out_27 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-27" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_27'] = $data?->out_status_27;
                $nestedData['in_28'] = $data->in_28 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-28" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['in_status_28'] = $data?->in_status_28;
                $nestedData['out_28'] = $data->out_28 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-28" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                $nestedData['out_status_28'] = $data?->out_status_28;

                // KONDISI UNTUK BULAN YANG MEMILIKI TANGGAL 29, 30, 31
                $daysInMonth = Carbon::createFromFormat('Y-m', $dataFilter['periode'])->daysInMonth;
                if ($daysInMonth >= 29) {
                    $nestedData['in_29'] = $data->in_29 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                    $nestedData['in_status_29'] = $data?->in_status_29;
                    $nestedData['out_29'] = $data->out_29 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-29" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                    $nestedData['out_status_29'] = $data?->out_status_29;
                } else {
                    $nestedData['in_29'] = '';
                    $nestedData['in_status_29'] = '';
                    $nestedData['out_29'] = '';
                    $nestedData['out_status_29'] = '';
                }

                if ($daysInMonth >= 30) {
                    $nestedData['in_30'] = $data->in_30 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                    $nestedData['in_status_30'] = $data?->in_status_30;
                    $nestedData['out_30'] = $data->out_30 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-30" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                    $nestedData['out_status_30'] = $data?->out_status_30;
                } else {
                    $nestedData['in_30'] = '';
                    $nestedData['in_status_30'] = '';
                    $nestedData['out_30'] = '';
                    $nestedData['out_status_30'] = '';
                }

                if ($daysInMonth == 31) {
                    $nestedData['in_31'] = $data->in_31 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                    $nestedData['in_status_31'] = $data?->in_status_31;
                    $nestedData['out_31'] = $data->out_31 ?? '<button class="btn btn-sm btn-danger btnCheck" data-date="' . $dataFilter['periode'] . '-31" data-karyawan-id="' . $data?->id_karyawan . '" data-pin="'.$data?->pin.'">Check</button>';
                    $nestedData['out_status_31'] = $data?->out_status_31;
                } else {
                    $nestedData['in_31'] = '';
                    $nestedData['in_status_31'] = '';
                    $nestedData['out_31'] = '';
                    $nestedData['out_status_31'] = '';
                }

                $nestedData['total_in_selisih'] = $data?->total_in_selisih;
                $nestedData['total_kehadiran'] = $data?->total_kehadiran;

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
                if(auth()->user()->hasRole('admin-dept')){
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
                if(auth()->user()->hasRole('admin-dept')){
                    $departemen = auth()->user()->karyawan->posisi[0]->departemen_id;
                    $dataFilter['departemens'] = [$departemen];
                }
            }

            if (!empty($tanggal)) {
                $dataFilter['date'] = $tanggal;
            } else {
                $dataFilter['date'] = Carbon::now()->format('Y-m-d');
            }

            if($type == 1) {
                $presensis = ScanlogDetail::getHadirByDate($dataFilter);
            } elseif($type == 2) {
                $presensis = Sakite::getDataSakit($dataFilter);
            } elseif($type == 3) {
                $presensis = Izine::getDataIzin($dataFilter);
            } elseif($type == 4) {
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
            $cuti = Cutie::where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $karyawan_id)->where('status_dokumen', '!=', 'REJECTED')->whereDate('rencana_mulai_cuti', '>=', $date)->whereDate('rencana_selesai_cuti', '<=', $date)->get();
            $izin = Izine::where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $karyawan_id)->where('jenis_izin', 'TM')->whereDate('rencana_mulai_or_masuk', '>=', $date)->whereDate('rencana_selesai_or_keluar', '<=', $date)->whereNull('rejected_by')->get();
            $sakit = Sakite::where('organisasi_id', auth()->user()->organisasi_id)->where('karyawan_id', $karyawan_id)->whereDate('tanggal_mulai', '>=', $date)->whereDate('tanggal_selesai', '<=', $date)->whereNull('rejected_by')->get();
            $scanlog = Scanlog::where('organisasi_id', auth()->user()->organisasi_id)->where('pin', $pin)->whereDate('scan_date', $date)->get();

            $datas = [];
            if($scanlog){
                $datas = ['data' => $scanlog, 'jenis' => 'scanlog'];
            } elseif ($cuti) {
                $datas = ['data' => $cuti, 'jenis' => 'cuti'];
            } elseif ($izin) {
                $datas = ['data' => $izin, 'jenis' => 'izin'];
            } elseif ($sakit) {
                $datas = ['data' => $sakit, 'jenis' => 'sakit'];
            } else {
                $datas = ['data' => null, 'jenis' => ''];
            }

            return response()->json(['message' => 'Pengecekan Data Presensi berhasil dilakukan', 'data' => $datas], 200);
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
