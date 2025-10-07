<?php

namespace App\Http\Controllers\TugasLuare;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TugasLuare\TugasLuar;
use App\Models\TugasLuare\DetailTugasLuar;

class AjaxController extends Controller
{
    public function select_get_data_karyawan(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');
        $selectedIds = $request->input('selectedIds');

        $query = Karyawan::select(
            'id_karyawan',
            'nama',
            'ni_karyawan',
        );
        $query->where('status_karyawan', 'AT');
        $query->where('organisasi_id', auth()->user()->organisasi_id);
        $query->whereNot('id_karyawan', auth()->user()->karyawan->id_karyawan);

        if (isset($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('nama', 'ILIKE', "%{$search}%");
                $query->orWhere('ni_karyawan', 'ILIKE', "%{$search}%");
            });
        }

        if (is_array($selectedIds) && !empty($selectedIds)) {
            $query->whereNotIn('id_karyawan', $selectedIds);
        }

        $perPage = 10; 
        $offset = ($page - 1) * $perPage;

        $totalCount = $query->count();

        $query->offset($offset)->limit($perPage);
        $data = $query->get();


        $dataKaryawan = [];
        foreach ($data as $karyawan) {
            $dataKaryawan[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama . ' (' . $karyawan->ni_karyawan . ')'
            ];
        }

        $results = [
            "results" => $dataKaryawan,
            "pagination" => [
                "more" => ($offset + $perPage) < $totalCount
            ]
        ];

        return response()->json($results);
    }

    public function select_get_data_all_karyawan(){
        $organisasi_id = auth()->user()->organisasi_id;
        $data = Karyawan::organisasi($organisasi_id)->aktif()->whereNot('id_karyawan', auth()->user()->karyawan->id_karyawan)->orderBy('nama', 'ASC')->get();
        $dataKaryawan = [];
        foreach ($data as $karyawan) {
            $dataKaryawan[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama
            ];
        }
        return response()->json($dataKaryawan);
    }

    public function get_data_pengikut(string $id_tugasluar){
        try {
            $data = TugasLuar::findOrFail($id_tugasluar);
            $dataPengikut = $data->pengikut()->where('role', 'F')->get();
            return response()->json(['message' => 'Data Berhasil Ditemukan', 'data' => $dataPengikut], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
