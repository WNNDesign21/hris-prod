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
            2 => 'karyawans.email',
            3 => 'karyawans.no_telp',
            4 => 'grups.nama',
            5 => 'karyawans.jenis_kontrak',
            6 => 'karyawans.status_karyawan',
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
            $no = $start;
            foreach ($karyawan as $data) {
                $posisis = $data->posisi()->pluck('posisis.nama')->toArray();
                $no++;
                $nestedData['no'] = $no;
                $nestedData['id_karyawan'] = $data->id_karyawan;
                $nestedData['nama_karyawan'] = $data->nama_karyawan;
                $nestedData['email_karyawan'] = $data->email_karyawan;
                $nestedData['no_telp_karyawan'] = $data->no_telp_karyawan;
                $formattedPosisis = array_map(function($posisi) {
                    return '<span class="badge badge-info m-1">#' . $posisi . '</span>';
                }, $posisis);
                $nestedData['posisi_karyawan'] = implode(' ', $formattedPosisis);
                $nestedData['nama_grup'] = $data->nama_grup;
                $nestedData['jenis_kontrak'] = $data->jenis_kontrak;
                $nestedData['status_karyawan'] = $data->status_karyawan;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_karyawan.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_karyawan.'"><i class="fas fa-trash-alt"></i></button>
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
