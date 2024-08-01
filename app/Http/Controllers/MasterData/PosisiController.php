<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use App\Models\Seksi;
use App\Models\Divisi;
use App\Models\Posisi;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PosisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatan = Jabatan::all();
        $dataPage = [
            'pageTitle' => "Master Data - Posisi",
            'page' => 'masterdata-posisi',
            'jabatan' => $jabatan,
        ];
        return view('pages.master-data.posisi.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id_posisi',
            1 => 'posisis.nama',
            2 => 'jabatans.nama',
            3 => 'nama_organisasi',
            4 => 'nama_divisi',
            5 => 'nama_departemen',
            6 => 'nama_seksi',
        );

        $totalData = Posisi::count();
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

        $posisi = Posisi::getData($dataFilter, $settings);
        $totalFiltered = Posisi::countData($dataFilter);

        $dataTable = [];

        if (!empty($posisi)) {
            $no = $start;
            foreach ($posisi as $data) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_organisasi'] = $data->nama_organisasi !== null ? $data->nama_organisasi : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['nama_divisi'] = $data->nama_divisi !== null ? $data->nama_divisi : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['nama_departemen'] = $data->nama_departemen !== null ? $data->nama_departemen : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['nama_seksi'] = $data->nama_seksi !== null ? $data->nama_seksi : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_posisi.'" data-posisi-nama="'.$data->nama_posisi.'" data-jabatan-id="'.$data->jabatan_id.'" data-organisasi-id="'.$data->organisasi_id.'" data-divisi-id="'.$data->divisi_id.'" data-departemen-id="'.$data->departemen_id.'" data-seksi-id="'.$data->seksi_id.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_posisi.'"><i class="fas fa-trash-alt"></i></button>
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
        $dataValidate = [
            'nama_jabatan' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }
    
        DB::beginTransaction();
        try{
            $posisi = Posisi::create([
                'nama' => $request->input('nama_jabatan'),
            ]);

            DB::commit();
            return response()->json(['message' => 'Jabatan Ditambahkan!'],200);
        } catch(Throwable $error){
            return response()->json(['message' => $error->getMessage()], 500);
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
        $dataValidate = [
            'nama_jabatan_edit' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $posisi = Posisi::find($id);

        DB::beginTransaction();
        try{
            $posisi->nama = $request->input('nama_jabatan_edit');
            $posisi->save();
            DB::commit();
            return response()->json(['message' => 'Jabatan Updated!'], 200);
        } catch(\Throwable $error){
            DB::rollback();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $posisi = Posisi::findOrFail($id); 
            $posisi->delete();
            DB::commit();
            return response()->json(['message' => 'Jabatan deleted!', 'id_divisi' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting Posisi: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
