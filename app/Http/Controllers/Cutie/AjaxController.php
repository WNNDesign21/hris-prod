<?php

namespace App\Http\Controllers\Cutie;

use Exception;
use Illuminate\Http\Request;
use App\Services\CutiService;
use App\Http\Controllers\Controller;

class AjaxController extends Controller
{
    private $cutiService;
    public function __construct(CutiService $cutiService)
    {
        $this->cutiService = $cutiService;
    }

    public function get_data_detail_cuti(int $id)
    {
        try {
            $fields = [
            'id_cuti',
            'durasi_cuti',
            'jenis_cuti',
            'jenis_cuti_id',
            'rencana_mulai_cuti',
            'rencana_selesai_cuti',
            'alasan_cuti',
            ];
            $cuti = $this->cutiService->getById($id, $fields)->toArray();
            return response()->json(['data' => $cuti], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function get_data_detail_jenis_cuti(int $id)
    {
        try {
            $fields = [
                'id_jenis_cuti',
                'jenis',
                'durasi',
                'isUrgent',
                'isWorkday'
            ];
            $jenisCuti = $this->cutiService->getJenisCutiById($id, $fields)->toArray();
            return response()->json(['data' => $jenisCuti], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function get_data_jenis_cuti_khusus(){
        try {
            $fields = [
                'id_jenis_cuti',
                'jenis',
                'durasi',
                'isUrgent',
                'isWorkday'
            ];
            $jenisCuti = $this->cutiService->getJenisCuti($fields);
            $jenisCutiKhusus = [];

            if ($jenisCuti->isNotEmpty()) {
                foreach ($jenisCuti as $jc) {
                    $jenisCutiKhusus[] = [
                        'id' => $jc->id_jenis_cuti,
                        'text' => $jc->jenis,
                        'durasi' => $jc->durasi,
                        'isurgent' => $jc->isUrgent,
                        'isworkday' => $jc->isWorkday
                    ];
                }
            }

            return response()->json(['data' => $jenisCutiKhusus], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function get_karyawan_pengganti(string $id_karyawan)
    {
        try {
            $data = $this->cutiService->getKaryawanPengganti($id_karyawan);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_karyawan_cuti(Request $request)
    {
        $search = $request->search;
        $page = $request->page;
        $dataFilter = [];

        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        if (auth()->user()->hasRole('atasan')) {
            $posisi = auth()->user()->karyawan->posisi;
        } else {
            $posisi = null;
        }

        $data = $this->cutiService->getKaryawanCuti($posisi, $dataFilter);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $karyawan) {
            $karyawanCuti[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama
            ];
        }

        $results = array(
            "results" => $karyawanCuti,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
