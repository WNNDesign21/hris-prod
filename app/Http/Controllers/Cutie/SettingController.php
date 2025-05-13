<?php

namespace App\Http\Controllers\Cutie;

use Exception;
use Illuminate\Http\Request;
use App\Services\CutiService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    private $cutiService;
    public function __construct(CutiService $cutiService)
    {
        $this->cutiService = $cutiService;
    }

    public function index()
    {
        $dataPage = [
            'pageTitle' => "Cuti-E - Setting Cuti Khusus",
            'page' => 'cutie-setting',
        ];
        return view('pages.cuti-e.setting-cuti', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'jenis',
            1 => 'durasi',
            2 => 'isUrgent',
            3 => 'isWorkday'
        );

        $totalData = $this->cutiService->countJenisCuti();
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

        $jenis_cuti = $this->cutiService->getSettingCutiDatatable($dataFilter, $settings);
        $totalFiltered = $this->cutiService->countSettingCutiDatatable($dataFilter);
        $dataTable = [];

        if (!empty($jenis_cuti)) {
            foreach ($jenis_cuti as $data) {
                $nestedData['jenis'] = $data->jenis;
                $nestedData['durasi'] = $data->durasi.' Hari';
                $nestedData['isUrgent'] = $data->isUrgent == 'N' ? '❌' : '✅';
                $nestedData['isWorkday'] = $data->isWorkday == 'N' ? '❌' : '✅';
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm"><button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_jenis_cuti.'"><i class="fas fa-edit"></i> Edit </button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_jenis_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button></div>';

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
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required',
            'durasi' => 'required|numeric|min:1',
            'isUrgent' => 'required|string|in:Y,N',
            'isWorkday' => 'required|string|in:Y,N',
        ]);

        $jenis = $request->jenis;
        $durasi = $request->durasi;
        $isUrgent = $request->isUrgent;
        $isWorkday = $request->isWorkday;

        DB::beginTransaction();
        try{
            $dataJenisCuti = [
                'jenis' => $jenis,
                'durasi' => $durasi,
                'isUrgent' => $isUrgent,
                'isWorkday' => $isWorkday
            ];
            $this->cutiService->createJenisCuti($dataJenisCuti);
            DB::commit();
            return response()->json(['message' => 'Store Jenis Cuti Khusus Berhasil dilakukan!'], 200);
        } catch(Exception $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id_jenis_cuti)
    {
        $request->validate([
            'jenis' => 'required',
            'durasi' => 'required|numeric|min:1',
            'isUrgent' => 'required|string|in:Y,N',
            'isWorkday' => 'required|string|in:Y,N',
        ]);

        $jenis = $request->jenis;
        $durasi = $request->durasi;
        $isUrgent = $request->isUrgent;
        $isWorkday = $request->isWorkday;

        DB::beginTransaction();
        try{
            $dataJenisCuti = [
                'jenis' => $jenis,
                'durasi' => $durasi,
                'isUrgent' => $isUrgent,
                'isWorkday' => $isWorkday
            ];
            $jenisCuti = $this->cutiService->updateJenisCuti($id_jenis_cuti, $dataJenisCuti);
            DB::commit();
            return response()->json(['message' => 'Update Jenis Cuti Khusus Berhasil dilakukan!'], 200);
        } catch(Exception $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function delete(string $id_jenis_cuti)
    {
        DB::beginTransaction();
        try{
            $jenis_cuti = $this->cutiService->getJenisCutiById($id_jenis_cuti);
            if($jenis_cuti){
                if($jenis_cuti->isUsed($id_jenis_cuti)){
                    DB::commit();
                    return response()->json(['message' => 'Jenis Cuti sudah digunakan, tidak bisa dihapus!'],400);
                } else {
                    $this->cutiService->deleteJenisCuti($id_jenis_cuti);
                }
            } else {
                DB::commit();
                return response()->json(['message' => 'Jenis Cuti tidak ditemukan!'],400);
            }

            DB::commit();
            return response()->json(['message' => 'Jenis Cuti Dihapus!'],200);
        } catch(Exception $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

}
