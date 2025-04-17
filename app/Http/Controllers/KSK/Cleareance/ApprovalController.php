<?php

namespace App\Http\Controllers\KSK\Cleareance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\KSK\Cleareance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\KSK\CleareanceDetail;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    public function index()
    {
        // return redirect()->route('under-maintenance');
        $dataPage = [
            'pageTitle' => "KSK-E - Approval Cleareance",
            'page' => 'ksk-cleareance-approval',
        ];
        return view('pages.ksk-e.cleareance.approval.index', $dataPage);
    }

    public function datatable_must_approved(Request $request)
    {
        $columns = array(
            0 => 'cleareance_details.cleareance_id',
            1 => 'karyawans.nama',
            2 => 'cleareances.nama_departemen',
            3 => 'cleareances.nama_jabatan',
            4 => 'cleareances.nama_posisi',
            5 => 'cleareance_details.tanggal_akhir_bekerja',
            6 => 'cleareance_details.status',
        );

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

        $dataFilter['id_karyawan'] = auth()->user()->karyawan->id_karyawan;
        $dataFilter['is_clear'] = 'N';

        $cleareances = CleareanceDetail::getData($dataFilter, $settings);
        $totalData = CleareanceDetail::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($cleareances)) {
            foreach ($cleareances as $data) {
                $actionFormatted = '<a href="javascript:void(0)" class="btnDetail" data-id-cleareance="'.$data->cleareance_id.'">'.$data->cleareance_id.' <i class="fas fa-search"></i></a>';

                if ($data->is_clear == 'Y') {
                    $statusFormatted = '<span class="badge badge-success">CONFIRMED</span>';
                } else {
                    if (!$data->confirmed_by) {
                        $statusFormatted = '<span class="badge badge-warning">UNCONFIRMED</span>';
                    } else {
                        $statusFormatted = '<span class="badge badge-danger">NOT CLEAR</span>';
                    }
                }

                $nestedData['cleareance_id'] = $actionFormatted;
                $nestedData['nama_karyawan'] = $data->nama_karyawan.'<br><small>'.$data->ni_karyawan.'</small>';
                $nestedData['nama_departemen'] = $data->nama_departemen.'<br><small>'.$data->nama_divisi.'</small>';
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = Carbon::parse($data->tanggal_akhir_bekerja)->translatedFormat('d F Y');
                $nestedData['status'] = $statusFormatted;

                $dataTable[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function datatable_history(Request $request)
    {
        $columns = array(
            0 => 'cleareance_details.cleareance_id',
            1 => 'karyawans.nama',
            2 => 'cleareances.nama_departemen',
            3 => 'cleareances.nama_jabatan',
            4 => 'cleareances.nama_posisi',
            5 => 'cleareance_details.tanggal_akhir_bekerja',
            6 => 'cleareance_details.status',
        );

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

        $dataFilter['id_karyawan'] = auth()->user()->karyawan->id_karyawan;
        $dataFilter['is_clear'] = 'Y';

        $cleareances = CleareanceDetail::getData($dataFilter, $settings);
        $totalData = CleareanceDetail::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($cleareances)) {
            foreach ($cleareances as $data) {
                $actionFormatted = '<a href="javascript:void(0)" class="btnDetail" data-id-cleareance="'.$data->cleareance_id.'">'.$data->cleareance_id.' <i class="fas fa-search"></i></a>';

                if ($data->is_clear == 'Y') {
                    $statusFormatted = '<span class="badge badge-success">CONFIRMED</span>';
                } else {
                    if (!$data->confirmed_by) {
                        $statusFormatted = '<span class="badge badge-warning">UNCONFIRMED</span>';
                    } else {
                        $statusFormatted = '<span class="badge badge-danger">NOT CLEAR</span>';
                    }
                }

                $nestedData['cleareance_id'] = $actionFormatted;
                $nestedData['nama_karyawan'] = $data->nama_karyawan.'<br><small>'.$data->ni_karyawan.'</small>';
                $nestedData['nama_departemen'] = $data->nama_departemen.'<br><small>'.$data->nama_divisi.'</small>';
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = Carbon::parse($data->tanggal_akhir_bekerja)->translatedFormat('d F Y');
                $nestedData['status'] = $statusFormatted;

                $dataTable[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function konfirmasi(Request $request, int $id_cleareance_detail)
    {
        $dataValidate = [
            'keterangan' => ['required', 'string'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $keterangan = $request->keterangan;

        DB::beginTransaction();
        try {
            $cleareanceDetail = CleareanceDetail::findOrFail($id_cleareance_detail);
            $cleareanceDetail->keterangan = $keterangan;
            $cleareanceDetail->confirmed_by = auth()->user()->karyawan->nama;
            $cleareanceDetail->confirmed_at = Carbon::now();
            $cleareanceDetail->is_clear = 'Y';
            $cleareanceDetail->save();

            $cleareance = $cleareanceDetail->cleareance;
            if ($cleareance->cleareanceDetail->where('is_clear', 'N')->count() == 0) {
                $cleareance->status = 'Y';
                $cleareance->save();
            }

            DB::commit();
            return response()->json(['message' => 'Rollback konfirmasi berhasil dilakukan, silahkan konfirmasi ulang pada pihak yang bersangkutan'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
