<?php

namespace App\Http\Controllers\Izine;

use App\Models\Izine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IzineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function izin_pribadi_view()
    {
        $dataPage = [
            'pageTitle' => "Izine - Izin Pribadi",
            'page' => 'izine-izin-pribadi',
        ];
        return view('pages.izin-e.izin-pribadi', $dataPage);
    }

    public function izin_pribadi_datatable(Request $request)
    {

        $columns = array(
            0 => 'izins.tanggal_mulai',
            1 => 'izins.tanggal_selesai',
            2 => 'izins.jenis_izin',
            3 => 'izins.durasi',
            4 => 'izins.keterangan',
            5 => 'izins.checked_by',
            6 => 'izins.approved_by',
            7 => 'izins.legalized_by',
        );

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

        $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;

        $totalData = Izine::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $izine = Izine::getData($dataFilter, $settings);
        $totalFiltered = Izine::countData($dataFilter);
        $dataTable = [];
        

        if (!empty($izine)) {
            foreach ($izine as $data) {
                $nestedData['tanggal_mulai'] = Carbon::parse($data->tanggal_mulai)->format('d M Y');
                $nestedData['tanggal_selesai'] = Carbon::parse($data->tanggal_selesai)->format('d M Y');
                $nestedData['jenis_izin'] = $data->jenis_izin;
                $nestedData['durasi'] = $data->durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $data->checked_by;
                $nestedData['approved_by'] = $data->approved_by;
                $nestedData['legalized_by'] = $data->legalized_by;
                $nestedData['lampiran'] = '';
                $nestedData['aksi'] = '';

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
