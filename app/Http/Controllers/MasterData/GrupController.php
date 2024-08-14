<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use App\Models\Grup;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GrupController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Grup",
            'page' => 'masterdata-grup',
        ];
        return view('pages.master-data.grup.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id_grup',
            1 => 'nama',
        );

        $totalData = Grup::count();
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

        $grup = Grup::getData($dataFilter, $settings);
        $totalFiltered = Grup::countData($dataFilter);

        $dataTable = [];

        if (!empty($grup)) {
            $no = $start;
            foreach ($grup as $data) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama'] = $data->nama;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_grup.'" data-grup-nama="'.$data->nama.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_grup.'"><i class="fas fa-trash-alt"></i></button>
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
            'nama_grup' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }
    
        DB::beginTransaction();
        try{
            $grup = Grup::create([
                'nama' => $request->input('nama_grup'),
            ]);

            DB::commit();
            return response()->json(['message' => 'Grup Ditambahkan!'],200);
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
            'nama_grup_edit' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $grup = Grup::find($id);

        DB::beginTransaction();
        try{
            $grup->nama = $request->input('nama_grup_edit');
            $grup->save();
            DB::commit();
            return response()->json(['message' => 'Grup Updated!'], 200);
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
            $grup = Grup::findOrFail($id); 
            $grup->delete();
            DB::commit();
            return response()->json(['message' => 'Grup deleted!', 'id_grup' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting grup: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_data_grup(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Grup::select(
            'id_grup',
            'nama',
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('nama', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $grup) {
            $dataGrup[] = [
                'id' => $grup->id_grup,
                'text' => $grup->nama
            ];
        }

        $results = array(
            "results" => $dataGrup,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
