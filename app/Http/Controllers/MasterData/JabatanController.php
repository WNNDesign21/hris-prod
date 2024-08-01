<?php

namespace App\Http\Controllers\MasterData;


use Exception;
use Throwable;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Jabatan",
            'page' => 'masterdata-jabatan',
        ];
        return view('pages.master-data.jabatan.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id_jabatan',
            1 => 'nama',
        );

        $totalData = Jabatan::count();
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

        $jabatan = Jabatan::getData($dataFilter, $settings);
        $totalFiltered = Jabatan::countData($dataFilter);

        $dataTable = [];

        if (!empty($jabatan)) {
            $no = $start;
            foreach ($jabatan as $data) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama'] = $data->nama;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_jabatan.'" data-jabatan-nama="'.$data->nama.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_jabatan.'"><i class="fas fa-trash-alt"></i></button>
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
            $jabatan = Jabatan::create([
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

        $jabatan = Jabatan::find($id);

        DB::beginTransaction();
        try{
            $jabatan->nama = $request->input('nama_jabatan_edit');
            $jabatan->save();
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
            $jabatan = Jabatan::findOrFail($id); 
            $jabatan->delete();
            DB::commit();
            return response()->json(['message' => 'Jabatan deleted!', 'id_divisi' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting jabatan: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
