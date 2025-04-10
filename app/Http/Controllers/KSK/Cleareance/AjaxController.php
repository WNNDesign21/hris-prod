<?php

namespace App\Http\Controllers\KSK\Cleareance;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AjaxController extends Controller
{
    public function select_get_karyawans(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
        );
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id');
        $query->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi');
        $query->where('karyawans.organisasi_id', auth()->user()->organisasi_id);
        $query->where('posisis.jabatan_id', '<=', 5);
        $query->aktif();

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        $dataUser = [];
        if ($page == 1) {
            $dataUser[] = [
                'id' => '',
                'text' => 'TIDAK DIPERLUKAN'
            ];
        }

        foreach ($data->items() as $karyawan) {
            $dataUser[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
