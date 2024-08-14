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
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PosisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tree = Posisi::with('children')->where('parent_id', 0)->get();
        $dataPage = [
            'pageTitle' => "Master Data - Posisi",
            'page' => 'masterdata-posisi',
            'tree' => $tree,
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
                $parent = Posisi::find($data->parent_id);
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['nama_jabatan'] = $data->nama_jabatan;
                $nestedData['nama_organisasi'] = $data->nama_organisasi !== null ? $data->nama_organisasi : 'CORPORATE/ALL PLANT';
                $nestedData['nama_divisi'] = $data->nama_divisi !== null ? $data->nama_divisi : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['nama_departemen'] = $data->nama_departemen !== null ? $data->nama_departemen : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['nama_seksi'] = $data->nama_seksi !== null ? $data->nama_seksi : '<i class="fa fa-minus-square" aria-hidden="true"></i>';
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_posisi.'" data-parent-id="'.$data->parent_id.'" data-posisi-nama="'.$data->nama_posisi.'" data-jabatan-id="'.$data->jabatan_id.'" data-organisasi-id="'.$data->organisasi_id.'" data-divisi-id="'.$data->divisi_id.'" data-departemen-id="'.$data->departemen_id.'" data-seksi-id="'.$data->seksi_id.'"><i class="fas fa-edit"></i></button>
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
            'nama_posisi' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama_posisi = $request->nama_posisi;
        $id_jabatan = $request->id_jabatan;
        $id_divisi = $request->id_divisi;
        $id_departemen = $request->id_departemen;
        $id_organisasi = $request->id_organisasi;
        $id_seksi = $request->id_seksi;
        $parent_id = $request->parent_id;

        DB::beginTransaction();
        try{
            //CEK BOD
            if($parent_id == 0){
                $posisi = Posisi::create([
                    'jabatan_id' => 1,
                    'nama' =>  $nama_posisi,
                    'parent_id' => $parent_id,
                ]);
            //CEK DIVISION/PLANT HEAD
            } elseif($id_jabatan == 2){
                //CEK PLANT HEAD
                if(isset($request->id_organisasi)){
                    $posisi = Posisi::create([
                        'jabatan_id' => $id_jabatan,
                        'nama' =>  $nama_posisi,
                        'parent_id' => $parent_id,
                        'organisasi_id' => $id_organisasi,
                        'divisi_id' => $id_divisi
                    ]);
                //CEK DIVISION HEAD
                } else{
                    $posisi = Posisi::create([
                        'jabatan_id' => $id_jabatan,
                        'nama' =>  $nama_posisi,
                        'parent_id' => $parent_id,
                        'divisi_id' => $id_divisi
                    ]);
                }
            } elseif($id_jabatan == 3){
                $departemen = Departemen::find($id_departemen);
                $posisi = Posisi::create([
                    'jabatan_id' => $id_jabatan,
                    'nama' =>  $nama_posisi,
                    'parent_id' => $parent_id,
                    'divisi_id' => $departemen->divisi_id,
                    'departemen_id' => $id_departemen
                ]);
            } elseif ($id_jabatan == 4){
                $seksi = Seksi::find($id_seksi);
                $posisi = Posisi::create([
                    'jabatan_id' => $id_jabatan,
                    'nama' =>  $nama_posisi,
                    'organisasi_id' => $id_organisasi,
                    'parent_id' => $parent_id,
                    'divisi_id' => $seksi->divisi->id_divisi,
                    'departemen_id' => $seksi->departemen_id,
                    'seksi_id' => $id_seksi
                ]);
            } else {
                $seksi = Seksi::find($id_seksi);
                $posisi = Posisi::create([
                    'jabatan_id' => $id_jabatan,
                    'nama' =>  $nama_posisi,
                    'organisasi_id' => $id_organisasi,
                    'parent_id' => $parent_id,
                    'divisi_id' => $seksi->divisi->id_divisi,
                    'departemen_id' => $seksi->departemen_id,
                    'seksi_id' => $id_seksi
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Posisi Ditambahkan!'],200);
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
            'nama_posisi_edit' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama_posisi_edit = $request->nama_posisi_edit;
        $id_jabatan_edit = $request->id_jabatan_edit;
        $id_divisi_edit = $request->id_divisi_edit;
        $id_departemen_edit = $request->id_departemen_edit;
        $id_organisasi_edit = $request->id_organisasi_edit;
        $id_seksi_edit = $request->id_seksi_edit;
        $parent_id_edit = $request->parent_id_edit;

        $posisi = Posisi::find($id);

        DB::beginTransaction();
        try{
            //CEK BOD
            if($parent_id_edit == 0){
                $posisi->jabatan_id = 1;
                $posisi->nama = $nama_posisi_edit;
                $posisi->parent_id = $parent_id_edit;
                $posisi->organisasi_id = null;
                $posisi->divisi_id = null;
                $posisi->departemen_id = null;
                $posisi->seksi_id = null;
            //CEK DIVISION/PLANT HEAD
            } elseif ($id_jabatan_edit == 2){
                //CEK PLANT HEAD
                if(isset($request->id_organisasi_edit)){
                    $posisi->jabatan_id = $id_jabatan_edit;
                    $posisi->nama = $nama_posisi_edit;
                    $posisi->parent_id = $parent_id_edit;
                    $posisi->organisasi_id = $id_organisasi_edit;
                    $posisi->divisi_id = $id_divisi_edit;
                    $posisi->departemen_id = null;
                    $posisi->seksi_id = null;
                //CEK DIVISION HEAD
                } else{
                    $posisi->jabatan_id = $id_jabatan_edit;
                    $posisi->nama = $nama_posisi_edit;
                    $posisi->parent_id = $parent_id_edit;
                    $posisi->divisi_id = $id_divisi_edit;
                    $posisi->departemen_id = null;
                    $posisi->seksi_id = null;
                    $posisi->organisasi_id = null;
                }
            } elseif($id_jabatan_edit == 3){
                $departemen = Departemen::find($id_departemen_edit);
                if(isset($request->id_organisasi_edit)){
                    $posisi->jabatan_id = $id_jabatan_edit;
                    $posisi->nama = $nama_posisi_edit;
                    $posisi->parent_id = $parent_id_edit;
                    $posisi->organisasi_id = $id_organisasi_edit;
                    $posisi->divisi_id = $departemen->divisi_id;
                    $posisi->departemen_id = $departemen_id_edit;
                    $posisi->seksi_id = null;
                } else{
                    $posisi->jabatan_id = $id_jabatan_edit;
                    $posisi->nama = $nama_posisi_edit;
                    $posisi->parent_id = $parent_id_edit;
                    $posisi->organisasi_id = null;
                    $posisi->divisi_id = $departemen->divisi_id;
                    $posisi->departemen_id = $departemen_id_edit;
                    $posisi->seksi_id = null;
                }
            } else {
                $seksi = Seksi::find($id_seksi_edit);
                if(isset($request->id_organisasi_edit)){
                    $posisi->jabatan_id = $id_jabatan_edit;
                    $posisi->nama = $nama_posisi_edit;
                    $posisi->parent_id = $parent_id_edit;
                    $posisi->organisasi_id = $id_organisasi_edit;
                    $posisi->divisi_id = $seksi->divisi->id_divisi;
                    $posisi->departemen_id = $seksi->departemen_id;
                    $posisi->seksi_id = $id_seksi_edit;
                } else{
                    $posisi->jabatan_id = $id_jabatan_edit;
                    $posisi->nama = $nama_posisi_edit;
                    $posisi->parent_id = $parent_id_edit;
                    $posisi->organisasi_id = null;
                    $posisi->divisi_id = $seksi->divisi->id_divisi;
                    $posisi->departemen_id = $seksi->departemen_id;
                    $posisi->seksi_id = $id_seksi_edit;
                }
            } 

            $posisi->save();
            DB::commit();
            return response()->json(['message' => 'Posisi Updated!'], 200);
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
        if (Posisi::where('parent_id', $id)->exists()) {
            return response()->json(['message' => 'Failed to Delete Posisi, This posisi has member'], 400);
        }

        DB::beginTransaction();
        try {
            $posisi = Posisi::findOrFail($id); 
            $posisi->delete();
            DB::commit();
            return response()->json(['message' => 'Posisi deleted!', 'posisi' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting Posisi: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //GET DATA PARENT UNTUK SELECT2 #parent_id
    public function get_data_parent(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $getOrg = Organisasi::select("id_organisasi as org_id", "nama as nama_org");
        $getDivisi = Divisi::select("id_divisi as div_id", "nama as nama_div");
        $getDepartemen = Departemen::select("id_departemen as dep_id", "nama as nama_dep");
        $getSeksi = Seksi::select("id_seksi as sek_id", "nama as nama_sek");
        $query = Posisi::select(
            'id_posisi',
            'posisis.organisasi_id',
            'posisis.divisi_id',
            'posisis.departemen_id',
            'posisis.seksi_id',
            'posisis.parent_id',
            'posisis.nama as nama_posisi',
            'jabatans.nama as nama_jabatan',
            'org.nama_org as nama_organisasi',
            'div.nama_div as nama_divisi',
            'dep.nama_dep as nama_departemen',
            'sek.nama_sek as nama_seksi',
        )
        ->leftJoin('jabatans', 'posisis.jabatan_id', 'jabatans.id_jabatan')
        ->leftJoinSub($getOrg, 'org', function (JoinClause $joinOrg) {
            $joinOrg->on('posisis.organisasi_id', 'org.org_id');
        })
        ->leftJoinSub($getDivisi, 'div', function (JoinClause $joinDivisi) {
            $joinDivisi->on('posisis.divisi_id', 'div.div_id');
        })
        ->leftJoinSub($getDepartemen, 'dep', function (JoinClause $joinDepartemen) {
            $joinDepartemen->on('posisis.departemen_id', 'dep.dep_id');
        })
        ->leftJoinSub($getSeksi, 'sek', function (JoinClause $joinSeksi) {
            $joinSeksi->on('posisis.seksi_id', 'sek.sek_id');
        });

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('posisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jabatans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('org.nama_org', 'ILIKE', "%{$search}%")
                    ->orWhere('div.nama_div', 'ILIKE', "%{$search}%")
                    ->orWhere('dep.nama_dep', 'ILIKE', "%{$search}%")
                    ->orWhere('sek.nama_sek', 'ILIKE', "%{$search}%");
            });
        }

        $query->whereNotIn('posisis.jabatan_id', [6]);
        $query->orderBy('posisis.nama', 'ASC');

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        $dataPosisi = [
            [
                'id' => '0',
                'text' => 'Tidak Memiliki Atasan'
            ]
        ];
        foreach ($data->items() as $ps) {
            $nama_org = $ps->nama_organisasi !== null ? $ps->nama_organisasi : 'CORPORATE/ALL PLANT';
            $dataPosisi[] = [
                'id' => $ps->id_posisi,
                'text' => $ps->nama_jabatan ." - ". $ps->nama_posisi . ' - ' . $nama_org
            ];
        }

        $results = array(
            "results" => $dataPosisi,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    //GET DATA PARENT UNTUK SELECT2 #parent_id_edit
    public function get_data_parent_edit(string $id_posisi){
        $jabatan_data = [6];
        $posisi = Posisi::find($id_posisi);
        $available_posisi = Posisi::where('jabatan_id', '>=' , $posisi->jabatan_id)->pluck('jabatan_id');
        foreach($available_posisi as $item){
            if($item != 6){
                $jabatan_data[] = $item;
            }
        }

        $getOrg = Organisasi::select("id_organisasi as org_id", "nama as nama_org");
        $getDivisi = Divisi::select("id_divisi as div_id", "nama as nama_div");
        $getDepartemen = Departemen::select("id_departemen as dep_id", "nama as nama_dep");
        $getSeksi = Seksi::select("id_seksi as sek_id", "nama as nama_sek");
        $query = Posisi::select(
            'id_posisi',
            'posisis.organisasi_id',
            'posisis.divisi_id',
            'posisis.departemen_id',
            'posisis.seksi_id',
            'posisis.parent_id',
            'posisis.nama as nama_posisi',
            'jabatans.nama as nama_jabatan',
            'org.nama_org as nama_organisasi',
            'div.nama_div as nama_divisi',
            'dep.nama_dep as nama_departemen',
            'sek.nama_sek as nama_seksi',
        )
        ->leftJoin('jabatans', 'posisis.jabatan_id', 'jabatans.id_jabatan')
        ->leftJoinSub($getOrg, 'org', function (JoinClause $joinOrg) {
            $joinOrg->on('posisis.organisasi_id', 'org.org_id');
        })
        ->leftJoinSub($getDivisi, 'div', function (JoinClause $joinDivisi) {
            $joinDivisi->on('posisis.divisi_id', 'div.div_id');
        })
        ->leftJoinSub($getDepartemen, 'dep', function (JoinClause $joinDepartemen) {
            $joinDepartemen->on('posisis.departemen_id', 'dep.dep_id');
        })
        ->leftJoinSub($getSeksi, 'sek', function (JoinClause $joinSeksi) {
            $joinSeksi->on('posisis.seksi_id', 'sek.sek_id');
        });
        $query->whereNotIn('posisis.jabatan_id', array_unique($jabatan_data));
        $query->orderBy('posisis.nama', 'ASC');

        $data = $query->get();

        $dataPosisi = [
            [
                'id' => '0',
                'text' => 'Tidak Memiliki Atasan'
            ]
        ];
        foreach ($data as $ps) {
            $nama_org = $ps->nama_organisasi !== null ? $ps->nama_organisasi : 'CORPORATE/ALL PLANT';
            $dataPosisi[] = [
                'id' => $ps->id_posisi,
                'text' => $ps->nama_jabatan ." - ". $ps->nama_posisi . ' - ' . $nama_org
            ];
        }

        $posisi = array(
            "posisi" => $dataPosisi,
        );

        return response()->json($posisi);
    }

    //GET DATA JABATAN BERDASARKAN POSISI PARENTNYA UNTUK SELECT2 #parent_id
    public function get_data_jabatan_by_posisi(string $id)
    {
        $id_jabatan = Posisi::find($id)->jabatan_id;
        $jabatan = Jabatan::where('id_jabatan', '>' , $id_jabatan)->get();

        return response()->json($jabatan, 200);
    }

    //GET DATA JABATAN BERDASARKAN POSISI PARENTNYA UNTUK SELECT2 getDataJabatanByPosisiEdit()
    public function get_data_jabatan_by_posisi_edit(string $id,string $myposisi)
    {
        $my_posisi = Posisi::find($myposisi);
        $posisi = Posisi::where('id_posisi',$id)->first();
        $parent_posisi = Posisi::where('id_posisi', $posisi->parent_id)->first();
        $jabatan = Jabatan::where('id_jabatan', '>' , $posisi->jabatan_id)->get();
        $data = [
            'jabatan' => $jabatan,
            'jabatan_id' => $my_posisi->jabatan_id,
            'organisasi_id' => $posisi->organisasi_id,
            'divisi_id' => $posisi->divisi_id,
            'departemen_id' => $posisi->departemen_id,
            'seksi_id' => $posisi->seksi_id,
        ];

        return response()->json($data, 200);
    }

     //GET DATA SELECT OPTION BERDASARKAN JABATAN UNTUK SELECT2 #id_jabatan
     public function get_data_by_jabatan(string $id_jabatan)
     {
         $data = [];
 
         if($id_jabatan == 1){
             $data = [
                 'organisasi' => null,
                 'divisi' => null,
                 'departemen' => null,
                 'seksi' => null,
             ];
         } elseif ($id_jabatan == 2){
             $data = [
                 'organisasi' => Organisasi::all(),
                 'divisi' => Divisi::all(),
                 'departemen' => null,
                 'seksi' => null,
             ];
         } elseif ($id_jabatan == 3){
             $data = [
                 'organisasi' => Organisasi::all(),
                 'divisi' => null,
                 'departemen' => Departemen::with('divisi')->get(),
                 'seksi' => null,
             ];
         } elseif ($id_jabatan == 4){
             $data = [
                 'organisasi' => Organisasi::all(),
                 'divisi' => null,
                 'departemen' => null,
                 'seksi' => Seksi::with('departemen', 'divisi')->get(),
             ];
         } elseif ($id_jabatan == 5){
             $data = [
                 'organisasi' => Organisasi::all(),
                 'divisi' => null,
                 'departemen' => null,
                 'seksi' => Seksi::with('departemen', 'divisi')->get(),
             ];
         } elseif ($id_jabatan == 6){
             $data = [
                 'organisasi' => Organisasi::all(),
                 'divisi' => null,
                 'departemen' => null,
                 'seksi' => Seksi::with('departemen', 'divisi')->get(),
             ];
         }
 
         return response()->json($data, 200);
     }

     public function get_data_posisi(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $getOrg = Organisasi::select("id_organisasi as org_id", "nama as nama_org");
        $getDivisi = Divisi::select("id_divisi as div_id", "nama as nama_div");
        $getDepartemen = Departemen::select("id_departemen as dep_id", "nama as nama_dep");
        $getSeksi = Seksi::select("id_seksi as sek_id", "nama as nama_sek");
        $query = Posisi::select(
            'id_posisi',
            'posisis.organisasi_id',
            'posisis.divisi_id',
            'posisis.departemen_id',
            'posisis.seksi_id',
            'posisis.parent_id',
            'posisis.nama as nama_posisi',
            'jabatans.nama as nama_jabatan',
            'org.nama_org as nama_organisasi',
            'div.nama_div as nama_divisi',
            'dep.nama_dep as nama_departemen',
            'sek.nama_sek as nama_seksi',
        )
        ->leftJoin('jabatans', 'posisis.jabatan_id', 'jabatans.id_jabatan')
        ->leftJoinSub($getOrg, 'org', function (JoinClause $joinOrg) {
            $joinOrg->on('posisis.organisasi_id', 'org.org_id');
        })
        ->leftJoinSub($getDivisi, 'div', function (JoinClause $joinDivisi) {
            $joinDivisi->on('posisis.divisi_id', 'div.div_id');
        })
        ->leftJoinSub($getDepartemen, 'dep', function (JoinClause $joinDepartemen) {
            $joinDepartemen->on('posisis.departemen_id', 'dep.dep_id');
        })
        ->leftJoinSub($getSeksi, 'sek', function (JoinClause $joinSeksi) {
            $joinSeksi->on('posisis.seksi_id', 'sek.sek_id');
        });

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('posisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jabatans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('org.nama_org', 'ILIKE', "%{$search}%")
                    ->orWhere('div.nama_div', 'ILIKE', "%{$search}%")
                    ->orWhere('dep.nama_dep', 'ILIKE', "%{$search}%")
                    ->orWhere('sek.nama_sek', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $ps) {
            $nama_org = $ps->nama_organisasi !== null ? $ps->nama_organisasi : 'CORPORATE/ALL PLANT';
            $dataPosisi[] = [
                'id' => $ps->id_posisi,
                'text' => $ps->nama_jabatan ." - ". $ps->nama_posisi . ' - ' . $nama_org
            ];
        }

        $results = array(
            "results" => $dataPosisi,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
