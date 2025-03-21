<?php

namespace App\Http\Controllers\KSK;

use Carbon\Carbon;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "KSK-E - Release KSK",
            'page' => 'ksk-release',
        ];
        return view('pages.ksk-e.release.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'departemens.nama',
            1 => 'divisis.nama',
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

        $ksk = Karyawan::getDataKSK($dataFilter, $settings);
        $totalData = Karyawan::countDataKSK($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($ksk)) {
            foreach ($ksk as $data) {
                $nestedData['level'] = $data->jabatan_nama;
                $nestedData['divisi'] = $data->divisi_nama;
                $nestedData['departemen'] = $data->departemen_nama;
                $nestedData['release_for'] = Carbon::createFromDate($data->tahun_selesai, $data->bulan_selesai, 1)->format('M Y');
                $nestedData['jumlah_karyawan_habis'] = $data->jumlah_karyawan_habis.' Orang';
                $nestedData['action'] = '<button class="btn btn-sm btn-success btnRelease" data-id-departemen="'.$data->id_departemen.'" data-id-divisi="'.$data->id_divisi.'" data-parent-id="'.$data->parent_id.'" data-tahun-selesai="'.$data->tahun_selesai.'" data-bulan-selesai="'.$data->bulan_selesai.'"><i class="fas fa-plus"></i> Buat KSK</button>';

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

    public function get_karyawans(Request $request)
    {
        $dataValidate = [
            'id_departemen' => ['nullable','exists:departemens,id_departemen'],
            'id_divisi' => ['nullable', 'exists:divisis,id_divisi'],
            'tahun_selesai' => ['required', 'numeric'],
            'bulan_selesai' => ['required', 'numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id_departemen = $request->id_departemen;
        $id_divisi = $request->id_divisi;
        $parent_id = $request->parent_id;
        $tahun_selesai = $request->tahun_selesai;
        $bulan_selesai = $request->bulan_selesai;

        try {
            $dataFilter = [
                'id_departemen' => $id_departemen,
                'id_divisi' => $id_divisi,
                'parent_id' => $parent_id,
                'tahun_selesai' => $tahun_selesai,
                'bulan_selesai' => $bulan_selesai,
            ];

            $datas = Karyawan::getKaryawanKsk($dataFilter);
            return response()->json(['message' => 'success', 'data' => $datas], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
