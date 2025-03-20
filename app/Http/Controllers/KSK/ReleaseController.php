<?php

namespace App\Http\Controllers\KSK;

use Carbon\Carbon;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
                $nestedData['release_for'] = Carbon::now()->addMonth()->format('M Y');
                $nestedData['jumlah_karyawan_habis'] = $data->jumlah_karyawan_habis.' Orang';
                $nestedData['action'] = '';

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
