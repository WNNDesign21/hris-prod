<?php

namespace App\Http\Controllers\KSK\Cleareance;

use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use App\Http\Controllers\Controller;

class ReleaseController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "KSK-E - Release Cleareance",
            'page' => 'ksk-cleareance-release',
        ];
        return view('pages.ksk-e.cleareance.release.index', $dataPage);
    }

    public function datatable_unreleased(Request $request)
    {
        $columns = array(
            0 => 'ksk_details.nama_karyawan',
            1 => 'ksk_details.nama_departemen',
            2 => 'ksk_details.nama_jabatan',
            3 => 'ksk_details.nama_posisi',
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

        $detailKSK = DetailKSK::getData($dataFilter, $settings);
        $totalData = DetailKSK::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($detailKSK)) {
            foreach ($detailKSK as $data) {
                $actionFormatted = '<button class="btn btn-sm btn-success btnRelease" data-id-karyawan="'+$data->karyawan_id+'"><i class="fas fa-plus"></i> Buat Cleareance</button>';

                $nestedData['karyawan'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['jabatan'] = $data->nama_jabatan;
                $nestedData['posisi'] = $data->nama_posisi;
                $nestedData['action'] = $actionFormatted;

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
}
