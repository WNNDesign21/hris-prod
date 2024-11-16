<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Grup;
use App\Models\User;
use App\Models\Event;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\Departemen;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::all();
        $posisi = Posisi::all();
        $grup = Grup::all();
        $dataPage = [
            'pageTitle' => "Master Data - Karyawan",
            'page' => 'masterdata-karyawan',
            'departemen' => $departemen,
            'grup' => $grup,
        ];
        return view('pages.master-data.karyawan.index', $dataPage);
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'karyawans.ni_karyawan',
            1 => 'karyawans.nama',
            2 => 'departemens.nama',
            4 => 'grups.nama',
            5 => 'jenis_kontrak',
            6 => 'tanggal_mulai',
            7 => 'tanggal_selesai',
            8 => 'status_karyawan',
            9 => 'nik',
            10 => 'no_kk',
            11 => 'tempat_lahir',
            12 => 'tanggal_lahir',
            13 => 'jenis_kelamin',  
            14 => 'agama',
            15 => 'alamat',
            16 => 'domisili',
            17 => 'npwp',
            18 => 'no_bpjs_ks',
            19 => 'no_bpjs_kt',
            20 => 'no_telp',
            21 => 'email',
            22 => 'nama_bank',
            23 => 'no_rekening',
            24 => 'nama_rekening',
            25 => 'nama_ibu_kandung',
            26 => 'jenjang_pendidikan',
            27 => 'jurusan_pendidikan',
            28 => 'no_telp_darurat',
            29 => 'gol_darah',
            31 => 'hutang_cuti'
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


        //FILTER DATA
        $departemenFilter = $request->input('departemen');
        if (!empty($departemenFilter)) {
            $dataFilter['departemen'] = $departemenFilter;
        }
        $grupFilter = $request->input('grup');
        if (!empty($grupFilter)) {
            $dataFilter['grup'] = $grupFilter;
        }
        $jeniskontrakFilter = $request->input('jenisKontrak');
        if (!empty($jeniskontrakFilter)) {
            $dataFilter['jenisKontrak'] = $jeniskontrakFilter;
        }
        $statuskaryawanFilter = $request->input('statusKaryawan');
        if (!empty($statuskaryawanFilter)) {
            $dataFilter['statusKaryawan'] = $statuskaryawanFilter;
        }
        $jeniskelaminFilter = $request->input('jenisKelamin');
        if (!empty($jeniskelaminFilter)) {
            $dataFilter['jenisKelamin'] = $jeniskelaminFilter;
        }
        $agamaFilter = $request->input('agama');
        if (!empty($agamaFilter)) {
            $dataFilter['agama'] = $agamaFilter;
        }
        $golongandarahFilter = $request->input('golonganDarah');
        if (!empty($golongandarahFilter)) {
            $dataFilter['golonganDarah'] = $golongandarahFilter;
        }
        $statuskeluargaFilter = $request->input('statusKeluarga');
        if (!empty($statuskeluargaFilter)) {
            $dataFilter['statusKeluarga'] = $statuskeluargaFilter;
        }
        $kategorikeluargaFilter = $request->input('kategoriKeluarga');
        if (!empty($kategorikeluargaFilter)) {
            $dataFilter['kategoriKeluarga'] = $kategorikeluargaFilter;
        }
        $namabankFilter = $request->input('namaBank');
        if (!empty($namabankFilter)) {
            $dataFilter['namaBank'] = $namabankFilter;
        }
        $namaFilter = $request->input('nama');
        if (!empty($namaFilter)) {
            $dataFilter['nama'] = $namaFilter;
        }
        $nikFilter = $request->input('nik');
        if (!empty($nikFilter)) {
            $dataFilter['nik'] = $nikFilter;
        }

        $karyawan = Karyawan::getData($dataFilter, $settings);
        $totalFiltered = Karyawan::countData($dataFilter);

        $dataTable = [];

        if (!empty($karyawan)) {
            foreach ($karyawan as $data) {
                if($data->status_karyawan == 'AT'){
                    $status_karyawan_text = 'AKTIF';
                } elseif ($data->status_karyawan == 'MD') {
                    $status_karyawan_text = 'MENGUNDURKAN DIRI';
                } elseif ($data->status_karyawan == 'HK') {
                    $status_karyawan_text = 'HABIS KONTRAK';
                } elseif ($data->status_karyawan == 'PS') {
                    $status_karyawan_text = 'PENSIUN';
                } elseif ($data->status_karyawan == 'TM') {
                    $status_karyawan_text = 'TERMINASI';
                } else {
                    $status_karyawan_text = '-';
                }
                $kontrak = Kontrak::where('karyawan_id', $data->id_karyawan)->orderBy('tanggal_mulai', 'DESC')->pluck('jenis')->first();
                $posisis = $data->posisi()->pluck('posisis.nama')->toArray();
                $nestedData['ni_karyawan'] = $data->ni_karyawan;
                $nestedData['nama'] = $data->nama;
                $nestedData['jenis_kontrak'] = $kontrak ? $kontrak : ($data->jenis_kontrak ? $data->jenis_kontrak : 'BELUM ADA KONTRAK');
                $nestedData['tanggal_mulai'] = $data->tanggal_mulai ? $data->tanggal_mulai : 'BELUM ADA KONTRAK';
                $nestedData['tanggal_selesai'] = $data->tanggal_selesai ? $data->tanggal_selesai : ($kontrak == 'PKWTT' || $data->jenis_kontrak == 'PKWTT' ? '-' : 'BELUM ADA KONTRAK');
                $nestedData['status_karyawan'] = $status_karyawan_text;
                $formattedPosisi = array_map(function($posisi) {
                    return '<span class="badge badge-primary m-1">' . $posisi . '</span>';
                }, $posisis);
                $nestedData['posisi'] = implode(' ', $formattedPosisi);
                $nestedData['grup'] = $data->nama_grup;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['nik'] = $data->nik;
                $nestedData['no_kk'] = $data->no_kk;
                $nestedData['tempat_lahir'] = $data->tempat_lahir;
                $nestedData['tanggal_lahir'] = $data->tanggal_lahir;
                $nestedData['jenis_kelamin'] = $data->jenis_kelamin;
                $nestedData['agama'] = $data->agama;
                $nestedData['gol_darah'] = $data->gol_darah;
                $nestedData['status_keluarga'] = $data->status_keluarga;
                $nestedData['kategori_keluarga'] = $data->kategori_keluarga;
                $nestedData['alamat'] = $data->alamat;
                $nestedData['domisili'] = $data->domisili;
                $nestedData['npwp'] = $data->npwp;
                $nestedData['no_bpjs_ks'] = $data->no_bpjs_ks;
                $nestedData['no_bpjs_kt'] = $data->no_bpjs_kt;
                $nestedData['no_telp'] = $data->notelp_karyawan;
                $nestedData['email'] = $data->email_karyawan;
                $nestedData['nama_bank'] = $data->nama_bank;
                $nestedData['no_rekening'] = $data->no_rekening;
                $nestedData['nama_rekening'] = $data->nama_rekening;
                $nestedData['nama_ibu_kandung'] = $data->nama_ibu_kandung;
                $nestedData['jenjang_pendidikan'] = $data->jenjang_pendidikan;
                $nestedData['jurusan_pendidikan'] = $data->jurusan_pendidikan;
                $nestedData['no_telp_darurat'] = $data->no_telp_darurat;
                $nestedData['gol_darah'] = $data->gol_darah;
                $nestedData['sisa_cuti'] = 'Cuti Pribadi : '.$data->sisa_cuti_pribadi.'<br> Cuti Bersama : '. $data->sisa_cuti_bersama.'<br> Cuti Tahun Lalu : '. $data->sisa_cuti_tahun_lalu.'<br> Expired Cuti Tahun Lalu : '. $data->expired_date_cuti_tahun_lalu;
                $nestedData['hutang_cuti'] = $data->hutang_cuti;
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
            'no_kk' => ['required','numeric'],
            'nik' => ['required','numeric'],
            'tempat_lahir' => ['required','string'],
            'tanggal_lahir' => ['required','date_format:Y-m-d'],
            'jenis_kelamin' => ['required'],
            'agama' => ['required', 'string'],
            'gol_darah' => ['required', 'string'],
            'status_keluarga' => ['required', 'string'],
            'kategori_keluarga' => ['required', 'string'],
            'alamat' => ['required', 'string'],
            'domisili' => ['required', 'string'],
            'no_telp' => ['required','numeric'],
            'no_telp_darurat' => ['required','numeric'],
            'email' => ['required', 'email', 'unique:karyawans,email'],
            'npwp' => ['required'],
            'no_bpjs_ks' => ['required','numeric'],
            'no_bpjs_kt' => ['required','numeric'],
            'no_rekening' => ['required', 'numeric'],
            'nama_rekening' => ['required', 'string'],
            'nama_bank' => ['required', 'string'],
            'nama_ibu_kandung' => ['required', 'string'],
            'jenjang_pendidikan' => ['required', 'string'],
            'jurusan_pendidikan' => ['required', 'string'],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'posisi.*' => ['required'],
            'grup' => ['required'],
            'foto' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama = $request->nama;
        $ni_karyawan = $request->ni_karyawan;
        $no_kk = $request->no_kk;
        $nik = $request->nik;
        $tempat_lahir = $request->tempat_lahir;
        $tanggal_lahir = $request->tanggal_lahir;
        $jenis_kelamin = $request->jenis_kelamin;
        $agama = $request->agama;
        $gol_darah = $request->gol_darah;
        $status_keluarga = $request->status_keluarga;
        $kategori_keluarga = $request->kategori_keluarga;
        $alamat = $request->alamat;
        $domisili = $request->domisili;
        $no_telp = $request->no_telp;
        $no_telp_darurat = $request->no_telp_darurat;
        $email = $request->email;
        $npwp = $request->npwp;
        $no_bpjs_ks = $request->no_bpjs_ks;
        $no_bpjs_kt = $request->no_bpjs_kt;
        $no_rekening = $request->no_rekening;
        $nama_rekening = $request->nama_rekening;
        $nama_bank = $request->nama_bank;
        $nama_ibu_kandung = $request->nama_ibu_kandung;
        $jenjang_pendidikan = $request->jenjang_pendidikan;
        $jurusan_pendidikan = $request->jurusan_pendidikan;
        $posisi = $request->posisi;
        $tanggal_mulai = $request->tanggal_mulai;
        $grup_id = $request->grup;
        $user_id = $request->user_id;
        $email_akun = $request->email_akun;
        $username = $request->username;
        $password = $request->password;
        $foto = $request->file('foto');
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try{
            $id_karyawan = $this->generateIdKaryawan($nama);

            if($user_id == null){
                if($username !== null && $email_akun !== null && $password !== null){
                    $user = User::create([
                        'username' => $username,
                        'email' => $email_akun,
                        'password' => Hash::make($password),
                        'organisasi_id' => $organisasi_id,
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

            if($request->hasFile('foto')){
                $foto_karyawan = $id_karyawan . '_' . time() . '.' . $foto->getClientOriginalExtension();
                $file_path = $foto->storeAs("attachment/foto_karyawan", $foto_karyawan);
            } else {
                $file_path = null;
            }

            $karyawan = Karyawan::create([
                'id_karyawan' => $id_karyawan,
                'foto' => $file_path,
                'organisasi_id' => $organisasi_id,
                'ni_karyawan' => $ni_karyawan,
                'user_id' => $user_id,
                'nama' => $nama,
                'no_kk' => $no_kk,
                'nik' => $nik,
                'tempat_lahir' => $tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'agama' => $agama,
                'gol_darah' => $gol_darah,
                'status_keluarga' => $status_keluarga,
                'kategori_keluarga' => $kategori_keluarga,
                'alamat' => $alamat,
                'domisili' => $domisili,
                'no_telp' => $no_telp,
                'no_telp_darurat' => $no_telp_darurat,
                'email' => $email,
                'npwp' => $npwp,
                'no_bpjs_ks' => $no_bpjs_ks,
                'no_bpjs_kt' => $no_bpjs_kt,
                'no_rekening' => $no_rekening,
                'nama_rekening' => $nama_rekening,
                'nama_bank' => $nama_bank,
                'nama_ibu_kandung' => $nama_ibu_kandung,
                'jenjang_pendidikan' => $jenjang_pendidikan,
                'jurusan_pendidikan' => $jurusan_pendidikan,
                'grup_id' => $grup_id,
                'tanggal_mulai' => $tanggal_mulai,
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
            'no_kkEdit' => ['required','numeric'],
            'nikEdit' => ['required','numeric'],
            'tempat_lahirEdit' => ['required','string'],
            'tanggal_lahirEdit' => ['required','date_format:Y-m-d'],
            'jenis_kelaminEdit' => ['required'],
            'agamaEdit' => ['required', 'string'],
            'gol_darahEdit' => ['required', 'string'],
            'status_keluargaEdit' => ['required', 'string'],
            'kategori_keluargaEdit' => ['required', 'string'],
            'alamatEdit' => ['required', 'string'],
            'domisiliEdit' => ['required', 'string'],
            'no_telpEdit' => ['required','numeric'],
            'no_telp_daruratEdit' => ['required','numeric'],
            'npwpEdit' => ['required'],
            'no_bpjs_ksEdit' => ['required','numeric'],
            'no_bpjs_ktEdit' => ['required','numeric'],
            'no_rekeningEdit' => ['required','numeric'],
            'nama_rekeningEdit' => ['required','string'],
            'nama_bankEdit' => ['required','string'],
            'nama_ibu_kandungEdit' => ['required','string'],
            'jenjang_pendidikanEdit' => ['required','string'],
            'jurusan_pendidikanEdit' => ['required','string'],
            'posisiEdit.*' => ['required'],
            'grupEdit' => ['required'],
            'fotoEdit' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'sisa_cuti_pribadiEdit' => ['required','numeric'],
            'sisa_cuti_bersamaEdit' => ['required','numeric'],
            'sisa_cuti_tahun_laluEdit' => ['required','numeric'],
        ];

        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $nama = $request->namaEdit;
        $ni_karyawan = $request->ni_karyawanEdit;
        $no_kk = $request->no_kkEdit;
        $nik = $request->nikEdit;
        $tempat_lahir = $request->tempat_lahirEdit;
        $tanggal_lahir = $request->tanggal_lahirEdit;
        $jenis_kelamin = $request->jenis_kelaminEdit;
        $agama = $request->agamaEdit;
        $gol_darah = $request->gol_darahEdit;
        $status_keluarga = $request->status_keluargaEdit;
        $kategori_keluarga = $request->kategori_keluargaEdit;
        $alamat = $request->alamatEdit;
        $domisili = $request->domisiliEdit;
        $no_telp = $request->no_telpEdit;
        $no_telp_darurat = $request->no_telp_daruratEdit;
        $email = $request->emailEdit;
        $npwp = $request->npwpEdit;
        $no_bpjs_ks = $request->no_bpjs_ksEdit;
        $no_bpjs_kt = $request->no_bpjs_ktEdit;
        $no_rekening = $request->no_rekeningEdit;
        $nama_rekening = $request->nama_rekeningEdit;
        $nama_bank = $request->nama_bankEdit;
        $nama_ibu_kandung = $request->nama_ibu_kandungEdit;
        $jenjang_pendidikan = $request->jenjang_pendidikanEdit;
        $jurusan_pendidikan = $request->jurusan_pendidikanEdit;
        $posisi = $request->posisiEdit;
        $grup_id = $request->grupEdit;
        $foto = $request->file('fotoEdit');
        $sisa_cuti_pribadi = $request->sisa_cuti_pribadiEdit;
        $sisa_cuti_bersama = $request->sisa_cuti_bersamaEdit;
        $sisa_cuti_tahun_lalu = $request->sisa_cuti_tahun_laluEdit;
        $expired_date_cuti_tahun_lalu = $request->expired_date_cuti_tahun_laluEdit;

        DB::beginTransaction();
        try{
            $karyawan = Karyawan::find($id_karyawan);
            $karyawan->nama = $nama;
            $karyawan->ni_karyawan = $ni_karyawan;
            $karyawan->no_kk = $no_kk;
            $karyawan->nik = $nik;
            $karyawan->tempat_lahir = $tempat_lahir;
            $karyawan->tanggal_lahir = $tanggal_lahir;
            $karyawan->jenis_kelamin = $jenis_kelamin;
            $karyawan->agama = $agama;
            $karyawan->gol_darah = $gol_darah;
            $karyawan->status_keluarga = $status_keluarga;
            $karyawan->kategori_keluarga = $kategori_keluarga;
            $karyawan->alamat = $alamat;    
            $karyawan->domisili = $domisili;    
            $karyawan->no_telp = $no_telp;
            $karyawan->no_telp_darurat = $no_telp_darurat;
            $karyawan->email = $email;
            $karyawan->npwp = $npwp;
            $karyawan->no_bpjs_ks = $no_bpjs_ks;
            $karyawan->no_bpjs_kt = $no_bpjs_kt;
            $karyawan->no_rekening = $no_rekening;
            $karyawan->nama_rekening = $nama_rekening;
            $karyawan->nama_bank = $nama_bank;
            $karyawan->nama_ibu_kandung = $nama_ibu_kandung;
            $karyawan->jenjang_pendidikan = $jenjang_pendidikan;
            $karyawan->jurusan_pendidikan = $jurusan_pendidikan;
            $karyawan->grup_id = $grup_id;
            $karyawan->sisa_cuti_pribadi = $sisa_cuti_pribadi;
            $karyawan->sisa_cuti_bersama = $sisa_cuti_bersama;
            $karyawan->sisa_cuti_tahun_lalu = $sisa_cuti_tahun_lalu;
            $karyawan->expired_date_cuti_tahun_lalu = $expired_date_cuti_tahun_lalu;
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

            if($request->hasFile('fotoEdit')){
                $foto_karyawan = $id_karyawan . '_' . time() . '.' . $foto->getClientOriginalExtension();
                $file_path = $foto->storeAs("attachment/foto_karyawan", $foto_karyawan);
                if($karyawan->foto){
                    Storage::delete($karyawan->foto);
                }
                $karyawan->foto = $file_path;
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

        $organisasi_id = auth()->user()->organisasi_id;
        if($organisasi_id){
            $query->organisasi($organisasi_id);
        }

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

        $organisasi_id = auth()->user()->organisasi_id;
        if($organisasi_id){
            $query->organisasi($organisasi_id);
        }

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('nama', 'ILIKE', "%{$search}%");
            });
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $status = $request->input('status');
        if (!empty($status)) {
            $query->aktif();
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
            if($karyawan->status_karyawan == 'AT'){
                $status_karyawan_text = 'AKTIF';
            } elseif ($karyawan->status_karyawan == 'MD') {
                $status_karyawan_text = 'MENGUNDURKAN DIRI';
            } elseif ($karyawan->status_karyawan == 'HK') {
                $status_karyawan_text = 'HABIS KONTRAK';
            } elseif ($karyawan->status_karyawan == 'PS') {
                $status_karyawan_text = 'PENSIUN';
            } elseif ($karyawan->status_karyawan == 'TM') {
                $status_karyawan_text = 'TERMINASI';
            } else {
                $status_karyawan_text = '-';
            }

            $detail = [
                'id_karyawan' => $karyawan->id_karyawan,
                'ni_karyawan' => $karyawan->ni_karyawan,
                'foto' => $karyawan->foto ? asset('storage/'.$karyawan->foto) : asset('img/no-image.png'),
                'nama' => $karyawan->nama,
                'no_kk' => $karyawan->no_kk,
                'nik' => $karyawan->nik,
                'tempat_lahir' => $karyawan->tempat_lahir,
                'tanggal_lahir' => $karyawan->tanggal_lahir,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'agama' => $karyawan->agama,
                'gol_darah' => $karyawan->gol_darah,
                'status_keluarga' => $karyawan->status_keluarga,
                'kategori_keluarga' => $karyawan->kategori_keluarga,
                'alamat' => $karyawan->alamat,
                'domisili' => $karyawan->domisili,
                'no_telp' => $karyawan->no_telp,
                'no_telp_darurat' => $karyawan->no_telp_darurat,
                'email' => $karyawan->email,
                'npwp' => $karyawan->npwp,
                'no_bpjs_ks' => $karyawan->no_bpjs_ks,
                'no_bpjs_kt' => $karyawan->no_bpjs_kt,
                'no_rekening' => $karyawan->no_rekening,
                'nama_rekening' => $karyawan->nama_rekening,
                'nama_bank' => $karyawan->nama_bank,
                'nama_ibu_kandung' => $karyawan->nama_ibu_kandung,
                'jenjang_pendidikan' => $karyawan->jenjang_pendidikan,
                'jurusan_pendidikan' => $karyawan->jurusan_pendidikan,
                'jenis_kontrak' => $karyawan->jenis_kontrak,
                'status_karyawan' => $status_karyawan_text,
                'sisa_cuti_pribadi' => $karyawan->sisa_cuti_pribadi,
                'sisa_cuti_bersama' => $karyawan->sisa_cuti_bersama,
                'sisa_cuti_tahun_lalu' => $karyawan->sisa_cuti_tahun_lalu,
                'expired_date_cuti_tahun_lalu' => $karyawan->expired_date_cuti_tahun_lalu,
                'hutang_cuti' => $karyawan->hutang_cuti,
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
        $baseString = $initials . $timestamp . rand(100, 999);

        return $baseString;
    }

    public function upload_karyawan(Request $request)
    {
        $file = $request->file('karyawan_file');
        $organisasi_id = auth()->user()->organisasi_id;
        
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
                $spreadsheet = IOFactory::load(storage_path("app/public/".$karyawan_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                $chunkSize = 25;

                //Chunck data agar tidak terlalu banyak
                for ($i = 1; $i <= count($data); $i += $chunkSize) {
                    $chunk = array_slice($data, $i, $chunkSize);
                    foreach ($chunk as $index => $row) {
                        // Log::info('Memproses data ke-' . $index+1);
                        // if ($index < 1) { 
                        //     continue;
                        // }

                        //Convert tanggal lahir ke format Ymd jika ada
                        if($row[7] !== null){
                            try {
                                $tanggal_lahir = Carbon::createFromFormat('d/m/Y', $row[7])->format('Y-m-d');
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Format tanggal lahir salah!'], 402);
                            }
                        } 

                        //Convert tanggal bergabung/mulai ke format Ymd jika ada
                        if($row[17] !== null){
                            try {
                                $tanggal_mulai = Carbon::createFromFormat('d/m/Y', $row[17])->format('Y-m-d');
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Format tanggal bergabung salah!'], 402);
                            }

                        } 

                        if($row[33] !== null){
                            try {
                                $expired_date_cuti_tahun_lalu = Carbon::createFromFormat('d/m/Y', $row[33])->format('Y-m-d');
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Format tanggal expired cuti tahun lalu salah!'], 402);
                            }

                        } else {
                            $expired_date_cuti_tahun_lalu = null;
                        }

                        //Validasi Kolom Numeric
                        if (!is_numeric($row[11]) || $row[11] < 0 || 
                            !is_numeric($row[12]) || $row[12] < 0 || 
                            !is_numeric($row[14]) || $row[14] < 0 || 
                            !is_numeric($row[15]) || $row[15] < 0 || 
                            !is_numeric($row[16]) || $row[16] < 0 || 
                            !is_numeric($row[23]) || $row[23] < 0 || 
                            !is_numeric($row[29]) || $row[29] < 0 || 
                            !is_numeric($row[30]) || $row[30] < 0 || 
                            !is_numeric($row[31]) || $row[31] < 0 || 
                            !is_numeric($row[32]) || $row[32] < 0 || 
                            !is_numeric($row[34]) || $row[34] < 0) {
                            return response()->json(['message' => 'Kolom No KK, NIK KTP, No BPJS, No Hp, No Darurat, No. Rekening dan Seluruh Jatah serta Hutang Cuti harus berupa angka positif!'], 402);
                        }

                        //Cek apakah karyawan sudah ada atau belum
                        $existingKaryawan = Karyawan::where('ni_karyawan', $row[0])->first();
                        if (isset($row[1])) {
                            try {
                                if (strpos($row[1], ',') !== false) {
                                    return response()->json(['message' => 'Hanya boleh mengisi 1 posisi utama!'], 402);
                                    // $posisis = Posisi::whereIn('nama',explode(',', $row[1]))->pluck('id_posisi')->toArray();
                                } else {
                                    $posisis = Posisi::where('nama', $row[1])->pluck('id_posisi')->toArray();
                                }
                            } catch (Exception $e) {
                                return response()->json(['message' => 'Format Posisi tidak sesuai template atau Posisi tidak tersedia!'], 402);
                            }
                        } else {
                            $posisis = [];
                        }


                        //VALIDASI EMAIL
                        if (!filter_var($row[24], FILTER_VALIDATE_EMAIL)) {
                            return response()->json(['message' => 'Email pribadi harus berformat sebuah Email!'], 402);
                        }

                        if (!filter_var($row[26], FILTER_VALIDATE_EMAIL)) {
                            return response()->json(['message' => 'Email perusahaan harus berformat sebuah Email!'], 402);
                        }

                        if ($existingKaryawan) {
                            $existingKaryawan->update([
                                'ni_karyawan' => $row[0],
                                'organisasi_id' => $organisasi_id,
                                'nama' => $row[2],
                                'jenis_kelamin' => in_array(strtoupper($row[3]), ['L', 'P']) ? strtoupper($row[3]) : null,
                                'alamat' => $row[4],
                                'domisili' => $row[5],
                                'tempat_lahir' => $row[6],
                                'tanggal_lahir' => $tanggal_lahir,
                                'status_keluarga' => in_array(strtoupper($row[8]), ['MENIKAH', 'BELUM MENIKAH', 'CERAI']) ? strtoupper($row[8]) : null,
                                'kategori_keluarga' => in_array(strtoupper($row[9]), ['TK0', 'TK1', 'TK2', 'TK3', 'K0', 'K1', 'K2', 'K3']) ? strtoupper($row[9]) : null,
                                'agama' => in_array(strtoupper($row[10]), ['ISLAM', 'KATOLIK', 'KRISTEN', 'KONGHUCU', 'HINDU', 'BUDHA', 'PROTESTAN', 'LAINNYA']) ? strtoupper($row[10]) : null,
                                'no_kk' => $row[11],
                                'nik' => $row[12],
                                'npwp' => $row[13],
                                'no_bpjs_ks' => $row[14],
                                'no_bpjs_kt' => $row[15],
                                'no_telp' => $row[16],
                                'no_telp_darurat' => $row[29],
                                'jenjang_pendidikan' => in_array(strtoupper($row[18]), ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3']) ? strtoupper($row[18]) : null,
                                'jurusan_pendidikan' => $row[19],
                                'nama_ibu_kandung' => $row[20],
                                'nama_bank' => $row[21],
                                'nama_rekening' => $row[22],
                                'no_rekening' => $row[23],
                                'email' => $row[24],
                                'gol_darah' => in_array(strtoupper($row[25]), ['O', 'A', 'B', 'AB']) ? strtoupper($row[25]) : null,
                                'sisa_cuti_pribadi' => $row[30],
                                'sisa_cuti_bersama' => $row[31],
                                'sisa_cuti_tahun_lalu' => $row[32] ? $row[32] : 0,
                                'expired_date_cuti_tahun_lalu' => $expired_date_cuti_tahun_lalu ? $expired_date_cuti_tahun_lalu : null,
                                'hutang_cuti' => $row[34],
                            ]);

                            if(isset($row[26]) && isset($row[27]) && isset($row[28])){
                                $user = User::find($existingKaryawan->user_id);
                                $user->update([
                                    'email' => $row[26],
                                    'username' => $row[27],
                                    'password' => Hash::make($row[28]),
                                    'organisasi_id' => $organisasi_id,
                                ]); 
                            }

                            if(!empty($posisis)){
                                $existingKaryawan->posisi()->sync($posisis);
                                if($existingKaryawan->posisi[0]->jabatan_id !== 6){
                                    if ($user->roles()->count() > 0) {
                                        $user->roles()->detach();
                                    }
                                    $user->assignRole('atasan');
                                } else {
                                    if ($user->roles()->count() > 0) {
                                        $user->roles()->detach();
                                    }
                                    $user->assignRole('member');
                                }
                            }

                            continue;
                        }

                        $user = User::create([
                            'email' => $row[26],
                            'username' => $row[27],
                            'password' => Hash::make($row[28]),
                            'organisasi_id' => $organisasi_id,
                        ]); 

                        $id_karyawan = $this->generateIdKaryawan(strtoupper($row[2]));
        
                        $karyawan = Karyawan::create([
                            'user_id' => $user->id,
                            'organisasi_id' => $organisasi_id,
                            'id_karyawan' => $id_karyawan,
                            'ni_karyawan' => $row[0],
                            'nama' => $row[2],
                            'jenis_kelamin' => in_array(strtoupper($row[3]), ['L', 'P']) ? strtoupper($row[3]) : null,
                            'alamat' => $row[4],
                            'domisili' => $row[5],
                            'tempat_lahir' => $row[6],
                            'tanggal_lahir' => $tanggal_lahir,
                            'status_keluarga' => in_array(strtoupper($row[8]), ['MENIKAH', 'BELUM MENIKAH', 'CERAI']) ? strtoupper($row[8]) : null,
                            'kategori_keluarga' => in_array(strtoupper($row[9]), ['TK0', 'TK1', 'TK2', 'TK3', 'K0', 'K1', 'K2', 'K3']) ? strtoupper($row[9]) : null,
                            'agama' => in_array(strtoupper($row[10]), ['ISLAM', 'KATOLIK', 'KRISTEN', 'KONGHUCU', 'HINDU', 'BUDHA', 'PROTESTAN', 'LAINNYA']) ? strtoupper($row[10]) : null,
                            'no_kk' => $row[11],
                            'nik' => $row[12],
                            'npwp' => $row[13],
                            'no_bpjs_ks' => $row[14],
                            'no_bpjs_kt' => $row[15],
                            'no_telp' => $row[16],
                            'no_telp_darurat' => $row[29],
                            'jenjang_pendidikan' => in_array(strtoupper($row[18]), ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3']) ? strtoupper($row[18]) : null,
                            'jurusan_pendidikan' => $row[19],
                            'nama_ibu_kandung' => $row[20],
                            'nama_bank' => $row[21],
                            'nama_rekening' => $row[22],
                            'no_rekening' => $row[23],
                            'email' => $row[24],
                            'tanggal_mulai' => $tanggal_mulai,
                            'gol_darah' => in_array(strtoupper($row[25]), ['O', 'A', 'B', 'AB']) ? strtoupper($row[25]) : null,
                            'sisa_cuti_pribadi' => $row[30],
                            'sisa_cuti_bersama' => $row[31],
                            'sisa_cuti_tahun_lalu' => $row[32] ? $row[32] : 0,
                            'expired_date_cuti_tahun_lalu' => $expired_date_cuti_tahun_lalu ? $expired_date_cuti_tahun_lalu : null,
                            'hutang_cuti' => $row[34],
                        ]);

                        if(!empty($posisis)){
                            $karyawan->posisi()->sync($posisis);
                            if($karyawan->posisi[0]->jabatan_id !== 6){
                                if ($user->roles()->count() > 0) {
                                    $user->roles()->detach();
                                }
                                $user->assignRole('atasan');
                            } else {
                                if ($user->roles()->count() > 0) {
                                    $user->roles()->detach();
                                }
                                $user->assignRole('member');
                            }
                        }
                    }
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
