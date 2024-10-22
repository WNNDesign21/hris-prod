<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use App\Models\Seksi;
use App\Models\Posisi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SeksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::all();
        $dataPage = [
            'pageTitle' => "Master Data - Seksi",
            'page' => 'masterdata-seksi',
            'departemen' => $departemen
        ];
        return view('pages.master-data.seksi.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id_seksi',
            1 => 'seksis.nama',
            2 => 'departemens.nama'
        );

        $totalData = Seksi::count();
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

        $seksi = Seksi::getData($dataFilter, $settings);
        $totalFiltered = Seksi::countData($dataFilter);

        $dataTable = [];

        if (!empty($seksi)) {
            $no = $start;
            foreach ($seksi as $data) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama'] = $data->nama_seksi;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_seksi.'" data-seksi-nama="'.$data->nama_seksi.'" data-departemen-id="'.$data->departemen_id.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_seksi.'"><i class="fas fa-trash-alt"></i></button>
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
            'nama_seksi' => ['required'],
            'id_departemen' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }
    
        DB::beginTransaction();
        try{
            $seksi = Seksi::create([
                'nama' => $request->input('nama_seksi'),
                'departemen_id' => $request->input('id_departemen')
            ]);

            DB::commit();
            return response()->json(['message' => 'Seksi Ditambahkan!'],200);
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
            'nama_seksi_edit' => ['required'],
            'id_departemen_edit' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $seksi = Seksi::find($id);

        DB::beginTransaction();
        try{
            $posisi = Posisi::where('seksi_id', $id)->get();
            if($posisi){
                foreach($posisi as $ps){
                    $ps->divisi_id = Departemen::find($request->input('id_departemen_edit'))->divisi->id_divisi;
                    $ps->departemen_id = $request->input('id_departemen_edit');
                    $ps->save();
                }
            }

            $seksi->nama = $request->input('nama_seksi_edit');
            $seksi->departemen_id = $request->input('id_departemen_edit');
            $seksi->save();
            DB::commit();
            return response()->json(['message' => 'Seksi Updated!'], 200);
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
            $seksi = Seksi::findOrFail($id); 
            $seksi->delete();
            DB::commit();
            return response()->json(['message' => 'Seksi deleted!', 'id_seksi' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting seksi: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
