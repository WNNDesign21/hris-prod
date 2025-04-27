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
use App\Models\Organisasi;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\UploadKaryawanJob;
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
        $dataPage = [
            'pageTitle' => "Master Data - Karyawan",
            'page' => 'masterdata-karyawan',
            'departemen' => $departemen,
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
            'ni_karyawan' => ['required', 'unique:karyawans,ni_karyawan'],
            'no_kk' => ['required','numeric'],
            'nik' => ['required','numeric','unique:karyawans,nik'],
            'tempat_lahir' => ['nullable', 'string'],
            'tanggal_lahir' => ['nullable', 'date_format:Y-m-d'],
            'jenis_kelamin' => ['required','in:L,P'],
            'agama' => ['nullable', 'string','in:ISLAM,KATOLIK,KRISTEN,HINDU,BUDHA,KONGHUCU,LAINNYA,PROTESTAN'],
            'gol_darah' => ['string', 'in:A,B,AB,O'],
            'status_keluarga' => ['string', 'in:MENIKAH,BELUM MENIKAH,CERAI'],
            'kategori_keluarga' => ['string','in:TK0,TK1,TK2,TK3,K0,K1,K2,K3'],
            'alamat' => ['nullable', 'string'],
            'domisili' => ['nullable', 'string'],
            'no_telp' => ['required','numeric', 'unique:karyawans,no_telp'],
            'no_telp_darurat' => ['nullable', 'numeric'],
            'email' => ['required','email', 'unique:karyawans,email'],
            'npwp' => ['nullable', 'string', 'unique:karyawans,npwp'],
            'no_bpjs_kt' => ['nullable', 'numeric', 'unique:karyawans,no_bpjs_kt'],
            'no_bpjs_ks' => ['nullable', 'numeric', 'unique:karyawans,no_bpjs_ks'],
            'no_rekening' => ['required','numeric'],
            'nama_rekening' => ['nullable', 'string'],
            'nama_bank' => ['nullable', 'string', 'in:BNI,BRI,BCA,MANDIRI,BSI'],
            'nama_ibu_kandung' => ['nullable', 'string'],
            'jenjang_pendidikan' => ['nullable', 'string', 'in:SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3'],
            'jurusan_pendidikan' => ['nullable', 'string'],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'posisi.*' => ['required'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'isAdmin' => ['in:Y'],
            'pin' => ['nullable', 'string'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
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
        $no_bpjs_kt = $request->no_bpjs_kt;
        $no_bpjs_ks = $request->no_bpjs_ks;
        $no_rekening = $request->no_rekening;
        $nama_rekening = $request->nama_rekening;
        $nama_bank = $request->nama_bank;
        $nama_ibu_kandung = $request->nama_ibu_kandung;
        $jenjang_pendidikan = $request->jenjang_pendidikan;
        $jurusan_pendidikan = $request->jurusan_pendidikan;
        $posisi = $request->posisi;
        $tanggal_mulai = $request->tanggal_mulai;
        $user_id = $request->user_id;
        $email_akun = $request->email_akun;
        $username = $request->username;
        $password = $request->password;
        $foto = $request->file('foto');
        $organisasi_id = auth()->user()->organisasi_id;
        $pin = $request->pin;

        DB::beginTransaction();
        try{
            $id_karyawan = $this->generateIdKaryawan($nama);

            if($user_id == null){
                if($username !== null && $email_akun !== null && $password !== null){
                    $user = User::create([
                        'username' => $username,
                        'email' => $email_akun,
                        'password' => bcrypt($password),
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
                'no_bpjs_kt' => $no_bpjs_kt,
                'no_bpjs_ks' => $no_bpjs_ks,
                'no_rekening' => $no_rekening,
                'nama_rekening' => $nama_rekening,
                'nama_bank' => $nama_bank,
                'nama_ibu_kandung' => $nama_ibu_kandung,
                'jenjang_pendidikan' => $jenjang_pendidikan,
                'jurusan_pendidikan' => $jurusan_pendidikan,
                'tanggal_mulai' => $tanggal_mulai,
                'pin' => $pin
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

            if(isset($request->isAdmin)){
                $user = User::find($user_id);
                $user->assignRole('admin-dept');
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
            'ni_karyawanEdit' => ['required', 'unique:karyawans,ni_karyawan,'.$request->ni_karyawanEdit.',ni_karyawan'],
            'no_kkEdit' => ['required','numeric'],
            'nikEdit' => ['required','numeric', 'unique:karyawans,nik,'.$request->nikEdit.',nik'],
            'tempat_lahirEdit' => ['nullable', 'string'],
            'tanggal_lahirEdit' => ['nullable', 'date_format:Y-m-d'],
            'jenis_kelaminEdit' => ['required', 'in:L,P'],
            'agamaEdit' => ['nullable', 'string', 'in:ISLAM,KRISTEN,KATOLIK,HINDU,BUDHA,KONGHUCU,LAINNYA,PROTESTAN'],
            'gol_darahEdit' => ['required','string', 'in:A,B,AB,O'],
            'status_keluargaEdit' => ['required','string', 'in:MENIKAH,BELUM MENIKAH,CERAI'],
            'kategori_keluargaEdit' => ['required','string', 'in:TK0,TK1,TK2,TK3,K0,K1,K2,K3'],
            'alamatEdit' => ['nullable','string'],
            'domisiliEdit' => ['nullable','string'],
            'no_telpEdit' => ['required','numeric', 'unique:karyawans,no_telp,'.$request->no_telpEdit.',no_telp'],
            'no_telp_daruratEdit' => ['nullable','numeric'],
            'emailEdit' => ['email', 'unique:karyawans,email,'.$request->emailEdit.',email'],
            'npwpEdit' => ['nullable', 'string', 'unique:karyawans,npwp,'.$request->npwpEdit.',npwp'],
            'no_bpjs_ksEdit' => ['nullable', 'numeric', 'unique:karyawans,no_bpjs_ks,'.$request->no_bpjs_ksEdit.',no_bpjs_ks'],
            'no_bpjs_ktEdit' => ['nullable', 'numeric', 'unique:karyawans,no_bpjs_kt,'.$request->no_bpjs_ktEdit.',no_bpjs_kt'],
            'no_rekeningEdit' => ['required','numeric'],
            'nama_rekeningEdit' => ['nullable', 'string'],
            'nama_bankEdit' => ['nullable', 'string','in:MANDIRI,BCA,BRI,BSI,BNI'],
            'nama_ibu_kandungEdit' => ['nullable', 'string'],
            'jenjang_pendidikanEdit' => ['nullable', 'string', 'in:SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3'],
            'jurusan_pendidikanEdit' => ['nullable', 'string'],
            'posisiEdit.*' => ['required'],
            'fotoEdit' => ['nullable','image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'hutang_cutiEdit' => ['required','numeric'],
            'sisa_cuti_pribadiEdit' => ['required','numeric'],
            'sisa_cuti_bersamaEdit' => ['required','numeric'],
            'sisa_cuti_tahun_laluEdit' => ['required','numeric'],
            'isAdminEdit' => ['in:Y'],
            'pinEdit' => ['nullable', 'string']
        ];


        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
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
        $foto = $request->file('fotoEdit');
        $sisa_cuti_pribadi = $request->sisa_cuti_pribadiEdit;
        $sisa_cuti_bersama = $request->sisa_cuti_bersamaEdit;
        $sisa_cuti_tahun_lalu = $request->sisa_cuti_tahun_laluEdit;
        $hutang_cuti = $request->hutang_cutiEdit;
        $expired_date_cuti_tahun_lalu = $request->expired_date_cuti_tahun_laluEdit;
        $pin = $request->pinEdit;

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
            $karyawan->no_bpjs_kt = $no_bpjs_kt;
            $karyawan->no_bpjs_ks = $no_bpjs_ks;
            $karyawan->no_rekening = $no_rekening;
            $karyawan->nama_rekening = $nama_rekening;
            $karyawan->nama_bank = $nama_bank;
            $karyawan->nama_ibu_kandung = $nama_ibu_kandung;
            $karyawan->jenjang_pendidikan = $jenjang_pendidikan;
            $karyawan->jurusan_pendidikan = $jurusan_pendidikan;
            $karyawan->sisa_cuti_pribadi = $sisa_cuti_pribadi;
            $karyawan->sisa_cuti_bersama = $sisa_cuti_bersama;
            $karyawan->sisa_cuti_tahun_lalu = $sisa_cuti_tahun_lalu;
            $karyawan->hutang_cuti = $hutang_cuti;
            $karyawan->expired_date_cuti_tahun_lalu = $expired_date_cuti_tahun_lalu;
            $karyawan->pin = $pin;
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

            if(isset($request->isAdminEdit)){
                if (!$user->hasRole('admin-dept')) {
                    $user->assignRole('admin-dept');
                }
            } else {
                if ($user->hasRole('admin-dept')) {
                    $user->removeRole('admin-dept');
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
                'no_bpjs_kt' => $karyawan->no_bpjs_kt,
                'no_bpjs_ks' => $karyawan->no_bpjs_ks,
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
                'is_admin' => $karyawan->user->hasRole('admin-dept'),
                'pin' => $karyawan->pin,
            ];
            return response()->json(['data' => $detail], 200);
        } else {
            return response()->json(['message' => 'Data Karyawan tidak ditemukan!'], 404);
        }

    }

    function generateIdKaryawan($name, $organisasi_id)
    {
        $organisasi = Organisasi::find($organisasi_id)->nama;

        if ($organisasi) {
            $organisasi = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($organisasi));
        } else {
            $organisasi = 'KRY';
        }

        $words = explode(' ', $name);

        if (count($words) === 1) {
            $initials = substr($name, 0, 2);
        } else {
            $initials = substr($words[0], 0, 1) . substr($words[1], 0, 1);
        }

        $timestamp = now()->timestamp;
        $baseString = $organisasi.'-'.$initials . $timestamp . rand(100, 999);

        return $baseString;
    }

    public function upload_karyawan(Request $request)
    {
        $dataValidate = [
            'method' => ['required','in:I,U'],
            'karyawan_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        try {
            $organisasi_id = auth()->user()->organisasi_id;
            $method = $request->method;
            $user = auth()->user();

            if($request->hasFile('karyawan_file')){
                $file = $request->file('karyawan_file');
                $karyawan_records = 'KR_' . time() . '.' . $file->getClientOriginalExtension();
                $karyawan_file = $file->storeAs("attachment/upload-karyawan", $karyawan_records);
            }

           if (file_exists(storage_path("app/public/".$karyawan_file))) {
                $spreadsheet = IOFactory::load(storage_path("app/public/".$karyawan_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                $dataWithoutHeader = array_slice($data, 1);

                if (count($dataWithoutHeader) < 1) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan!'
                    ], 404);
                }

                UploadKaryawanJob::dispatch($dataWithoutHeader, $organisasi_id, $method, $user);
                return response()->json([
                    'status' => 'success',
                    'message' => 'File uploaded successfully, please wait for the process to finish (job)',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please upload a file',
                ], 500);
            }

        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function upload_datatable(Request $request)
    {

        $columns = array(
            0 => 'activity_log.description',
            1 => 'users.username',
            2 => 'activity_log.created_at',
        );

        $totalData = ActivityLog::count();
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

        $dataFilter['log_name'] = 'error_job_upload_karyawan';

        $uploadLog = ActivityLog::getData($dataFilter, $settings);
        $totalFiltered = ActivityLog::countData($dataFilter);

        $dataTable = [];

        if (!empty($uploadLog)) {
            foreach ($uploadLog as $data) {
                $nestedData['description'] = $data->description;
                $nestedData['causer'] = $data->username;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->translatedFormat('d F Y H:i:s');
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
}
