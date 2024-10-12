<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use App\Models\Karyawan;
use App\Models\Turnover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TurnoverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Turnover",
            'page' => 'masterdata-turnover',
        ];
        return view('pages.master-data.turnover.index', $dataPage);
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'id_turnover',
            1 => 'turnovers.karyawan_id',
            2 => 'karyawans.nama',
            3 => 'turnovers.status_karyawan',
            4 => 'turnovers.tanggal_keluar',
            5 => 'turnovers.keterangan',
        );

        $totalData = Turnover::count();
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

        $turnover = Turnover::getData($dataFilter, $settings);
        $totalFiltered = Turnover::countData($dataFilter);

        $dataTable = [];

        if (!empty($turnover)) {
            foreach ($turnover as $data) {
                $nestedData['id_turnover'] = $data->id_turnover;
                $nestedData['karyawan_id'] = $data->karyawan_id;
                $nestedData['nama'] = $data->nama;
                $nestedData['status'] = $data->status_karyawan;
                $nestedData['tanggal_keluar'] = $data->tanggal_keluar;
                $nestedData['keterangan'] = $data->keterangan;

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
        $dataValidate = [
            'karyawan_id' => ['required'],
            'status_karyawan' => ['required'],
            'tanggal_keluar' => ['required','date'],
            'keterangan' => ['nullable'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $karyawan_id = $request->karyawan_id;
        $status_karyawan = $request->status_karyawan;   
        $tanggal_keluar = $request->tanggal_keluar; 
        $keterangan = $request->keterangan;
        $jumlah_aktif_karyawan_terakhir = Karyawan::where('status_karyawan', 'AT')->count();

        DB::beginTransaction();

        try { 
            
            $turnover = Turnover::create([
                'karyawan_id' => $karyawan_id,
                'status_karyawan' => $status_karyawan,
                'tanggal_keluar' => $tanggal_keluar,
                'keterangan' => $keterangan,
                'jumlah_aktif_karyawan_terakhir' => $jumlah_aktif_karyawan_terakhir,
            ]);

            $karyawan = Karyawan::find($karyawan_id);
            $karyawan->status_karyawan = $status_karyawan;
            if($status_karyawan == 'MD' || $status_karyawan == 'TM'){
                $karyawan->tanggal_selesai = $tanggal_keluar;
            }
            $karyawan->save();  

            DB::commit();
            return response()->json(['message' => 'Data Turnover berhasil ditambahkan!'], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage() ], 402);
        }
        
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
