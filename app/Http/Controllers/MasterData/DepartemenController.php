<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use App\Models\Divisi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisi = Divisi::all();
        $dataPage = [
            'pageTitle' => "Master Data - Departemen",
            'page' => 'masterdata-departemen',
            'divisi' => $divisi
        ];
        return view('pages.master-data.departemen.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id_departemen',
            1 => 'departemens.nama',
            2 => 'divisis.nama'
        );

        $totalData = Departemen::count();
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

        $departemen = Departemen::getData($dataFilter, $settings);
        $totalFiltered = Departemen::countData($dataFilter);

        $dataTable = [];

        if (!empty($departemen)) {
            $no = $start;
            foreach ($departemen as $data) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama'] = $data->nama_departemen;
                $nestedData['divisi'] = $data->nama_divisi;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_departemen.'" data-departemen-nama="'.$data->nama_departemen.'" data-divisi-id="'.$data->divisi_id.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_departemen.'"><i class="fas fa-trash-alt"></i></button>
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
            'nama_departemen' => ['required'],
            'id_divisi' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }
    
        DB::beginTransaction();
        try{
            $departemen = Departemen::create([
                'nama' => $request->input('nama_departemen'),
                'divisi_id' => $request->input('id_divisi')
            ]);

            DB::commit();
            return response()->json(['message' => 'Departemen Ditambahkan!'],200);
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
            'nama_departemen_edit' => ['required'],
            'id_divisi_edit' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $departemen = Departemen::find($id);

        DB::beginTransaction();
        try{
            $departemen->nama = $request->input('nama_departemen_edit');
            $departemen->divisi_id = $request->input('id_divisi_edit');
            $departemen->save();
            DB::commit();
            return response()->json(['message' => 'Departemen Updated!'], 200);
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
            $departemen = Departemen::findOrFail($id); 
            $departemen->delete();
            DB::commit();
            return response()->json(['message' => 'Departemen deleted!', 'id_departemen' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting departemen: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
