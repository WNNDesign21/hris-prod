<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Karyawan",
            'page' => 'masterdata-karyawan',
        ];
        return view('pages.master-data.karyawan.index', $dataPage);
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'id_karyawan',
            1 => 'karyawans.nama',
            3 => 'grups.nama',
            4 => 'jenis_kontrak',
            5 => 'status_karyawan'
        );

        $totalData = Karyawan::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = (!empty($request->input('order.0.column'))) ? $columns[$request->input('order.0.column')] : $columns[0];
        $dir = (!empty($request->input('order.0.dir'))) ? $request->input('order.0.dir') : "DESC";

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $karyawan = Karyawan::getData($dataFilter, $settings);
        $totalFiltered = Karyawan::countData($dataFilter);

        $dataTable = [];

        if (!empty($karyawan)) {
            foreach ($karyawan as $data) {
                $posisis = $data->posisi()->pluck('posisis.nama')->toArray();
                $nestedData['id_karyawan'] = $data->id_karyawan;
                $nestedData['nama'] = $data->nama;
                $nestedData['jenis_kontrak'] = $data->jenis_kontrak;
                $nestedData['status_karyawan'] = $data->status_karyawan;
                $formattedPosisi = array_map(function($posisi) {
                    return '<span class="badge badge-primary m-1">#' . $posisi . '</span>';
                }, $posisis);
                $nestedData['posisi'] = implode(' ', $formattedPosisi);
                $nestedData['grup'] = $data->nama_grup;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-secondary btnKontrak"><i class="fas fa-file-signature"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-info btnUser"><i class="fas fa-user-circle"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit"><i class="fas fa-info-circle"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_karyawan.'"><i class="fas fa-trash-alt"></i></button>
                </div>
                ';

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
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
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
