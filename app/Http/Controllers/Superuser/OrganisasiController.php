<?php

namespace App\Http\Controllers\Superuser;

use Exception;
use Throwable;
use App\Models\User;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use App\Models\SettingLembur;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrganisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Superuser - Organisasi",
            'page' => 'superuser-organisasi',
        ];
        return view('pages.superuser.organisasi.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id_organisasi',
            1 => 'nama',
            2 => 'alamat',
        );

        $totalData = Organisasi::count();
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

        $org = Organisasi::getData($dataFilter, $settings);
        $totalFiltered = Organisasi::countData($dataFilter);

        $dataTable = [];

        if (!empty($org)) {
            $no = $start;
            foreach ($org as $data) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['nama'] = $data->nama;
                $nestedData['alamat'] = $data->alamat;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_organisasi.'" data-org-nama="'.$data->nama.'" data-org-alamat="'.$data->alamat.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_organisasi.'"><i class="fas fa-trash-alt"></i></button>
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

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:organisasis,nama',
            'alamat' => 'required',
            'personalia_email' => 'required|email|unique:users,email',
            'personalia_username' => 'required|unique:users,username',
            'personalia_password' => 'required|min:8|confirmed',
            'security_email' => 'required|email|unique:users,email',
            'security_username' => 'required|unique:users,username',
            'security_password' => 'required|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try{
            $org = Organisasi::create([
                'nama' => $request->nama,
                'alamat' => $request->alamat
            ]);

            // Setting
            $settingLembur = [
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'batas_pengajuan_lembur',
                    'value' => '17:00',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'batas_approval_lembur',
                    'value' => '23:59',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'onoff_batas_pengajuan_lembur',
                    'value' => 'Y',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'uang_makan',
                    'value' => 15000,
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'pembagi_upah_lembur_harian',
                    'value' => 173,
                ],

                //JAM ISTIRAHAT 1
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_mulai_1',
                    'value' => '12:00',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_selesai_1',
                    'value' => '12:45',
                ],

                //JAM ISTIRAHAT 2
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_mulai_2',
                    'value' => '18:00',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_selesai_2',
                    'value' => '18:45',
                ],

                //JAM ISTIRAHAT 3
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_mulai_3',
                    'value' => '02:30',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_selesai_3',
                    'value' => '03:15',
                ],

                //JAM ISTIRAHAT 4 (JUMAT)
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_mulai_jumat',
                    'value' => '11:30',
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'jam_istirahat_selesai_jumat',
                    'value' => '13:00',
                ],

                //DURASI ISTIRAHAT NORMAL
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'durasi_istirahat_1',
                    'value' => 45,
                ],

                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'durasi_istirahat_2',
                    'value' => 45,
                ],

                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'durasi_istirahat_3',
                    'value' => 45,
                ],

                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'durasi_istirahat_jumat',
                    'value' => 90,
                ],

                //INSENTIF SECTION HEAD
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'insentif_section_head_1',
                    'value' => 32500,
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'insentif_section_head_2',
                    'value' => 67500,
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'insentif_section_head_3',
                    'value' => 107500,
                ],
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'insentif_section_head_4',
                    'value' => 250000,
                ],

                //INSENTIF DEPTHEAD
                [
                    'organisasi_id' => $org->id_organisasi,
                    'setting_name' => 'insentif_department_head_4',
                    'value' => 400000,
                ],
            ];
            SettingLembur::insert($settingLembur);

            $personalia = User::create([
                'username' => $request->personalia_username,
                'email' => $request->personalia_email,
                'password' => bcrypt($request->personalia_password),
                'organisasi_id' => $org->id_organisasi
            ]);
            $personalia->assignRole('personalia');

            $security = User::create([
                'username' => $request->security_username,
                'email' => $request->security_email,
                'password' => bcrypt($request->security_password),
                'organisasi_id' => $org->id_organisasi
            ]);
            $security->assignRole('security');

            DB::commit();
            return response()->json(['message' => 'Organisasi Ditambahkan!'],200);
        } catch(Throwable $error){
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $dataValidate = [
            'nama_org_edit' => ['required'],
            'alamat_org_edit' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $org = Organisasi::find($id);

        DB::beginTransaction();
        try{
            $org->nama = $request->input('nama_org_edit');
            $org->alamat = $request->input('alamat_org_edit');
            $org->save();
            DB::commit();
            return response()->json(['message' => 'Organisasi Updated!'], 200);
        } catch(\Throwable $error){
            DB::rollback();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $org = Organisasi::findOrFail($id);
            $org->delete();
            DB::commit();
            return response()->json(['message' => 'Organisasi deleted!', 'id_organisasi' => $id], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting organisasi: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_data_organisasi()
    {
        $org = Organisasi::all();
        return response()->json(['data' => $org], 200);
    }

    public function validate_input(Request $request,string $table_name,string $column_name)
    {
        try {
            $dataValidate = [
                'value' => 'required|unique:'.$table_name.','.$column_name,
            ];

            $validator = Validator::make(request()->all(), $dataValidate);

            if ($validator->fails()) {
                return response()->json(false);
            } else {
                return response()->json(true);
            }
        } catch (Exception $e) {
            return response()->json(false);
        }
    }

    public function render_wizard()
    {
        try {
            $html = view('layouts.partials.superuser.setup-organisasi')->render();
            return response()->json(['message' => 'success', 'html' => $html], 200);
        } catch (Exception $e) {
            return response()->json(false);
        }
    }
}
