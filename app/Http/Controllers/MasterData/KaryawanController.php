<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Karyawan",
            'page' => 'masterdata-karyawan',
        ];
        return view('pages.master-data.karyawan.index', $dataPage);
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'id_karyawan',
            1 => 'karyawans.nama',
            3 => 'grups.nama',
            4 => 'jenis_kontrak',
            5 => 'tanggal_mulai',
            6 => 'tanggal_selesai',
            7 => 'status_karyawan'
        );

        $totalData = Karyawan::count();
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

        $karyawan = Karyawan::getData($dataFilter, $settings);
        $totalFiltered = Karyawan::countData($dataFilter);

        $dataTable = [];

        if (!empty($karyawan)) {
            foreach ($karyawan as $data) {
                $kontrak = Kontrak::where('karyawan_id', $data->id_karyawan)->orderBy('tanggal_mulai', 'DESC')->pluck('jenis')->first();
                $posisis = $data->posisi()->pluck('posisis.nama')->toArray();
                $nestedData['id_karyawan'] = $data->id_karyawan;
                $nestedData['nama'] = $data->nama.'<br> ('.$data->ni_karyawan.')';
                $nestedData['jenis_kontrak'] = $kontrak ? $kontrak : $data->jenis_kontrak;
                $nestedData['tanggal_mulai'] = $data->tanggal_mulai;
                $nestedData['tanggal_selesai'] = $data->tanggal_selesai;
                $nestedData['status_karyawan'] = $data->status_karyawan;
                $formattedPosisi = array_map(function($posisi) {
                    return '<span class="badge badge-primary m-1">' . $posisi . '</span>';
                }, $posisis);
                $nestedData['posisi'] = implode(' ', $formattedPosisi);
                $nestedData['grup'] = $data->nama_grup;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-secondary btnKontrak" data-id="'.$data->id_karyawan.'" data-nama="'.$data->nama.'"><i class="fas fa-file-signature"></i> Kontrak</button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-info btnAkun" data-id="'.$data->user_id.'" data-id-karyawan="'.$data->id_karyawan.'" data-nama="'.$data->nama.'"><i class="fas fa-user-circle"></i> Akun</button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_karyawan.'"><i class="fas fa-edit"></i> Detail</button>
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_karyawan.'"><i class="fas fa-trash-alt"></i> Hapus</button>
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
            'nama' => ['required'],
            'ni_karyawan' => ['required'],
            'no_ktp' => ['nullable','numeric'],
            'nik' => ['nullable','numeric'],
            'tempat_lahir' => ['nullable','string'],
            'tanggal_lahir' => ['nullable','date_format:Y-m-d'],
            'jenis_kelamin' => ['required'],
            'agama' => ['nullable', 'string'],
            'gol_darah' => ['nullable', 'string'],
            'status_keluarga' => ['nullable', 'string'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable','numeric'],
            'email' => ['nullable', 'email', 'unique:karyawans,email'],
            'npwp' => ['nullable', 'numeric'],
            'no_bpjs_ks' => ['nullable','numeric'],
            'no_bpjs_kt' => ['nullable','numeric'],
            'posisi.*' => ['required'],
            'grup' => ['required'],
            'sisa_cuti' => ['required','numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama = $request->nama;
        $ni_karyawan = $request->ni_karyawan;
        $no_ktp = $request->no_ktp;
        $nik = $request->nik;
        $tempat_lahir = $request->tempat_lahir;
        $tanggal_lahir = $request->tanggal_lahir;
        $jenis_kelamin = $request->jenis_kelamin;
        $agama = $request->agama;
        $gol_darah = $request->gol_darah;
        $status_keluarga = $request->status_keluarga;
        $alamat = $request->alamat;
        $no_telp = $request->no_telp;
        $email = $request->email;
        $npwp = $request->npwp;
        $no_bpjs_ks = $request->no_bpjs_ks;
        $no_bpjs_kt = $request->no_bpjs_kt;
        $posisi = $request->posisi;
        $grup_id = $request->grup;
        $sisa_cuti = $request->sisa_cuti;
        $user_id = $request->user_id;
        $email_akun = $request->email_akun;
        $username = $request->username;
        $password = $request->password;

        DB::beginTransaction();
        try{
            if($user_id == null){
                if($username !== null && $email_akun !== null && $password !== null){
                    $user = User::create([
                        'username' => $username,
                        'email' => $email_akun,
                        'password' => Hash::make($password),
                    ]); 

                    $cek_jabatan = Posisi::find($posisi[0]);
                    if($cek_jabatan){
                        if($cek_jabatan->jabatan_id !== 6){
                            $user->assignRole('atasan');
                        } else {
                            $user->assignRole('member');
                        }
                    }

                    $user_id = $user->id;
                }else{
                    return response()->json(['message' => 'Email Akun, Username dan Password tidak boleh kosong!'], 500);
                }
            } 

            $karyawan = Karyawan::create([
                'id_karyawan' => $this->generateIdKaryawan($nama),
                'ni_karyawan' => $ni_karyawan,
                'user_id' => $user_id,
                'nama' => $nama,
                'no_ktp' => $no_ktp,
                'nik' => $nik,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'agama' => $agama,
                'gol_darah' => $gol_darah,
                'status_keluarga' => $status_keluarga,
                'alamat' => $alamat,
                'no_telp' => $no_telp,
                'email' => $email,
                'npwp' => $npwp,
                'no_bpjs_ks' => $no_bpjs_ks,
                'no_bpjs_kt' => $no_bpjs_kt,
                'grup_id' => $grup_id,
                'sisa_cuti' => $sisa_cuti,
            ]);

            $jabatan = null;
            foreach($posisi as $posisi_id){
                $posisi_cek = Posisi::find($posisi_id);
                if ($posisi_cek && $jabatan !== null) {
                    if($posisi_cek->jabatan_id !== $jabatan){
                        DB::rollBack();
                        return response()->json(['message' => 'Posisi yang dipilih harus memiliki jabatan yang sama!'], 500);
                    } 
                } else {
                    $jabatan = $posisi_cek->jabatan_id;
                }
                $karyawan->posisi()->attach($posisi_id);
            }

            DB::commit();
            return response()->json(['message' => 'Karyawan Ditambahkan!'],200);
        } catch(Throwable $error){
            DB::rollBack();
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
    public function update(Request $request, string $id_karyawan)
    {
        $dataValidate = [
            'namaEdit' => ['required'],
            'ni_karyawanEdit' => ['required'],
            'no_ktpEdit' => ['nullable','numeric'],
            'nikEdit' => ['nullable','numeric'],
            'tempat_lahirEdit' => ['nullable','string'],
            'tanggal_lahirEdit' => ['nullable','date_format:Y-m-d'],
            'jenis_kelaminEdit' => ['required'],
            'agamaEdit' => ['nullable', 'string'],
            'gol_darahEdit' => ['nullable', 'string'],
            'status_keluargaEdit' => ['nullable', 'string'],
            'alamatEdit' => ['nullable', 'string'],
            'no_telpEdit' => ['nullable','numeric'],
            'npwpEdit' => ['nullable', 'numeric'],
            'no_bpjs_ksEdit' => ['nullable','numeric'],
            'no_bpjs_ktEdit' => ['nullable','numeric'],
            'posisiEdit.*' => ['required'],
            'grupEdit' => ['required'],
            'sisa_cutiEdit' => ['required','numeric'],
        ];

        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama = $request->namaEdit;
        $ni_karyawan = $request->ni_karyawanEdit;
        $no_ktp = $request->no_ktpEdit;
        $nik = $request->nikEdit;
        $tempat_lahir = $request->tempat_lahirEdit;
        $tanggal_lahir = $request->tanggal_lahirEdit;
        $jenis_kelamin = $request->jenis_kelaminEdit;
        $agama = $request->agamaEdit;
        $gol_darah = $request->gol_darahEdit;
        $status_keluarga = $request->status_keluargaEdit;
        $alamat = $request->alamatEdit;
        $no_telp = $request->no_telpEdit;
        $email = $request->emailEdit;
        $npwp = $request->npwpEdit;
        $no_bpjs_ks = $request->no_bpjs_ksEdit;
        $no_bpjs_kt = $request->no_bpjs_ktEdit;
        $status_karyawan = $request->status_karyawanEdit;
        $posisi = $request->posisiEdit;
        $grup_id = $request->grupEdit;
        $sisa_cuti = $request->sisa_cutiEdit;

        DB::beginTransaction();
        try{
            $karyawan = Karyawan::find($id_karyawan);
            $karyawan->nama = $nama;
            $karyawan->ni_karyawan = $ni_karyawan;
            $karyawan->no_ktp = $no_ktp;
            $karyawan->nik = $nik;
            $karyawan->tempat_lahir = $tempat_lahir;
            $karyawan->tanggal_lahir = $tanggal_lahir;
            $karyawan->jenis_kelamin = $jenis_kelamin;
            $karyawan->agama = $agama;
            $karyawan->gol_darah = $gol_darah;
            $karyawan->status_keluarga = $status_keluarga;
            $karyawan->alamat = $alamat;    
            $karyawan->no_telp = $no_telp;
            $karyawan->email = $email;
            $karyawan->npwp = $npwp;
            $karyawan->no_bpjs_ks = $no_bpjs_ks;
            $karyawan->no_bpjs_kt = $no_bpjs_kt;
            $karyawan->status_karyawan = $status_karyawan;
            $karyawan->grup_id = $grup_id;
            $karyawan->sisa_cuti = $sisa_cuti;
            $karyawan->posisi()->detach();

            $user = $karyawan->user;
            $jabatan_cek = Posisi::find($posisi[0]);
            if($jabatan_cek){
                if($jabatan_cek->jabatan_id !== 6){
                    $user->roles()->detach();
                    $user->assignRole('atasan');
                } else {
                    $user->roles()->detach();
                    $user->assignRole('member');
                }
            }

            $jabatan = null;
            foreach($posisi as $posisi_id){
                $posisi_cek = Posisi::find($posisi_id);
                if ($posisi_cek && $jabatan !== null) {
                    if($posisi_cek->jabatan_id !== $jabatan){
                        DB::rollBack();
                        return response()->json(['message' => 'Posisi yang dipilih harus memiliki jabatan yang sama!'], 500);
                    } 
                } else {
                    $jabatan = $posisi_cek->jabatan_id;
                }
                $karyawan->posisi()->attach($posisi_id);
            }
            
            // foreach($posisi as $posisi_id){
            //     $karyawan->posisi()->attach($posisi_id);
            // }

            $karyawan->save();

            DB::commit();
            return response()->json(['message' => 'Karyawan Diupdate!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try{
            $karyawan = Karyawan::find($id);
            $karyawan->posisi()->detach();
            $karyawan->user()->delete();
            $karyawan->delete();
            DB::commit();
            return response()->json(['message' => 'Karyawan Dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_data_user(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = User::select(
            'id',
            'username',
        );

        $query->whereDoesntHave('karyawan')->whereNotIn('username', ['PERSONALIA', 'SUPERUSER']);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('username', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $user) {
            $dataUser[] = [
                'id' => $user->id,
                'text' => $user->username
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function get_data_karyawan(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Karyawan::select(
            'id_karyawan',
            'nama',
        );

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('nama', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $karyawan) {
            $dataUser[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    public function get_data_detail_karyawan(string $id_karyawan)
    {
        $karyawan = Karyawan::find($id_karyawan);
        $detail = [];
        if($karyawan){
            $detail = [
                'id_karyawan' => $karyawan->id_karyawan,
                'ni_karyawan' => $karyawan->ni_karyawan,
                'nama' => $karyawan->nama,
                'no_ktp' => $karyawan->no_ktp,
                'nik' => $karyawan->nik,
                'tempat_lahir' => $karyawan->tempat_lahir,
                'tanggal_lahir' => $karyawan->tanggal_lahir,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'agama' => $karyawan->agama,
                'gol_darah' => $karyawan->gol_darah,
                'status_keluarga' => $karyawan->status_keluarga,
                'alamat' => $karyawan->alamat,
                'no_telp' => $karyawan->no_telp,
                'email' => $karyawan->email,
                'npwp' => $karyawan->npwp,
                'no_bpjs_ks' => $karyawan->no_bpjs_ks,
                'no_bpjs_kt' => $karyawan->no_bpjs_kt,
                'jenis_kontrak' => $karyawan->jenis_kontrak,
                'status_karyawan' => $karyawan->status_karyawan,
                'sisa_cuti' => $karyawan->sisa_cuti,
                'tanggal_mulai' => $karyawan->tanggal_mulai,
                'tanggal_selesai' => $karyawan->tanggal_selesai,
                'posisi' => $karyawan->posisi()->pluck('posisis.id_posisi'),
                'grup_id' => $karyawan->grup_id,
            ];
            return response()->json(['data' => $detail], 200);
        } else {
            return response()->json(['message' => 'Data Karyawan tidak ditemukan!'], 404);
        }

    }

    function generateIdKaryawan($name)
    {
        $words = explode(' ', $name);

        if (count($words) === 1) {
            $initials = substr($name, 0, 2);
        } else {
            $initials = substr($words[0], 0, 1) . substr($words[1], 0, 1);
        }

        $timestamp = now()->timestamp;
        $baseString = $initials . $timestamp;

        return $baseString;
    }

    public function upload_karyawan(Request $request)
    {
        $file = $request->file('karyawan_file');

        $validator = Validator::make($request->all(), [
            'karyawan_file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'File Harus bertipe Excel!'], 400);
        }

        DB::beginTransaction();
        try {

            if($request->hasFile('karyawan_file')){
                $karyawan_records = 'KR_' . time() . '.' . $file->getClientOriginalExtension();
                $karyawan_file = $file->storeAs("attachment/upload-karyawan", $karyawan_records);
            } 

            if (file_exists(storage_path("app/public/".$karyawan_file))) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/public/".$karyawan_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();

                foreach ($data as $index => $row) {
                    if ($index < 1) { 
                        continue;
                    }

                    if($row[5] !== null){
                        $tanggal_lahir = Carbon::createFromFormat('m/d/Y', $row[5])->format('Y-m-d');
                    } else {
                        $tanggal_lahir = null;
                    }

                    if($row[16] > 12 || $row[16] < 0){
                        return response()->json(['message' => 'Sisa cuti harus berupa angka dari 0-12'], 402);
                    }

                    $existingKaryawan = Karyawan::where('nama', strtoupper($row[1]))->first();

                    if ($existingKaryawan) {
                        $existingKaryawan->update([
                            'ni_karyawan' => $row[0],
                            'no_ktp' => $row[2],
                            'nik' => $row[3],
                            'tempat_lahir' => $row[4],
                            'tanggal_lahir' => $tanggal_lahir,
                            'alamat' => $row[6],
                            'email' => $row[7],
                            'no_telp' => $row[8],
                            'gol_darah' => $row[9] !== null ? strtoupper($row[9]) : $row[9],
                            'jenis_kelamin' => $row[10] !== null ? strtoupper($row[10]) : $row[10],
                            'agama' => $row[11] !== null ? strtoupper($row[11]) : $row[11],
                            'status_keluarga' => $row[12] !== null ? strtoupper($row[12]) : $row[12],
                            'npwp' => $row[13],
                            'no_bpjs_ks' => $row[14],
                            'no_bpjs_kt' => $row[15],
                            'sisa_cuti' => $row[16],
                        ]);

                        if($row[17] !== null && $row[18] !== null && $row[19] !== null){
                            $user = User::find($existingKaryawan->user_id);
                            $user->update([
                                'email' => $row[17],
                                'username' => $row[18],
                                'password' => Hash::make($row[19]),
                            ]); 
                        }
                        continue;
                    }

                    $user = User::create([
                        'email' => $row[17],
                        'username' => $row[18],
                        'password' => Hash::make($row[19]),
                    ]); 
    
                    Karyawan::create([
                        'user_id' => $user->id,
                        'id_karyawan' => $this->generateIdKaryawan(strtoupper($row[1])),
                        'ni_karyawan' => $row[0],
                        'nama' => strtoupper($row[1]),
                        'no_ktp' => $row[2],
                        'nik' => $row[3],
                        'tempat_lahir' => $row[4],
                        'tanggal_lahir' => $tanggal_lahir,
                        'alamat' => $row[6],
                        'email' => $row[7],
                        'no_telp' => $row[8],
                        'gol_darah' => $row[9] !== null ? strtoupper($row[9]) : $row[9],
                        'jenis_kelamin' => $row[10] !== null ? strtoupper($row[10]) : $row[10],
                        'agama' => $row[11] !== null ? strtoupper($row[11]) : $row[11],
                        'status_keluarga' => $row[12] !== null ? strtoupper($row[12]) : $row[12],
                        'npwp' => $row[13],
                        'no_bpjs_ks' => $row[14],
                        'no_bpjs_kt' => $row[15],
                        'sisa_cuti' => $row[16],
                    ]);
                    
                }
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Terjadi kesalahan, silahkan upload ulang file!'], 404);
            }
            DB::commit();
            return response()->json(['message' => 'File berhasil di upload'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing the file: ' . $e->getMessage()], 500);
        }
    }
}
