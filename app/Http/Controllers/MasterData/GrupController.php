<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use Carbon\Carbon;
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
            2 => 'jam_masuk',
            3 => 'jam_keluar',
            4 => 'toleransi_waktu',
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
                $jam_masuk = Carbon::parse($data->jam_masuk)->format('H:i');
                $jam_keluar = Carbon::parse($data->jam_keluar)->format('H:i');
                $toleransi_waktu = abs(Carbon::parse($data->toleransi_waktu)->diffInMinutes(Carbon::today()));

                $nestedData['no'] = $no;
                $nestedData['nama'] = $data->nama;
                $nestedData['jam_masuk'] = $jam_masuk;
                $nestedData['jam_keluar'] = $jam_keluar;
                $nestedData['toleransi_waktu'] = $toleransi_waktu.' Menit';
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_grup.'" data-grup-nama="'.$data->nama.'" data-jam-masuk="'.$jam_masuk.'" data-jam-keluar="'.$jam_keluar.'" data-toleransi-waktu="'.$toleransi_waktu.'"><i class="fas fa-edit"></i></button>
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
            'jam_masuk' => ['required', 'date_format:H:i'],
            'jam_keluar' => ['required', 'date_format:H:i'],
            'toleransi_waktu' => ['required', 'integer', 'regex:/^\d+$/'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }
    
        DB::beginTransaction();
        try{
            $grup = Grup::create([
                'nama' => $request->input('nama_grup'),
                'jam_masuk' => Carbon::parse($request->input('jam_masuk'))->format('H:i:s'),
                'jam_keluar' => Carbon::parse($request->input('jam_keluar'))->format('H:i:s'),
                'toleransi_waktu' => Carbon::createFromTimestampUTC($request->input('toleransi_waktu') * 60)->format('H:i:s'),
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
            'jam_masuk_edit' => ['required', 'date_format:H:i'],
            'jam_keluar_edit' => ['required', 'date_format:H:i'],
            'toleransi_waktu_edit' => ['required', 'integer', 'regex:/^\d+$/'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        
        DB::beginTransaction();
        try{
            $grup = Grup::find($id);
            $grup->nama = $request->input('nama_grup_edit');
            $grup->jam_masuk = Carbon::parse($request->input('jam_masuk_edit'))->format('H:i:s');
            $grup->jam_keluar = Carbon::parse($request->input('jam_keluar_edit'))->format('H:i:s');
            $grup->toleransi_waktu = Carbon::createFromTimestampUTC($request->input('toleransi_waktu_edit') * 60)->format('H:i:s');
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

        $dataGrup = [];

        if(!$data->isEmpty()){
            foreach ($data->items() as $grup) {
                $dataGrup[] = [
                    'id' => $grup->id_grup,
                    'text' => $grup->nama
                ];
            }

        }

        $results = array(
            "results" => $dataGrup,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function get_data_all_grup()
    {
        $data = Grup::all();
        $dataGrup = [];
        foreach ($data as $grup) {
            $dataGrup[] = [
                'id' => $grup->id_grup,
                'text' => $grup->nama
            ];
        }
        return response()->json($dataGrup);
    }
}
