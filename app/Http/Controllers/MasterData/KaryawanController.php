<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
            5 => 'status_karyawan'
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
                $posisis = $data->posisi()->pluck('posisis.nama')->toArray();
                $nestedData['id_karyawan'] = $data->id_karyawan;
                $nestedData['nama'] = $data->nama;
                $nestedData['jenis_kontrak'] = $data->jenis_kontrak;
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
            'tahun_masuk' => ['required'],
            'posisi.*' => ['required'],
            'grup' => ['required'],
            'sisa_cuti' => ['required','numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama = $request->nama;
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
        $tahun_masuk = $request->tahun_masuk;
        $status_karyawan = 'AKTIF';
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

                    $user_id = $user->id;
                }else{
                    return response()->json(['message' => 'Email Akun, Username dan Password tidak boleh kosong!'], 500);
                }
            } 

            $karyawan = Karyawan::create([
                'id_karyawan' => $this->generateIdKaryawan($nama),
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
                'tahun_masuk' => $tahun_masuk,
                'status_karyawan' => $status_karyawan,
                'grup_id' => $grup_id,
                'sisa_cuti' => $sisa_cuti,
            ]);

            foreach($posisi as $posisi_id){
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
            'tahun_masukEdit' => ['required'],
            'posisiEdit.*' => ['required'],
            'grupEdit' => ['required'],
            'sisa_cutiEdit' => ['required','numeric'],
            'status_karyawanEdit' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama = $request->namaEdit;
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
        $tahun_masuk = $request->tahun_masukEdit;
        $status_karyawan = $request->status_karyawanEdit;
        $posisi = $request->posisiEdit;
        $grup_id = $request->grupEdit;
        $sisa_cuti = $request->sisa_cutiEdit;

        DB::beginTransaction();
        try{
            $karyawan = Karyawan::find($id_karyawan);
            $karyawan->nama = $nama;
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
            $karyawan->tahun_masuk = $tahun_masuk;
            $karyawan->status_karyawan = $status_karyawan;
            $karyawan->grup_id = $grup_id;
            $karyawan->sisa_cuti = $sisa_cuti;
            $karyawan->posisi()->detach();

            foreach($posisi as $posisi_id){
                $karyawan->posisi()->attach($posisi_id);
            }

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

        $query->whereDoesntHave('karyawan');

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

    public function get_data_detail_karyawan(string $id_karyawan)
    {
        $karyawan = Karyawan::find($id_karyawan);
        $detail = [];
        if($karyawan){
            $detail = [
                'id_karyawan' => $karyawan->id_karyawan,
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
                'tahun_masuk' => $karyawan->tahun_masuk,
                'tahun_keluar' => $karyawan->tahun_keluar,
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
}
