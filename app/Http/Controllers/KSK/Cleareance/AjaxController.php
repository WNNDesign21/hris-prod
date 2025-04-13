<?php

namespace App\Http\Controllers\KSK\Cleareance;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\KSK\Cleareance;
use App\Http\Controllers\Controller;
use App\Models\KSK\CleareanceDetail;

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

    public function select_get_atasan_langsung(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');
        $idKaryawan = $request->id_karyawan;
        $parentId = Karyawan::find($idKaryawan)->posisi->pluck('parent_id')->toArray();

        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
        );
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id');
        $query->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi');
        $query->whereIn('posisis.id_posisi', $parentId);
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

    public function get_detail_cleareance(string $id_cleareance)
    {
        try {
            $header = Cleareance::find($id_cleareance);
            if (!$header) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
            $detail = CleareanceDetail::where('cleareance_id', $header->id_cleareance)->orderBy('type')->get();

            $html = view('layouts.partials.ksk.cleareance.modal-body-detail', ['header' => $header, 'detail' => $detail])->render();
            return response()->json(['message' => 'success', 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
