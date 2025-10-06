<?php

namespace App\Http\Controllers\MasterData;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Grup;
use App\Models\User;
use App\Models\Cutie;
use App\Models\Event;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Lembure;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\DetailLembur;
use Illuminate\Http\Request;
use App\Jobs\UploadKaryawanJob;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\SettingLemburKaryawan;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance\AttendanceSummary;
use App\Models\Divisi;
use App\Models\KeluargaKaryawan;
use App\Models\RekapManpowerHistory;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::select(['id_departemen', 'nama'])->get();
        // $posisi = Posisi::select(['id_posisi', 'nama'])->get();
        $organisasi = Organisasi::select(['id_organisasi', 'nama'])->get();
        $dataPage = [
            'pageTitle' => "Master Data - Karyawan",
            'page' => 'masterdata-karyawan',
            'departemen' => $departemen,
            'organisasi' => $organisasi,
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
                $kontrak = Kontrak::where('karyawan_id', $data->id_karyawan)->orderBy('tanggal_mulai', 'DESC')->pluck('jenis_kontrak_id')->first();
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
            'status_kawin' => ['required', 'string'],
            'agama' => ['nullable', 'string','in:ISLAM,KATOLIK,KRISTEN,HINDU,BUDHA,KONGHUCU,LAINNYA,PROTESTAN'],
            'gol_darah' => ['string', 'in:A,B,AB,O'],
            'status_keluarga' => ['string', 'in:MENIKAH,BELUM MENIKAH,CERAI'],
            'kategori_keluarga' => ['string','in:TK/0,TK/1,TK/2,TK/3,K/0,K/1,K/2,K/3'],
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
            'status_kawin' => ['required', 'string'],
            'tipe_karyawan' => ['required', 'string', 'in:D,I'],
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
        $status_kawin = $request->status_kawin;
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
        $tipe_karyawan = $request->tipe_karyawan;

        $direct = ($tipe_karyawan == 'D') ? 1 : null;
        $indirect = ($tipe_karyawan == 'I') ? 1 : null;

        DB::beginTransaction();
        try{
            $id_karyawan = $this->generateIdKaryawan($nama, $organisasi_id);

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
                'status_kawin' => $status_kawin,
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
                'pin' => $pin,
                'direct' => $direct,
                'indirect' => $indirect
            ]);

            // Simpan data keluarga dari request
            if ($request->has('keluarga') && is_array($request->keluarga)) {
                foreach ($request->keluarga as $anggota) {
                    if (!empty($anggota['nama'])) {
                        KeluargaKaryawan::create([
                            'karyawan_id' => $id_karyawan,
                            'hubungan' => $anggota['hubungan'],
                            'nama' => $anggota['nama'],
                            'tempat_lahir' => $anggota['tempat_lahir'],
                            'tanggal_lahir' => $anggota['tanggal_lahir'],
                        ]);
                    }
                }
            }

            // Otomatis tambahkan Istri/Suami jika belum ada di request
            $isPasanganAda = false;
            if ($request->has('keluarga') && is_array($request->keluarga)) {
                foreach ($request->keluarga as $anggota) {
                    if (
                        ($request->jenis_kelamin == 'L' && strtolower($anggota['hubungan']) == 'istri') ||
                        ($request->jenis_kelamin == 'P' && strtolower($anggota['hubungan']) == 'suami')
                    ) {
                        $isPasanganAda = true;
                        break;
                    }
                }
            }
            if (!$isPasanganAda) {
                if ($request->jenis_kelamin == 'L' && !empty($request->nama_istri)) {
                    KeluargaKaryawan::create([
                        'karyawan_id' => $id_karyawan,
                        'hubungan' => 'Istri',
                        'nama' => $request->nama_istri,
                        'tempat_lahir' => $request->tempat_lahir_istri ?? null,
                        'tanggal_lahir' => $request->tanggal_lahir_istri ?? null,
                    ]);
                } elseif ($request->jenis_kelamin == 'P' && !empty($request->nama_suami)) {
                    KeluargaKaryawan::create([
                        'karyawan_id' => $id_karyawan,
                        'hubungan' => 'Suami',
                        'nama' => $request->nama_suami,
                        'tempat_lahir' => $request->tempat_lahir_suami ?? null,
                        'tanggal_lahir' => $request->tanggal_lahir_suami ?? null,
                    ]);
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

            if(isset($request->isAdmin)){
                $user = User::find($user_id);
                if (!$user->hasRole('admin-dept')) {
                    $user->assignRole('admin-dept');
                }
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
            'organisasiEdit' => ['required', 'exists:organisasis,id_organisasi'],
            'namaEdit' => ['required'],
            'ni_karyawanEdit' => ['required', 'unique:karyawans,ni_karyawan,'.$id_karyawan.',id_karyawan'],
            'no_kkEdit' => ['required','string'],
            'nikEdit' => ['required','string', 'unique:karyawans,nik,'.$id_karyawan.',id_karyawan'],
            'tempat_lahirEdit' => ['nullable', 'string'],
            'tanggal_lahirEdit' => ['nullable', 'date_format:Y-m-d'],
            'jenis_kelaminEdit' => ['required', 'in:L,P'],
            'status_kawinEdit' => ['required', 'string'],
            'agamaEdit' => ['nullable', 'string', 'in:ISLAM,KRISTEN,KATOLIK,HINDU,BUDHA,KONGHUCU,LAINNYA,PROTESTAN'],
            'gol_darahEdit' => ['required','string', 'in:A,B,AB,O'],
            'status_keluargaEdit' => ['required','string', 'in:MENIKAH,BELUM MENIKAH,CERAI'],
            'kategori_keluargaEdit' => ['required','string', 'in:TK/0,TK/1,TK/2,TK/3,K/0,K/1,K/2,K/3'],
            'alamatEdit' => ['nullable','string'],
            'domisiliEdit' => ['nullable','string'],
            'no_telpEdit' => ['required','numeric', 'unique:karyawans,no_telp,'.$id_karyawan.',id_karyawan'],
            'no_telp_daruratEdit' => ['nullable','numeric'],
            'emailEdit' => ['email', 'unique:karyawans,email,'.$id_karyawan.',id_karyawan'],
            'npwpEdit' => ['nullable', 'string', 'unique:karyawans,npwp,'.$id_karyawan.',id_karyawan'],
            'no_bpjs_ksEdit' => ['nullable', 'string', 'unique:karyawans,no_bpjs_ks,'.$id_karyawan.',id_karyawan'],
            'no_bpjs_ktEdit' => ['nullable', 'string', 'unique:karyawans,no_bpjs_kt,'.$id_karyawan.',id_karyawan'],
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
            'pinEdit' => ['nullable', 'string'],
            'tipe_karyawanEdit' => ['required', 'string', 'in:D,I'],
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
        $status_kawin = $request->status_kawinEdit;
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
        $organisasi_id = $request->organisasiEdit;
        $tipe_karyawan = $request->tipe_karyawanEdit;

        $direct = ($tipe_karyawan == 'D') ? 1 : null;
        $indirect = ($tipe_karyawan == 'I') ? 1 : null;

        DB::beginTransaction();
        try{
            Log::info('Keluarga Edit Request:', $request->input('keluargaEdit', []));
            $karyawan = Karyawan::find($id_karyawan);

            if ($karyawan->organisasi_id != $organisasi_id) {
                $existingLemburDone = Lembure::where('issued_by', $id_karyawan)
                ->where('organisasi_id', $karyawan->organisasi_id)
                ->where('status', 'PLANNED')
                ->exists();

                if ($existingLemburDone) {
                    DB::rollBack();
                    return response()->json(['message' => 'Karyawan tidak bisa dipindah, karena terdapat dokumen lembur yang menunggu pengisian aktual'], 500);
                }

                $existingLembur = DetailLembur::where('karyawan_id', $id_karyawan)
                ->where('organisasi_id', $karyawan->organisasi_id)
                ->where('is_aktual_approved', 'Y')
                ->whereHas('lembur', function($query) use ($karyawan){
                    $query->whereNull('actual_legalized_by')
                    ->whereNull('rejected_by');
                })->exists();

                if ($existingLembur) {
                    DB::rollBack();
                    return response()->json(['message' => 'Karyawan tidak bisa dipindah, karena terdapat dokumen lembur yang belum terlegalized'], 500);
                }

                $existingCuti = Cutie::where('karyawan_id', $id_karyawan)
                ->where('organisasi_id', $karyawan->organisasi_id)
                ->whereNot('status_dokumen', 'REJECTED')
                ->where('rencana_selesai_cuti', '>=', now())
                ->where(function ($query) {
                    $query->whereIn('status_cuti', ['SCHEDULED', 'ON LEAVE', 'COMPLETED'])
                    ->orWhereNull('status_cuti');
                })->exists();

                if ($existingCuti) {
                    DB::rollBack();
                    return response()->json(['message' => 'Karyawan tidak bisa dipindah, silahkan cancel cuti terlebih dahulu!'], 500);
                }

                $existingAttSum = AttendanceSummary::where('karyawan_id', $id_karyawan)
                ->where('organisasi_id', $karyawan->organisasi_id)
                ->whereMonth('periode', Carbon::now()->month)
                ->whereYear('periode', Carbon::now()->year)
                ->first();

                if ($existingAttSum) {
                    $existingAttSum->organisasi_id = $organisasi_id;
                    $existingAttSum->save();
                }

                $existingSettingLembur = SettingLemburKaryawan::where('karyawan_id', $id_karyawan)
                ->where('organisasi_id', $karyawan->organisasi_id)
                ->first();

                if ($existingSettingLembur) {
                    $existingSettingLembur->organisasi_id = $organisasi_id;
                    $existingSettingLembur->save();
                }

                $karyawan->user->organisasi_id = $organisasi_id;
                $karyawan->user->save();
            }

            $karyawan->nama = $nama;
            $karyawan->ni_karyawan = $ni_karyawan;
            $karyawan->no_kk = $no_kk;
            $karyawan->nik = $nik;
            $karyawan->tempat_lahir = $tempat_lahir;
            $karyawan->tanggal_lahir = $tanggal_lahir;
            $karyawan->jenis_kelamin = $jenis_kelamin;
            $karyawan->status_kawin = $status_kawin;
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
            $karyawan->organisasi_id = $organisasi_id;
            $karyawan->direct = $direct;
            $karyawan->indirect = $indirect;
            $karyawan->posisi()->detach();

            // Sync family members
            $keluargaIds = [];
            if ($request->has('keluargaEdit') && is_array($request->keluargaEdit)) {
                $anakCount = 1;
                foreach ($request->keluargaEdit as $anggota) {
                    if (!empty($anggota['nama']) && !empty($anggota['hubungan'])) {
                        $hubungan = $anggota['hubungan'];
                        if (strtolower($anggota['hubungan']) == 'anak') {
                            $hubungan = 'Anak ' . $anakCount++;
                        }

                        $data = [
                            'karyawan_id' => $id_karyawan,
                            'hubungan' => $hubungan,
                            'nama' => $anggota['nama'],
                            'tempat_lahir' => $anggota['tempat_lahir'],
                            'tanggal_lahir' => $anggota['tanggal_lahir'],
                        ];

                        if (!empty($anggota['id'])) {
                            // Update existing family member
                            $keluarga = KeluargaKaryawan::find($anggota['id']);
                            if ($keluarga) {
                                $keluarga->update($data);
                                $keluargaIds[] = $keluarga->id;
                            }
                        } else {
                            // Create new family member
                            $keluarga = KeluargaKaryawan::create($data);
                            $keluargaIds[] = $keluarga->id;
                        }
                    }
                }
            }

            // Remove family members that were not in the request
            $karyawan->keluarga()->whereNotIn('id', $keluargaIds)->delete();

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
        $karyawan = Karyawan::with('keluarga')->find($id_karyawan);
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

            $tipe_karyawan = null;
            if ($karyawan->direct == 1) {
                $tipe_karyawan = 'D';
            } elseif ($karyawan->indirect == 1) {
                $tipe_karyawan = 'I';
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
                'status_kawin' => $karyawan->status_kawin,
                'keluarga' => $karyawan->keluarga,
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
                'organisasi_id' => $karyawan->organisasi_id,
                'tipe_karyawan' => $tipe_karyawan,
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

           if (file_exists(storage_path("app/public/" . $karyawan_file))) {
                $spreadsheet = IOFactory::load(storage_path("app/public/" . $karyawan_file));
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

    public function downloadRekapManpower(Request $request)
    {
        $periode = $request->input('periode');
        $organisasi_filter = $request->input('organisasi');
        $selectedPeriod = null;

        if ($periode) {
            try {
                $selectedPeriod = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
            } catch (Exception $e) {
                return response()->json(['message' => 'Format periode tidak valid. Gunakan format YYYY-MM.'], 400);
            }
        }

        $now = Carbon::now()->startOfMonth();
        $processDate = $selectedPeriod ?: $now;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $spreadsheet->getDefaultStyle()->getFont()->setName('MS Gothic');
        $sheetIndex = 0;

        if ($selectedPeriod && $selectedPeriod->lt($now)) {
            $history = RekapManpowerHistory::where('period', $processDate->format('Y-m-d'))->first();

            if ($history && !empty($history->data)) {
                $allHistoryData = $history->data;

                $orgs_to_process = [];
                if ($organisasi_filter && $organisasi_filter !== 'all') {
                    $orgs_to_process = [$organisasi_filter];
                } else {
                    $orgs_to_process = array_keys($allHistoryData);
                }

                foreach ($orgs_to_process as $org_id) {
                    if (!isset($allHistoryData[$org_id])) {
                        continue;
                    }

                    $organisasi = Organisasi::find($org_id);
                    $organisasi_nama = $organisasi ? $organisasi->nama : 'ASI Plant ' . $org_id;
                    $allData = $allHistoryData[$org_id];

                    // --- Rekap Manpower Sheet from History ---
                    $rekapData = $allData['rekap_manpower'];
                    $rekapSheet = $spreadsheet->createSheet($sheetIndex++);
                    $rekapSheet->setTitle('Rekap Manpower ' . $organisasi_nama);

                    $rekapSheet->mergeCells('A1:U1')->setCellValue('A1', 'REKAP MANPOWER ' . strtoupper($organisasi_nama ?? 'PT. ADYAWINSA STAMPING INDUSTRIES'))->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                    $rekapSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $rekapSheet->mergeCells('A2:U2')->setCellValue('A2', 'Period : ' . strtoupper($processDate->format('F Y')))->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $rekapSheet->mergeCells('A3:U3')->setCellValue('A3', 'Update : ' . Carbon::parse($history->created_at)->format('d F Y'))->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                    $rekapSheet->fromArray($rekapData['headers'], null, 'A5');
                    $headerRange = 'A5:U6';
                    $headerStyle = $rekapSheet->getStyle($headerRange);
                    $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF00B050');
                    $headerStyle->getFont()->setBold(true);
                    $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $rekapSheet->fromArray($rekapData['rows'], null, 'A7');
                    $lastRow = 6 + count($rekapData['rows']);
                    if ($lastRow >= 7) {
                        $style = $rekapSheet->getStyle('A7:U' . $lastRow);
                        $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }

                    $totalRow = $lastRow + 1;
                    $rekapSheet->fromArray($rekapData['totals'], null, 'A' . $totalRow);
                    $rekapSheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                    $totalStyle = $rekapSheet->getStyle('A' . $totalRow . ':U' . $totalRow);
                    $totalStyle->getFont()->setBold(true);
                    $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $totalStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $percentageRow = $totalRow + 1;
                    $rekapSheet->fromArray($rekapData['percentages'], null, 'P' . $percentageRow);
                    $percentageStyle = $rekapSheet->getStyle('P' . $percentageRow . ':U' . $percentageRow);
                    $percentageStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    foreach (range('P', 'U') as $col) {
                        $rekapSheet->getStyle($col . $percentageRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    $domisiliData = $allData['rekap_domisili'];
                    $startRow = $percentageRow + 2;
                    $rekapSheet->setCellValue('A' . $startRow, 'REKAP BERDASARKAN DOMISILI')->mergeCells('A' . $startRow . ':D' . $startRow)->getStyle('A' . $startRow)->getFont()->setBold(true);
                    $startRow++;
                    $rekapSheet->fromArray($domisiliData, null, 'A' . $startRow);


                    foreach (range('A', 'U') as $columnID) {
                        $rekapSheet->getColumnDimension($columnID)->setAutoSize(true);
                    }

                    // --- Data Karyawan Sheet from History ---
                    $karyawanData = $allData['data_karyawan'];
                    $dataSheet = $spreadsheet->createSheet($sheetIndex++);
                    $dataSheet->setTitle('Data Karyawan ' . $organisasi_nama);

                    $dataSheet->mergeCells('A1:DR1')->setCellValue('A1', 'REKAP MANPOWER ' . strtoupper($organisasi_nama ?? 'PT. ADYAWINSA STAMPING INDUSTRIES'))->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                    $dataSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $dataSheet->setCellValue('A3', 'Period : ' . strtoupper($processDate->format('F Y')));
                    $dataSheet->setCellValue('A5', 'Update : ' . Carbon::parse($history->created_at)->format('d F Y'));

                    $headerRow = 7;
                    $subHeaderRow = 8;
                    $colIndex = 1;
                    $writeMergedHeader = function ($title, $mergeCount, $subtitles) use ($dataSheet, $headerRow, $subHeaderRow, &$colIndex) {
                        $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                        if ($mergeCount > 1) {
                            $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + $mergeCount - 1);
                            $dataSheet->mergeCells($startCol . $headerRow . ':' . $endCol . ($subtitles ? $headerRow : $subHeaderRow));
                        } else {
                            $dataSheet->mergeCells($startCol . $headerRow . ':' . $startCol . $subHeaderRow);
                        }
                        $dataSheet->setCellValue($startCol . $headerRow, $title);
                        if ($subtitles) {
                            $dataSheet->fromArray($subtitles, null, $startCol . $subHeaderRow);
                        }
                        $colIndex += $mergeCount;
                    };

                    $writeMergedHeader('', 1, null);
                    $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $dataSheet->setCellValue($colString . $headerRow, 'NO');
                    $dataSheet->setCellValue($colString . $subHeaderRow, 'ALL');
                    $colIndex++;
                    $writeMergedHeader('NO', 1, null);
                    $writeMergedHeader('NAMA LENGKAP', 1, null);
                    $writeMergedHeader('NIK', 1, null);
                    $writeMergedHeader('TANGGAL MASUK', 1, null);
                    $writeMergedHeader('DEPT', 1, null);
                    $writeMergedHeader('JABATAN', 1, null);
                    $writeMergedHeader('SECTION', 1, null);
                    $writeMergedHeader('Group', 1, null);
                    $writeMergedHeader('ATASAN LANGSUNG', 1, null);
                    $writeMergedHeader('TGL PENETAPAN', 1, null);
                    $writeMergedHeader('TGL PENGANGKATAN', 1, null);
                    $writeMergedHeader('DIRECT', 1, null);
                    $writeMergedHeader('INDIRECT', 1, null);
                    $writeMergedHeader('JABATAN', 9, ['DIR', 'ADV', 'ASS DIR', 'GNRL MANAGER', 'MANAGER', 'ASTMANAGER', 'SECT HEAD', 'LEADER', 'PELAKSANA']);
                    $writeMergedHeader('STATUS', 3, ['PKWTT', 'PKWT', 'PK']);
                    $kontrakHeaders = [];
                    for ($i = 1; $i <= 10; $i++) {
                        $kontrakHeaders[] = 'K' . $i;
                    }
                    $kontrakHeaders[] = 'PK';
                    $writeMergedHeader('JUMLAH KONTRAK', 11, $kontrakHeaders);
                    $writeMergedHeader('TANGGAL BERAKHIR', 1, null);
                    for ($i = 1; $i <= 10; $i++) {
                        $writeMergedHeader('KONTRAK ' . $i, 2, ['START', 'END']);
                    }
                    $writeMergedHeader('TEMPAT LAHIR', 1, null);
                    $writeMergedHeader('TANGGAL LAHIR', 1, null);
                    $writeMergedHeader('USIA', 3, ['TAHUN', 'BULAN', 'HARI']);
                    $writeMergedHeader('JK', 2, ['L', 'P']);
                    $writeMergedHeader('STATUS KAWINAN', 8, ['BK', 'K', 'KA 1', 'KA 2', 'KA 3', 'KA 4', 'KA 5', 'KA 6']);
                    $writeMergedHeader('AGAMA', 6, ['I', 'KRIS', 'KATH', 'H', 'B', '']);
                    $writeMergedHeader('TINGKAT PENDIDIKAN', 8, ['TS', 'SD', 'SLTP', 'SLTA/STM', 'DIP', 'SI', 'S2', '']);
                    $writeMergedHeader('NAMA ISTRI/SUAMI', 1, null);
                    $writeMergedHeader('TTL ISTRI/SUAMI', 1, null);
                    $anakHeaders = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $anakHeaders[] = 'ANAK ' . $i;
                        $anakHeaders[] = 'TTL ANAK ' . $i;
                    }
                    $writeMergedHeader('NAMA & TGL LAHIR ANAK', 10, $anakHeaders);
                    $writeMergedHeader('NAMA IBU KANDUNG', 1, null);
                    $writeMergedHeader('ALAMAT KTP', 1, null);
                    $writeMergedHeader('DOMISILI', 1, null);
                    $writeMergedHeader('NO. KTP / SIM', 1, null);
                    $writeMergedHeader('NO. TELPON', 1, null);
                    $writeMergedHeader('EMAIL', 1, null);
                    $writeMergedHeader('PERIODE', 1, null);
                    $writeMergedHeader('MASA KERJA', 3, ['TAHUN', 'BULAN', 'HARI']);
                    $writeMergedHeader('KETERANGAN', 1, null);
                    $writeMergedHeader('TANGGAL HARI INI', 1, null);
                    $writeMergedHeader('SINAS', 1, null);

                    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex - 1);
                    $headerStyle = $dataSheet->getStyle('B' . $headerRow . ':' . $lastCol . $subHeaderRow);
                    $headerStyle->getFont()->setBold(true);
                    $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF00B050');
                    $dataSheet->getColumnDimension('A')->setWidth(5);
                    for ($i = 2; $i < $colIndex; $i++) {
                        $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                        $dataSheet->getColumnDimension($colString)->setAutoSize(true);
                    }
                    $dataSheet->freezePane('I9');

                    $dataRow = $subHeaderRow + 1;
                    foreach ($karyawanData['grouped_data'] as $deptData) {
                        foreach ($deptData['contracts'] as $contractData) {
                            $dataSheet->setCellValue('B' . $dataRow, $contractData['contract_name']);
                            $dataSheet->getStyle('B' . $dataRow)->getFont()->setBold(true);
                            $dataSheet->mergeCells('B' . $dataRow . ':I' . $dataRow);
                            $dataSheet->getStyle('B' . $dataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                            $dataRow++;

                            if (!empty($contractData['rows'])) {
                                $dataSheet->fromArray($contractData['rows'], null, 'A' . $dataRow);
                                $dataRow += count($contractData['rows']);
                            }

                            $dataSheet->fromArray($contractData['subtotal'], null, 'A' . $dataRow);
                            $dataSheet->mergeCells('B' . $dataRow . ':K' . $dataRow);
                            $subtotalStyle = $dataSheet->getStyle('B' . $dataRow . ':' . $lastCol . $dataRow);
                            $subtotalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
                            $subtotalStyle->getFont()->setBold(true);
                            $subtotalStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                            $subtotalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                            $dataRow++;
                            $dataRow++;
                        }
                    }

                    $sinasData = $allData['rekap_sinas'];
                    $summaryStartRow = $dataRow;
                    $dataSheet->setCellValue('DH' . $summaryStartRow, 'REKAP SINAS');
                    $dataSheet->getStyle('DH' . $summaryStartRow)->getFont()->setBold(true);
                    $summaryStartRow++;
                    $totalSinas = 0;
                    foreach ($sinasData as $category => $count) {
                        $dataSheet->setCellValue('DH' . $summaryStartRow, $category)->setCellValue('DI' . $summaryStartRow, $count);
                        $totalSinas += $count;
                        $summaryStartRow++;
                    }
                    $dataSheet->setCellValue('DH' . $summaryStartRow, 'TOTAL');
                    $dataSheet->getStyle('DH' . $summaryStartRow)->getFont()->setBold(true);
                    $dataSheet->setCellValue('DI' . $summaryStartRow, $totalSinas);
                    $dataSheet->getStyle('DI' . $summaryStartRow)->getFont()->setBold(true);
                }

                $spreadsheet->setActiveSheetIndex(0);
                $fileName = 'Rekap Manpower (History) ' . $processDate->format('F Y') . '.xlsx';
                $writer = new Xlsx($spreadsheet);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $fileName . '"');
                header('Cache-Control: max-age=0');

                $writer->save('php://output');
                exit;
            }
        }

        // --- LIVE CALCULATION & SAVING ---
        $allRekapData = [];
        $orgs_to_process = [];
        if ($organisasi_filter && $organisasi_filter !== 'all') {
            $orgs_to_process = [$organisasi_filter];
        } else {
            $orgs_to_process = [1, 2]; // Process both organizations for 'all'
        }

        foreach ($orgs_to_process as $organisasi_id) {
            if (!$organisasi_id) {
                continue;
            }

            $organisasi = Organisasi::find($organisasi_id);
            $organisasi_nama = $organisasi ? $organisasi->nama : 'ASI Plant ' . $organisasi_id;

            // --- Rekap Manpower Sheet ---
            $rekapSheet = $spreadsheet->createSheet($sheetIndex++);
            $rekapSheet->setTitle('Rekap Manpower ' . $organisasi_nama);

            $rekapSheet->mergeCells('A1:U1')->setCellValue('A1', 'REKAP MANPOWER ' . strtoupper($organisasi_nama ?? 'PT. ADYAWINSA STAMPING INDUSTRIES'))->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $rekapSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $rekapSheet->mergeCells('A2:U2')->setCellValue('A2', 'Period : ' . strtoupper($processDate->format('F Y')))->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $rekapSheet->mergeCells('A3:U3')->setCellValue('A3', 'Update : ' . Carbon::now()->format('d F Y'))->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $rekapHeaders = [
                ['NO', 'DIVISION', 'DEPARTMENT', 'AREA', 'DIRECT', 'INDIRECT', 'POSITION', '', '', '', '', '', '', '', '', 'STATUS', '', '', 'JK', '', 'TOTAL MAN POWER'],
                ['', '', '', '', '', '', 'DIR', 'ADVISOR', 'ASS DIR', 'GNRL MANAGER', 'MANAGER', 'ASTMANAGER', 'SECT HEAD', 'LEADER', 'STAFF/OPERATOR', 'PKWTT', 'PKWT', 'PENGKARYAAN', 'L', 'P', '']
            ];
            $rekapSheet->fromArray($rekapHeaders, null, 'A5');
            $headerRange = 'A5:U6';
            $headerStyle = $rekapSheet->getStyle($headerRange);
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF00B050');
            $headerStyle->getFont()->setBold(true);
            $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $rekapSheet->mergeCells('A5:A6')->mergeCells('B5:B6')->mergeCells('C5:C6')->mergeCells('D5:D6')->mergeCells('E5:E6')->mergeCells('F5:F6');
            $rekapSheet->mergeCells('G5:O5');
            $rekapSheet->mergeCells('P5:R5');
            $rekapSheet->mergeCells('S5:T5');
            $rekapSheet->mergeCells('U5:U6');

            $baseManpowerQuery = function ($jabatan_id = null) use ($processDate, $organisasi_id) {
                $query = Karyawan::query()
                    ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->leftJoin('jabatans', 'posisis.jabatan_id', 'jabatans.id_jabatan')
                    ->where('karyawans.tanggal_mulai', '<=', $processDate->endOfMonth())
                    ->where(function ($q) use ($processDate) {
                        $q->whereNull('karyawans.tanggal_selesai')
                            ->orWhere('karyawans.tanggal_selesai', '>=', $processDate->startOfMonth());
                    });
                if ($organisasi_id) $query->where('karyawans.organisasi_id', $organisasi_id);
                if ($jabatan_id) $query->where('jabatans.id_jabatan', $jabatan_id);
                return $query;
            };

            $selectAggregates = [
                DB::raw('COUNT(karyawans.id_karyawan) as total_karyawan'),
                DB::raw('COALESCE(SUM(CASE WHEN karyawans.direct = 1 THEN 1 ELSE 0 END), 0) as total_direct'),
                DB::raw('COALESCE(SUM(CASE WHEN karyawans.indirect = 1 THEN 1 ELSE 0 END), 0) as total_indirect'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.id_jabatan = 1 THEN 1 ELSE 0 END), 0) as total_dir'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.id_jabatan = 3 THEN 1 ELSE 0 END), 0) as total_advisor'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.nama = \'ASSISTANT DIRECTOR\' THEN 1 ELSE 0 END), 0) as total_ass_dir'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.nama = \'GENERAL MANAGER\' THEN 1 ELSE 0 END), 0) as total_gnrl_manager'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.id_jabatan = 2 AND posisis.nama NOT LIKE \'%AST.%\' THEN 1 ELSE 0 END), 0) as total_manager'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.id_jabatan = 2 AND posisis.nama LIKE \'%AST.%\' THEN 1 ELSE 0 END), 0) as total_ast_manager'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.nama = \'SECTION HEAD\' THEN 1 ELSE 0 END), 0) as total_sect_head'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.nama = \'LEADER\' THEN 1 ELSE 0 END), 0) as total_leader'),
                DB::raw('COALESCE(SUM(CASE WHEN jabatans.nama = \'STAFF/OPERATOR\' THEN 1 ELSE 0 END), 0) as total_pelaksana'),
                DB::raw('COUNT(CASE WHEN karyawans.jenis_kontrak = \'PKWTT\' THEN 1 END) as total_pkwtt'),
                DB::raw('COUNT(CASE WHEN karyawans.jenis_kontrak = \'PKWT\' THEN 1 END) as total_pkwt'),
                DB::raw('COUNT(CASE WHEN karyawans.jenis_kontrak = \'PENGKARYAAN\' THEN 1 END) as total_pk'),
                DB::raw('COUNT(CASE WHEN karyawans.jenis_kelamin = \'L\' THEN 1 END) as total_l'),
                DB::raw('COUNT(CASE WHEN karyawans.jenis_kelamin = \'P\' THEN 1 END) as total_p'),
            ];

            $deptHeadData = $baseManpowerQuery()->addSelect('divisis.nama as nama_divisi', 'departemens.nama as nama_departemen', ...$selectAggregates)->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')->leftJoin('divisis', 'departemens.divisi_id', 'divisis.id_divisi')->where('jabatans.id_jabatan', 2)->whereNull('posisis.seksi_id')->groupBy('divisis.nama', 'departemens.nama')->get()->keyBy(fn($item) => $item->nama_divisi . '|' . $item->nama_departemen);
            $seksiData = $baseManpowerQuery()->addSelect('divisis.nama as nama_divisi', 'departemens.nama as nama_departemen', 'seksis.nama as nama_seksi', ...$selectAggregates)->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')->leftJoin('divisis', 'departemens.divisi_id', 'divisis.id_divisi')->leftJoin('seksis', 'posisis.seksi_id', 'seksis.id_seksi')->where(fn($q) => $q->where('jabatans.id_jabatan', '!=', 2)->orWhereNotNull('posisis.seksi_id'))->whereNotIn('jabatans.id_jabatan', [1, 3])->groupBy('divisis.nama', 'departemens.nama', 'seksis.nama')->get()->keyBy(fn($item) => $item->nama_divisi . '|' . $item->nama_departemen . '|' . $item->nama_seksi);
            $advisorData = $baseManpowerQuery(3)->select($selectAggregates)->first();
            $bodData = $baseManpowerQuery(1)->select($selectAggregates)->first();
            $divisis = Divisi::with('departemen.seksis')->orderBy('nama')->get();

            $historyData = ['headers' => $rekapHeaders, 'rows' => [], 'totals' => [], 'percentages' => []];
            $row = 7;
            $no = 1;
            foreach ($divisis as $divisi) {
                $divisiStartRow = $row;
                $divisiNameRendered = false;
                foreach ($divisi->departemen as $department) {
                    $deptStartRow = $row;
                    $deptHeadKey = $divisi->nama . '|' . $department->nama;
                    $deptHeadInfo = $deptHeadData->get($deptHeadKey);
                    $isFirstRow = true;
                    $seksisToRender = $department->seksis->isEmpty() ? [null] : $department->seksis;

                    foreach ($seksisToRender as $seksi) {
                        $seksiKey = $divisi->nama . '|' . $department->nama . '|' . ($seksi->nama ?? '');
                        $seksiInfo = $seksiData->get($seksiKey);
                        $rowData = [
                            $no++,
                            !$divisiNameRendered ? $divisi->nama : '',
                            $isFirstRow ? $department->nama : '',
                            $seksi->nama ?? '',
                            ($seksiInfo->total_direct ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_direct ?? 0) : 0),
                            ($seksiInfo->total_indirect ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_indirect ?? 0) : 0),
                            ($seksiInfo->total_dir ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_dir ?? 0) : 0),
                            ($seksiInfo->total_advisor ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_advisor ?? 0) : 0),
                            ($seksiInfo->total_ass_dir ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_ass_dir ?? 0) : 0),
                            ($seksiInfo->total_gnrl_manager ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_gnrl_manager ?? 0) : 0),
                            ($seksiInfo->total_manager ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_manager ?? 0) : 0),
                            ($seksiInfo->total_ast_manager ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_ast_manager ?? 0) : 0),
                            ($seksiInfo->total_sect_head ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_sect_head ?? 0) : 0),
                            ($seksiInfo->total_leader ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_leader ?? 0) : 0),
                            ($seksiInfo->total_pelaksana ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_pelaksana ?? 0) : 0),
                            ($seksiInfo->total_pkwtt ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_pkwtt ?? 0) : 0),
                            ($seksiInfo->total_pkwt ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_pkwt ?? 0) : 0),
                            ($seksiInfo->total_pk ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_pk ?? 0) : 0),
                            ($seksiInfo->total_l ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_l ?? 0) : 0),
                            ($seksiInfo->total_p ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_p ?? 0) : 0),
                            ($seksiInfo->total_karyawan ?? 0) + ($isFirstRow ? ($deptHeadInfo->total_karyawan ?? 0) : 0),
                        ];
                        $rekapSheet->fromArray($rowData, null, 'A' . $row++);
                        $historyData['rows'][] = $rowData;
                        $isFirstRow = false;
                        if (!$divisiNameRendered) {
                            $divisiNameRendered = true;
                        }
                    }
                    if (count($seksisToRender) > 1) {
                        $rekapSheet->mergeCells('C' . $deptStartRow . ':C' . ($row - 1))->getStyle('C' . $deptStartRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        $rekapSheet->mergeCells('K' . $deptStartRow . ':K' . ($row - 1))->getStyle('K' . $deptStartRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        $rekapSheet->mergeCells('L' . $deptStartRow . ':L' . ($row - 1))->getStyle('L' . $deptStartRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                }
                if (($row - $divisiStartRow) > 1) {
                    $rekapSheet->mergeCells('B' . $divisiStartRow . ':B' . ($row - 1))->getStyle('B' . $divisiStartRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }

            if ($advisorData && $advisorData->total_karyawan > 0) {
                $advisorRowData = [$no++, 'ADVISOR', '', '', $advisorData->total_direct ?? 0, $advisorData->total_indirect ?? 0, $advisorData->total_dir ?? 0, $advisorData->total_advisor ?? 0, $advisorData->total_ass_dir ?? 0, $advisorData->total_gnrl_manager ?? 0, $advisorData->total_manager ?? 0, $advisorData->total_ast_manager ?? 0, $advisorData->total_sect_head ?? 0, $advisorData->total_leader ?? 0, $advisorData->total_pelaksana ?? 0, $advisorData->total_pkwtt ?? 0, $advisorData->total_pkwt ?? 0, $advisorData->total_pk ?? 0, $advisorData->total_l ?? 0, $advisorData->total_p ?? 0, $advisorData->total_karyawan ?? 0];
                $rekapSheet->fromArray($advisorRowData, null, 'A' . $row);
                $historyData['rows'][] = $advisorRowData;
                $rekapSheet->mergeCells('B' . $row . ':D' . $row++);
            }

            if ($bodData && $bodData->total_karyawan > 0) {
                $bodRowData = [$no++, 'DIRECTOR', '', '', $bodData->total_direct ?? 0, $bodData->total_indirect ?? 0, $bodData->total_dir ?? 0, $bodData->total_advisor ?? 0, $bodData->total_ass_dir ?? 0, $bodData->total_gnrl_manager ?? 0, $bodData->total_manager ?? 0, $bodData->total_ast_manager ?? 0, $bodData->total_sect_head ?? 0, $bodData->total_leader ?? 0, $bodData->total_pelaksana ?? 0, $bodData->total_pkwtt ?? 0, $bodData->total_pkwt ?? 0, $bodData->total_pk ?? 0, $bodData->total_l ?? 0, $bodData->total_p ?? 0, $bodData->total_karyawan ?? 0];
                $rekapSheet->fromArray($bodRowData, null, 'A' . $row);
                $historyData['rows'][] = $bodRowData;
                $rekapSheet->mergeCells('B' . $row . ':D' . $row++);
            }

            $lastRow = $row - 1;
            if ($lastRow >= 7) {
                $style = $rekapSheet->getStyle('A7:U' . $lastRow);
                $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            $totalRow = $row;
            $rekapSheet->setCellValue('A' . $totalRow, 'TOTAL')->mergeCells('A' . $totalRow . ':D' . $totalRow);
            $seksiValues = $seksiData->values();
            $deptHeadValues = $deptHeadData->values();
            $total_direct = $seksiValues->sum('total_direct') + $deptHeadValues->sum('total_direct') + ($advisorData->total_direct ?? 0) + ($bodData->total_direct ?? 0);
            $total_indirect = $seksiValues->sum('total_indirect') + $deptHeadValues->sum('total_indirect') + ($advisorData->total_indirect ?? 0) + ($bodData->total_indirect ?? 0);
            $total_dir = $seksiValues->sum('total_dir') + $deptHeadValues->sum('total_dir') + ($bodData->total_dir ?? 0);
            $total_advisor = $seksiValues->sum('total_advisor') + $deptHeadValues->sum('total_advisor') + ($advisorData->total_advisor ?? 0);
            $total_ass_dir = $seksiValues->sum('total_ass_dir') + $deptHeadValues->sum('total_ass_dir');
            $total_gnrl_manager = $seksiValues->sum('total_gnrl_manager') + $deptHeadValues->sum('total_gnrl_manager');
            $total_manager = $seksiValues->sum('total_manager') + $deptHeadValues->sum('total_manager');
            $total_ast_manager = $seksiValues->sum('total_ast_manager') + $deptHeadValues->sum('total_ast_manager');
            $total_sect_head = $seksiValues->sum('total_sect_head') + $deptHeadValues->sum('total_sect_head');
            $total_leader = $seksiValues->sum('total_leader') + $deptHeadValues->sum('total_leader');
            $total_pelaksana = $seksiValues->sum('total_pelaksana') + $deptHeadValues->sum('total_pelaksana');
            $total_pkwtt = $seksiValues->sum('total_pkwtt') + $deptHeadValues->sum('total_pkwtt') + ($advisorData->total_pkwtt ?? 0) + ($bodData->total_pkwtt ?? 0);
            $total_pkwt = $seksiValues->sum('total_pkwt') + $deptHeadValues->sum('total_pkwt') + ($advisorData->total_pkwt ?? 0) + ($bodData->total_pkwt ?? 0);
            $total_pk = $seksiValues->sum('total_pk') + $deptHeadValues->sum('total_pk') + ($advisorData->total_pk ?? 0) + ($bodData->total_pk ?? 0);
            $total_l = $seksiValues->sum('total_l') + $deptHeadValues->sum('total_l') + ($advisorData->total_l ?? 0) + ($bodData->total_l ?? 0);
            $total_p = $seksiValues->sum('total_p') + $deptHeadValues->sum('total_p') + ($advisorData->total_p ?? 0) + ($bodData->total_p ?? 0);
            $total_karyawan = $seksiValues->sum('total_karyawan') + $deptHeadValues->sum('total_karyawan') + ($advisorData->total_karyawan ?? 0) + ($bodData->total_karyawan ?? 0);

            $totals = [$total_direct, $total_indirect, $total_dir, $total_advisor, $total_ass_dir, $total_gnrl_manager, $total_manager, $total_ast_manager, $total_sect_head, $total_leader, $total_pelaksana, $total_pkwtt, $total_pkwt, $total_pk, $total_l, $total_p, $total_karyawan];
            $rekapSheet->fromArray($totals, null, 'E' . $totalRow);
            $historyData['totals'] = array_merge(array_fill(0, 4, ''), $totals);

            $allRekapData[$organisasi_id] = [
                'nama' => $organisasi_nama,
                'rows' => $historyData['rows'],
                'totals' => $totals,
            ];

            $totalStyle = $rekapSheet->getStyle('A' . $totalRow . ':U' . $totalRow);
            $totalStyle->getFont()->setBold(true);
            $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $totalStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $percentageRow = $totalRow + 1;
            if ($total_karyawan > 0) {
                $percentages = [
                    $total_pkwtt / $total_karyawan, $total_pkwt / $total_karyawan, $total_pk / $total_karyawan,
                    $total_l / $total_karyawan, $total_p / $total_karyawan, 1
                ];
                $rekapSheet->fromArray($percentages, null, 'P' . $percentageRow);
                $historyData['percentages'] = $percentages;
                foreach (range('P', 'U') as $col) {
                    $rekapSheet->getStyle($col . $percentageRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                }
                $rekapSheet->getStyle('P' . $percentageRow . ':U' . $percentageRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            $allKaryawan = Karyawan::query()->where('tanggal_mulai', '<=', $processDate->endOfMonth())->where(fn($q) => $q->whereNull('tanggal_selesai')->orWhere('tanggal_selesai', '>=', $processDate->startOfMonth()))->when($organisasi_id, fn($q) => $q->where('organisasi_id', $organisasi_id))->get(['domisili', 'alamat']);
            $total_karyawan_all = $allKaryawan->count();
            $karawangKaryawan = $allKaryawan->filter(fn($k) => stripos($k->domisili, 'KARAWANG') !== false || stripos($k->alamat, 'KARAWANG') !== false);
            $total_karawang = $karawangKaryawan->count();
            $total_luar_karawang = $total_karyawan_all - $total_karawang;
            $rekapDomisiliForSheet = [
                ['K', $total_karawang, ($total_karyawan_all > 0 ? ($total_karawang / $total_karyawan_all) : 0)],
                ['LK', $total_luar_karawang, ($total_karyawan_all > 0 ? ($total_luar_karawang / $total_karyawan_all) : 0)],
                ['TOTAL', $total_karyawan_all, ($total_karyawan_all > 0 ? 1 : 0)]
            ];
            $rekapDomisiliForDb = $rekapDomisiliForSheet;

            $startRow = $rekapSheet->getHighestRow() + 2;
            $rekapSheet->setCellValue('A' . $startRow, 'REKAP BERDASARKAN DOMISILI')->mergeCells('A' . $startRow . ':D' . $startRow)->getStyle('A' . $startRow)->getFont()->setBold(true);
            $startRow++;
            $rekapSheet->fromArray($rekapDomisiliForSheet, null, 'A' . $startRow);
            $rekapSheet->getStyle('C' . $startRow . ':C' . ($startRow + 2))->getNumberFormat()->setFormatCode('0.00%');
            $rekapSheet->getStyle('A' . ($startRow + 2) . ':C' . ($startRow + 2))->getFont()->setBold(true);
            $startRow += 4;

            // New section for Karawang sub-districts
            $karawangBarat = $karawangKaryawan->filter(fn($k) => stripos($k->domisili, 'KARAWANG BARAT') !== false || stripos($k->alamat, 'KARAWANG BARAT') !== false)->count();
            $karawangKulon = $karawangKaryawan->filter(fn($k) => stripos($k->domisili, 'KARAWANG KULON') !== false || stripos($k->alamat, 'KARAWANG KULON') !== false)->count();
            $karawangTimur = $karawangKaryawan->filter(fn($k) => stripos($k->domisili, 'KARAWANG TIMUR') !== false || stripos($k->alamat, 'KARAWANG TIMUR') !== false)->count();
            $kecLain = $total_karawang - $karawangBarat - $karawangKulon - $karawangTimur;

            $rekapKecamatan = [
                ['KARAWANG BARAT', $karawangBarat, $total_karawang > 0 ? $karawangBarat / $total_karawang : 0],
                ['KARAWANG KULON', $karawangKulon, $total_karawang > 0 ? $karawangKulon / $total_karawang : 0],
                ['KARAWANG TIMUR', $karawangTimur, $total_karawang > 0 ? $karawangTimur / $total_karawang : 0],
                ['KEC. LAIN ( KARAWANG )', $kecLain, $total_karawang > 0 ? $kecLain / $total_karawang : 0],
                ['TOTAL', $total_karawang, $total_karawang > 0 ? 1 : 0]
            ];

            $rekapSheet->fromArray($rekapKecamatan, null, 'A' . $startRow);
            $rekapSheet->getStyle('C' . $startRow . ':C' . ($startRow + 4))->getNumberFormat()->setFormatCode('0%');
            $rekapSheet->getStyle('A' . ($startRow + 4) . ':C' . ($startRow + 4))->getFont()->setBold(true);
            $startRow += 6;

            // New section for Warung Bambu
            $warungBambuKaryawan = $karawangKaryawan->filter(fn($k) => stripos($k->domisili, 'WARUNG BAMBU') !== false || stripos($k->alamat, 'WARUNG BAMBU') !== false);
            $totalWarungBambu = $warungBambuKaryawan->count();

            $rekapSheet->setCellValue('A' . $startRow, 'TOTAL KELURAHAN WARUNG BAMBU')->setCellValue('C' . $startRow, $totalWarungBambu);
            $startRow++;
            $rekapSheet->setCellValue('B' . $startRow, 'DUSUN');
            
            $dusunKrajan1 = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'KRAJAN I') !== false || stripos($k->alamat, 'KRAJAN I') !== false)->count();
            $dusunKrajan2 = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'KRAJAN II') !== false || stripos($k->alamat, 'KRAJAN II') !== false)->count();
            $dusunSukamaju = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'SUKAMAJU') !== false || stripos($k->alamat, 'SUKAMAJU') !== false)->count();
            $dusunSukamulya = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'SUKAMULYA') !== false || stripos($k->alamat, 'SUKAMULYA') !== false)->count();
            $dusunWarnajaya = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'WARNAJAYA') !== false || stripos($k->alamat, 'WARNAJAYA') !== false)->count();
            $dusunBukaper = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'BUKAPER') !== false || stripos($k->alamat, 'BUKAPER') !== false)->count();
            $dusunPerum = $warungBambuKaryawan->filter(fn($k) => stripos($k->domisili, 'PERUM GADING ELOK') !== false || stripos($k->alamat, 'PERUM GADING ELOK') !== false)->count();
            
            $totalDusun = $dusunKrajan1 + $dusunKrajan2 + $dusunSukamaju + $dusunSukamulya + $dusunWarnajaya + $dusunBukaper + $dusunPerum;

            $rekapDusun = [
                ['KRAJAN I', $dusunKrajan1, $totalDusun > 0 ? $dusunKrajan1 / $totalDusun : 0],
                ['KRAJAN II', $dusunKrajan2, $totalDusun > 0 ? $dusunKrajan2 / $totalDusun : 0],
                ['SUKAMAJU', $dusunSukamaju, $totalDusun > 0 ? $dusunSukamaju / $totalDusun : 0],
                ['SUKAMULYA', $dusunSukamulya, $totalDusun > 0 ? $dusunSukamulya / $totalDusun : 0],
                ['WARNAJAYA', $dusunWarnajaya, $totalDusun > 0 ? $dusunWarnajaya / $totalDusun : 0],
                ['BUKAPER', $dusunBukaper, $totalDusun > 0 ? $dusunBukaper / $totalDusun : 0],
                ['PERUM GADING ELOK', $dusunPerum, $totalDusun > 0 ? $dusunPerum / $totalDusun : 0],
                ['TOTAL', $totalDusun, $totalDusun > 0 ? 1 : 0]
            ];

            $rekapSheet->fromArray($rekapDusun, null, 'B' . $startRow);
            $rekapSheet->getStyle('D' . $startRow . ':D' . ($startRow + 7))->getNumberFormat()->setFormatCode('0%');
            $rekapSheet->getStyle('B' . ($startRow + 7) . ':D' . ($startRow + 7))->getFont()->setBold(true);
            $startRow += 9;

            // Luar Warung Bambu
            $luarWarungBambu = $karawangTimur - $totalWarungBambu;
            $rekapSheet->setCellValue('A' . $startRow, 'LUAR KELURAHAN WARUNGBAMBU')->setCellValue('C' . $startRow, $luarWarungBambu);
            $startRow++;
            $rekapSheet->setCellValue('B' . $startRow, 'KARAWANG TIMUR')->setCellValue('C' . $startRow, $karawangTimur);

            foreach (range('A', 'U') as $columnID) $rekapSheet->getColumnDimension($columnID)->setAutoSize(true);

            // --- Data Karyawan Sheet ---
            $dataSheet = $spreadsheet->createSheet($sheetIndex++);
            $dataSheet->setTitle('Data Karyawan ' . $organisasi_nama);
            $karyawanSheetData = ['grouped_data' => []];

            $dataSheet->mergeCells('A1:DR1')->setCellValue('A1', 'REKAP MANPOWER ' . strtoupper($organisasi_nama ?? 'PT. ADYAWINSA STAMPING INDUSTRIES'))->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $dataSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $dataSheet->setCellValue('A3', 'Period : ' . strtoupper($processDate->format('F Y')));
            $dataSheet->setCellValue('A5', 'Update : ' . Carbon::now()->format('d F Y'));

            $headerRow = 7;
            $subHeaderRow = 8;
            $colIndex = 1;
            $writeMergedHeader = function ($title, $mergeCount, $subtitles) use ($dataSheet, $headerRow, $subHeaderRow, &$colIndex) {
                $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                if ($mergeCount > 1) {
                    $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + $mergeCount - 1);
                    $dataSheet->mergeCells($startCol . $headerRow . ':' . $endCol . ($subtitles ? $headerRow : $subHeaderRow));
                } else {
                    $dataSheet->mergeCells($startCol . $headerRow . ':' . $startCol . $subHeaderRow);
                }
                $dataSheet->setCellValue($startCol . $headerRow, $title);
                if ($subtitles) $dataSheet->fromArray($subtitles, null, $startCol . $subHeaderRow);
                $colIndex += $mergeCount;
            };

            $writeMergedHeader('', 1, null);
            $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $dataSheet->setCellValue($colString . $headerRow, 'NO');
            $dataSheet->setCellValue($colString . $subHeaderRow, 'ALL');
            $colIndex++;
            $writeMergedHeader('NO', 1, null);
            $writeMergedHeader('NAMA LENGKAP', 1, null);
            $writeMergedHeader('NIK', 1, null);
            $writeMergedHeader('TANGGAL MASUK', 1, null);
            $writeMergedHeader('DEPT', 1, null);
            $writeMergedHeader('JABATAN', 1, null);
            $writeMergedHeader('SECTION', 1, null);
            $writeMergedHeader('Group', 1, null);
            $writeMergedHeader('ATASAN LANGSUNG', 1, null);
            $writeMergedHeader('TGL PENETAPAN', 1, null);
            $writeMergedHeader('TGL PENGANGKATAN', 1, null);
            $writeMergedHeader('DIRECT', 1, null);
            $writeMergedHeader('INDIRECT', 1, null);
            $writeMergedHeader('JABATAN', 9, ['DIR', 'ADV', 'ASS DIR', 'GNRL MANAGER', 'MANAGER', 'ASTMANAGER', 'SECT HEAD', 'LEADER', 'PELAKSANA']);
            $writeMergedHeader('STATUS', 3, ['PKWTT', 'PKWT', 'PK']);
            $kontrakHeaders = array_map(fn($i) => 'K' . $i, range(1, 10));
            $kontrakHeaders[] = 'PK';
            $writeMergedHeader('JUMLAH KONTRAK', 11, $kontrakHeaders);
            $writeMergedHeader('TANGGAL BERAKHIR', 1, null);
            for ($i = 1; $i <= 10; $i++) $writeMergedHeader('KONTRAK ' . $i, 2, ['START', 'END']);
            $writeMergedHeader('TEMPAT LAHIR', 1, null);
            $writeMergedHeader('TANGGAL LAHIR', 1, null);
            $writeMergedHeader('USIA', 3, ['TAHUN', 'BULAN', 'HARI']);
            $writeMergedHeader('JK', 2, ['L', 'P']);
            $writeMergedHeader('STATUS KAWINAN', 8, ['BK', 'K', 'KA 1', 'KA 2', 'KA 3', 'KA 4', 'KA 5', 'KA 6']);
            $writeMergedHeader('AGAMA', 6, ['I', 'KRIS', 'KATH', 'H', 'B', '']);
            $writeMergedHeader('TINGKAT PENDIDIKAN', 8, ['TS', 'SD', 'SLTP', 'SLTA/STM', 'DIP', 'SI', 'S2', '']);
            $writeMergedHeader('NAMA ISTRI/SUAMI', 1, null);
            $writeMergedHeader('TTL ISTRI/SUAMI', 1, null);
            $anakHeaders = [];
            for ($i = 1; $i <= 5; $i++) {
                $anakHeaders[] = 'ANAK ' . $i;
                $anakHeaders[] = 'TTL ANAK ' . $i;
            }
            $writeMergedHeader('NAMA & TGL LAHIR ANAK', 10, $anakHeaders);
            $writeMergedHeader('NAMA IBU KANDUNG', 1, null);
            $writeMergedHeader('ALAMAT KTP', 1, null);
            $writeMergedHeader('DOMISILI', 1, null);
            $writeMergedHeader('NO. KTP / SIM', 1, null);
            $writeMergedHeader('NO. TELPON', 1, null);
            $writeMergedHeader('EMAIL', 1, null);
            $writeMergedHeader('PERIODE', 1, null);
            $writeMergedHeader('MASA KERJA', 3, ['TAHUN', 'BULAN', 'HARI']);
            $writeMergedHeader('KETERANGAN', 1, null);
            $writeMergedHeader('TANGGAL HARI INI', 1, null);
            $writeMergedHeader('SINAS', 1, null);

            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex - 1);
            $headerStyle = $dataSheet->getStyle('B' . $headerRow . ':' . $lastCol . $subHeaderRow);
            $headerStyle->getFont()->setBold(true);
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF00B050');
            $dataSheet->getColumnDimension('A')->setWidth(5);
            for ($i = 2; $i < $colIndex; $i++) $dataSheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
            $dataSheet->freezePane('I9');

            $dataKaryawan = Karyawan::query()->where('tanggal_mulai', '<=', $processDate->endOfMonth())->where(fn($q) => $q->whereNull('tanggal_selesai')->orWhere('tanggal_selesai', '>=', $processDate->startOfMonth()))->when($organisasi_id, fn($q) => $q->where('organisasi_id', $organisasi_id))->with(['posisi' => fn($q) => $q->with(['departemen.divisi', 'seksi', 'jabatan', 'parent']), 'kontrak' => fn($q) => $q->orderBy('tanggal_mulai', 'asc'), 'keluarga', 'grup'])->get();
            $sinasCounts = $dataKaryawan->whereNotNull('sinas')->groupBy('sinas')->map(fn ($group) => $group->count())->toArray();

            $dataRow = $subHeaderRow + 1;
            $globalNo = 1;
            $groupedByDept = $dataKaryawan->groupBy(fn($item) => $item->posisi->first()->departemen->nama ?? 'UNKNOWN');

            foreach ($groupedByDept as $deptName => $karyawansInDept) {
                $deptGroup = ['dept_name' => $deptName, 'contracts' => []];
                $no = 1;
                $groupedByContract = $karyawansInDept->groupBy('jenis_kontrak');
                foreach ($groupedByContract as $contractName => $karyawans) {
                    $contractDisplayName = !empty($contractName) ? strtoupper($contractName) : 'LAINNYA';
                    $contractGroup = ['contract_name' => $contractDisplayName, 'rows' => [], 'subtotal' => []];
                    $dataRow++;
                    $dataSheet->setCellValue('B' . $dataRow, $contractDisplayName)->getStyle('B' . $dataRow)->getFont()->setBold(true);
                    $dataSheet->mergeCells('B' . $dataRow . ':I' . $dataRow)->getStyle('B' . $dataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $dataRow++;

                    $contractSubtotalCounts = array_fill(0, $colIndex - 1, 0);
                    foreach ($karyawans as $karyawan) {
                        $posisi = $karyawan->posisi->first();
                        $jabatanName = $posisi->jabatan->nama ?? null;
                        $isKarawang = stripos(strtoupper($karyawan->domisili), 'KARAWANG') !== false;
                        $rowData = [$isKarawang ? 'K' : 'LK', $globalNo++, $no++, $karyawan->nama, $karyawan->ni_karyawan, $karyawan->tanggal_mulai, $deptName, $jabatanName, $posisi->seksi->nama ?? '', $karyawan->grup->nama ?? 'NON SHIFT', $posisi->parent->nama ?? '', null, null, $karyawan->direct ? 1 : '', $karyawan->indirect ? 1 : ''];
                        $jabatanMap = ['DIR', 'ADV', 'ASS DIR', 'GNRL MANAGER', 'MANAGER', 'ASTMANAGER', 'SECT HEAD', 'LEADER', 'PELAKSANA'];
                        foreach ($jabatanMap as $j) $rowData[] = (strtoupper($jabatanName) == $j) ? 1 : '';
                        $statusMap = ['PKWTT', 'PKWT', 'PK'];
                        foreach ($statusMap as $s) $rowData[] = (strtoupper($karyawan->jenis_kontrak) == $s) ? 1 : '';
                        $kontrakCount = $karyawan->kontrak->count();
                        for ($i = 1; $i <= 10; $i++) $rowData[] = ($kontrakCount == $i) ? 1 : '';
                        $rowData[] = '';
                        $rowData[] = $karyawan->kontrak->last()->tanggal_selesai ?? null;
                        $kontrakData = [];
                        $kontrakOnSheet = 0;
                        foreach ($karyawan->kontrak as $kontrak) {
                            if ($kontrakOnSheet < 10) {
                                $kontrakData[] = $kontrak->tanggal_mulai;
                                $kontrakData[] = $kontrak->tanggal_selesai;
                                $kontrakOnSheet++;
                            }
                        }
                        $kontrakData = array_pad($kontrakData, 20, null);
                        $rowData = array_merge($rowData, $kontrakData);
                        $rowData[] = $karyawan->tempat_lahir;
                        $rowData[] = $karyawan->tanggal_lahir;
                        $usia = $karyawan->tanggal_lahir ? Carbon::parse($karyawan->tanggal_lahir)->diff(Carbon::now()) : null;
                        $rowData = array_merge($rowData, [$usia->y ?? null, $usia->m ?? null, $usia->d ?? null]);
                        $rowData[] = ($karyawan->jenis_kelamin == 'L') ? 1 : '';
                        $rowData[] = ($karyawan->jenis_kelamin == 'P') ? 1 : '';
                        $statusKawinData = array_fill(0, 8, '');
                        if (strtoupper($karyawan->status_keluarga) == 'BELUM MENIKAH') {
                            $statusKawinData[0] = 1;
                        } else if (strtoupper($karyawan->status_keluarga) == 'MENIKAH') {
                            if (strtoupper($karyawan->kategori_keluarga) == 'K0') {
                                $statusKawinData[1] = 1;
                            } else if (strtoupper($karyawan->kategori_keluarga) == 'K1') {
                                $statusKawinData[2] = 1;
                            } else if (strtoupper($karyawan->kategori_keluarga) == 'K2') {
                                $statusKawinData[3] = 1;
                            } else if (strtoupper($karyawan->kategori_keluarga) == 'K3') {
                                $statusKawinData[4] = 1;
                            }
                        }
                        $rowData = array_merge($rowData, $statusKawinData);
                        $agamaMap = ['I' => 'ISLAM', 'KRIS' => 'KRISTEN', 'KATH' => 'KATOLIK', 'H' => 'HINDU', 'B' => 'BUDHA'];
                        foreach ($agamaMap as $key => $value) $rowData[] = (strtoupper($karyawan->agama) == $value) ? 1 : '';
                        $rowData[] = '';
                        $pendidikanData = array_fill(0, 8, '');
                        $jenjang = strtoupper($karyawan->jenjang_pendidikan);
                        if ($jenjang == 'SD') {
                            $pendidikanData[1] = 1;
                        } else if ($jenjang == 'SMP' || $jenjang == 'SLTP') {
                            $pendidikanData[2] = 1;
                        } else if ($jenjang == 'SMA' || $jenjang == 'SLTA' || $jenjang == 'STM' || $jenjang == 'SLTA/STM') {
                            $pendidikanData[3] = 1;
                        } else if (in_array($jenjang, ['D1', 'D2', 'D3', 'D4', 'DIP'])) {
                            $pendidikanData[4] = 1;
                        } else if ($jenjang == 'S1' || $jenjang == 'SI') {
                            $pendidikanData[5] = 1;
                        } else if ($jenjang == 'S2') {
                            $pendidikanData[6] = 1;
                        } else if ($jenjang == 'S3') {
                            $pendidikanData[7] = 1;
                        } else if ($jenjang == 'TS') {
                            $pendidikanData[0] = 1;
                        } else if (!empty($jenjang)) {
                            $pendidikanData[7] = 1; // Other
                        }
                        $rowData = array_merge($rowData, $pendidikanData);
                        $pasangan = $karyawan->keluarga->whereIn('hubungan', ['Istri', 'Suami'])->first();
                        $anak = $karyawan->keluarga->where('hubungan', 'Anak');
                        $rowData[] = $pasangan->nama ?? null;
                        $rowData[] = $pasangan ? ($pasangan->tempat_lahir . ', ' . $pasangan->tanggal_lahir) : null;
                        $anakData = [];
                        foreach ($anak as $a) {
                            if (count($anakData) < 10) {
                                $anakData[] = $a->nama;
                                $anakData[] = $a->tempat_lahir . ', ' . $a->tanggal_lahir;
                            }
                        }
                        $anakData = array_pad($anakData, 10, null);
                        $rowData = array_merge($rowData, $anakData);
                        $rowData[] = $karyawan->nama_ibu_kandung;
                        $rowData[] = $karyawan->alamat;
                        $rowData[] = ($karyawan->alamat !== $karyawan->domisili) ? $karyawan->domisili : '';
                        $rowData[] = "'" . $karyawan->nik;
                        $rowData[] = "'" . $karyawan->no_telp;
                        $rowData[] = $karyawan->email;
                        $rowData[] = null;
                        $masaKerja = $karyawan->tanggal_mulai ? Carbon::parse($karyawan->tanggal_mulai)->diff(Carbon::now()) : null;
                        $rowData = array_merge($rowData, [$masaKerja->y ?? null, $masaKerja->m ?? null, $masaKerja->d ?? null]);
                        $rowData[] = null;
                        $rowData[] = Carbon::now()->format('Y-m-d');
                        $rowData[] = $karyawan->sinas;
                        
                        $dataSheet->fromArray($rowData, null, 'A' . $dataRow);
                        $dataSheet->getStyle('B' . $dataRow . ':' . $lastCol . $dataRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $contractGroup['rows'][] = $rowData;
                        $dataRow++;
                    }

                    if (!empty($contractGroup['rows'])) {
                        $subtotalRow = array_fill(0, count($contractGroup['rows'][0]), '');
                        $subtotalRow[1] = 'SUB TOTAL ' . $contractDisplayName;
                        $subtotalSums = array_fill(0, count($contractGroup['rows'][0]), 0);
                        foreach($contractGroup['rows'] as $r) {
                            for($i = 13; $i < count($r); $i++) {
                                if(is_numeric($r[$i])) $subtotalSums[$i] += $r[$i];
                            }
                        }
                        for($i = 13; $i < count($subtotalRow); $i++) {
                            if($subtotalSums[$i] > 0) $subtotalRow[$i] = $subtotalSums[$i];
                        }

                        $dataSheet->fromArray($subtotalRow, null, 'A' . $dataRow);
                        $dataSheet->mergeCells('B' . $dataRow . ':K' . $dataRow);
                        $subtotalStyle = $dataSheet->getStyle('B' . $dataRow . ':' . $lastCol . $dataRow);
                        $subtotalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
                        $subtotalStyle->getFont()->setBold(true);
                        $subtotalStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        $subtotalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $contractGroup['subtotal'] = $subtotalRow;
                        $dataRow++;
                    }

                    $deptGroup['contracts'][] = $contractGroup;
                }

                $deptTotals = array_fill(0, $colIndex - 1, 0);
                $hasDataForDeptTotal = false;
                foreach($deptGroup['contracts'] as $contract) {
                    if(isset($contract['subtotal']) && !empty($contract['subtotal'])) {
                        $hasDataForDeptTotal = true;
                        for($i=13; $i < count($contract['subtotal']); $i++) {
                            if(is_numeric($contract['subtotal'][$i])) {
                                $deptTotals[$i] += $contract['subtotal'][$i];
                            }
                        }
                    }
                }

                if($hasDataForDeptTotal) {
                    $deptTotalRow = array_fill(0, $colIndex - 1, '');
                    $deptTotalRow[1] = 'TOTAL ' . $deptName;
                    for($i=13; $i < count($deptTotalRow); $i++) {
                        if($deptTotals[$i] > 0) $deptTotalRow[$i] = $deptTotals[$i];
                    }
                    $dataSheet->fromArray($deptTotalRow, null, 'A' . $dataRow);
                    $dataSheet->mergeCells('B' . $dataRow . ':K' . $dataRow);
                    $totalStyle = $dataSheet->getStyle('B' . $dataRow . ':' . $lastCol . $dataRow);
                    $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
                    $totalStyle->getFont()->setBold(true);
                    $totalStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $dataRow++;

                    $totalKaryawanInDept = count($karyawansInDept);
                    if($totalKaryawanInDept > 0) {
                        $percentageRow = array_fill(0, $colIndex - 1, '');
                        $percentageRow[1] = 'PRESENTASE';
                        
                        $indicesToCalculate = array_merge(
                            [13, 14], // DIRECT, INDIRECT
                            range(15, 23), // JABATAN
                            range(24, 26), // STATUS
                            [64, 65] // JK
                        );

                        foreach($indicesToCalculate as $idx) {
                            if(isset($deptTotals[$idx]) && $deptTotals[$idx] > 0) {
                                $percentageRow[$idx] = $deptTotals[$idx] / $totalKaryawanInDept;
                            }
                        }
                        
                        $dataSheet->fromArray($percentageRow, null, 'A' . $dataRow);
                        $dataSheet->mergeCells('B' . $dataRow . ':K' . $dataRow);
                        
                        foreach($indicesToCalculate as $idx) {
                            if(isset($percentageRow[$idx]) && $percentageRow[$idx] !== '') {
                                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
                                $dataSheet->getStyle($colLetter . $dataRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                            }
                        }

                        $percentageStyle = $dataSheet->getStyle('B' . $dataRow . ':' . $lastCol . $dataRow);
                        $percentageStyle->getFont()->setBold(true);
                        $percentageStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $percentageStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAD3');
                        $percentageStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        $dataSheet->getStyle('N' . $dataRow . ':' . $lastCol . $dataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $dataRow++;
                    }
                }

                $karyawanSheetData['grouped_data'][] = $deptGroup;
            }

            $summaryStartRow = $dataRow;
            $dataSheet->setCellValue('DH' . $summaryStartRow, 'REKAP SINAS')->getStyle('DH' . $summaryStartRow)->getFont()->setBold(true);
            $summaryStartRow++;
            $totalSinas = 0;
            foreach ($sinasCounts as $category => $count) {
                $dataSheet->setCellValue('DH' . $summaryStartRow, $category)->setCellValue('DI' . $summaryStartRow, $count);
                $totalSinas += $count;
                $summaryStartRow++;
            }
            $dataSheet->setCellValue('DH' . $summaryStartRow, 'TOTAL')->getStyle('DH' . $summaryStartRow)->getFont()->setBold(true);
            $dataSheet->setCellValue('DI' . $summaryStartRow, $totalSinas)->getStyle('DI' . $summaryStartRow)->getFont()->setBold(true);

            // --- Save to History ---
            $fullHistoryData = [
                'rekap_manpower' => $historyData,
                'data_karyawan' => $karyawanSheetData,
                'rekap_domisili' => $rekapDomisiliForDb,
                'rekap_sinas' => $sinasCounts,
            ];

            DB::transaction(function () use ($processDate, $organisasi_id, $fullHistoryData) {
                $history = RekapManpowerHistory::lockForUpdate()->firstOrNew([
                    'period' => $processDate->format('Y-m-d'),
                ]);
                $data = $history->data ?? [];
                $data[$organisasi_id] = $fullHistoryData;
                $history->data = $data;
                $history->save();
            });
        }

        if (($organisasi_filter === 'all' || !$organisasi_filter) && count($orgs_to_process) > 1) {
            $allRekapSheet = $spreadsheet->createSheet($sheetIndex++);
            $allRekapSheet->setTitle('Rekap Manpower ASI (ALL)');
            $allRekapSheet->getDefaultRowDimension()->setRowHeight(25);

            $newHeaders = [
                ['NO', 'DIVISION', 'DEPARTMENT', 'AREA', 'STATUS', '', '', 'JK', '', 'TOTAL MAN POWER', '% TETAP', '% KONTRAK', 'TOTAL PKWT,PKWTT dan PK', '20%'],
                ['', '', '', '', 'PKWTT', 'PKWT', 'PENGKARYAAN', 'L', 'P', '', '', '', '', '']
            ];

            $current_row = 1;

            foreach ($allRekapData as $org_id => $orgData) {
                // Title for the plant
                $allRekapSheet->mergeCells('A'.$current_row.':N'.$current_row)->setCellValue('A'.$current_row, 'REKAP MANPOWER ' . strtoupper($orgData['nama']));
                $allRekapSheet->getStyle('A'.$current_row)->getFont()->setBold(true)->setSize(14);
                $allRekapSheet->getStyle('A'.$current_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $current_row += 2;

                // Headers
                $header_start_row = $current_row;
                $allRekapSheet->fromArray($newHeaders, null, 'A' . $header_start_row);
                $allRekapSheet->mergeCells('E'.$header_start_row.':G'.$header_start_row); // STATUS
                $allRekapSheet->mergeCells('H'.$header_start_row.':I'.$header_start_row); // JK
                $allRekapSheet->mergeCells('A'.$header_start_row.':A'.($header_start_row+1));
                $allRekapSheet->mergeCells('B'.$header_start_row.':B'.($header_start_row+1));
                $allRekapSheet->mergeCells('C'.$header_start_row.':C'.($header_start_row+1));
                $allRekapSheet->mergeCells('D'.$header_start_row.':D'.($header_start_row+1));
                $allRekapSheet->mergeCells('J'.$header_start_row.':J'.($header_start_row+1));
                $allRekapSheet->mergeCells('K'.$header_start_row.':K'.($header_start_row+1));
                $allRekapSheet->mergeCells('L'.$header_start_row.':L'.($header_start_row+1));
                $allRekapSheet->mergeCells('M'.$header_start_row.':M'.($header_start_row+1));
                $allRekapSheet->mergeCells('N'.$header_start_row.':N'.($header_start_row+1));
                
                $headerRange = 'A'.$header_start_row.':N'.($header_start_row + 1);
                $headerStyle = $allRekapSheet->getStyle($headerRange);
                $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF00B050');
                $headerStyle->getFont()->setBold(true);
                $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $current_row += 2;

                // Data rows
                $data_start_row = $current_row;
                if (!empty($orgData['rows'])) {
                    $b_merge_start_row = $current_row;
                    $c_merge_start_row = $current_row;
                    $rowCount = count($orgData['rows']);
                    for ($i = 0; $i < $rowCount; $i++) {
                        $rowData = $orgData['rows'][$i];

                        // Before writing, check if we need to finalize a previous merge
                        if ($i > 0 && $rowData[1] !== '') { // New division
                            if (($current_row - 1) > $b_merge_start_row) {
                                $allRekapSheet->mergeCells('B' . $b_merge_start_row . ':B' . ($current_row - 1));
                                $allRekapSheet->getStyle('B' . $b_merge_start_row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                            }
                            $b_merge_start_row = $current_row;
                        }
                        if ($i > 0 && $rowData[2] !== '') { // New department
                            if (($current_row - 1) > $c_merge_start_row) {
                                $allRekapSheet->mergeCells('C' . $c_merge_start_row . ':C' . ($current_row - 1));
                                $allRekapSheet->getStyle('C' . $c_merge_start_row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                            }
                            $c_merge_start_row = $current_row;
                        }

                        $totalManPower = $rowData[20] ?? 0;
                        $pkwtt = $rowData[15] ?? 0;
                        $pkwt = $rowData[16] ?? 0;
                        $pk = $rowData[17] ?? 0;
                        $totalPk = $pkwtt + $pkwt + $pk;

                        $newRowData = [
                            $rowData[0], // NO
                            $rowData[1], // DIVISION
                            $rowData[2], // DEPARTMENT
                            $rowData[3], // AREA
                            $pkwtt,
                            $pkwt,
                            $pk,
                            $rowData[18] ?? 0, // L
                            $rowData[19] ?? 0, // P
                            $totalManPower,
                            $totalManPower > 0 ? $pkwtt / $totalManPower : 0,
                            $totalManPower > 0 ? ($pkwt + $pk) / $totalManPower : 0,
                            $totalPk,
                            round($totalPk * 0.2)
                        ];
                        $allRekapSheet->fromArray($newRowData, null, 'A' . $current_row);

                        if ($rowData[1] === 'ADVISOR' || $rowData[1] === 'DIRECTOR') {
                            $allRekapSheet->mergeCells('B' . $current_row . ':D' . $current_row);
                        }

                        $allRekapSheet->getStyle('K' . $current_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                        $allRekapSheet->getStyle('L' . $current_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                        
                        $current_row++;
                    }

                    // After loop, merge the last blocks
                    $last_data_row = $current_row - 1;
                    if ($last_data_row > $b_merge_start_row) {
                        $allRekapSheet->mergeCells('B' . $b_merge_start_row . ':B' . $last_data_row);
                        $allRekapSheet->getStyle('B' . $b_merge_start_row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                    if ($last_data_row > $c_merge_start_row) {
                        $allRekapSheet->mergeCells('C' . $c_merge_start_row . ':C' . $last_data_row);
                        $allRekapSheet->getStyle('C' . $c_merge_start_row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                }
                
                $last_data_row = $current_row - 1;
                if ($last_data_row >= $data_start_row) {
                    $style = $allRekapSheet->getStyle('A'.$data_start_row.':N'.$last_data_row);
                    $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                // Total row
                $totalRowData = $orgData['totals'];
                $totalManPower = $totalRowData[16] ?? 0;
                $pkwtt = $totalRowData[11] ?? 0;
                $pkwt = $totalRowData[12] ?? 0;
                $pk = $totalRowData[13] ?? 0;
                $totalPk = $pkwtt + $pkwt + $pk;
                $newTotalRowData = [
                    'TOTAL',
                    '',
                    '',
                    '',
                    $pkwtt,
                    $pkwt,
                    $pk,
                    $totalRowData[14] ?? 0,
                    $totalRowData[15] ?? 0,
                    $totalManPower,
                    $totalManPower > 0 ? $pkwtt / $totalManPower : 0,
                    $totalManPower > 0 ? ($pkwt + $pk) / $totalManPower : 0,
                    $totalPk,
                    round($totalPk * 0.2)
                ];
                $allRekapSheet->fromArray($newTotalRowData, null, 'A' . $current_row);
                $allRekapSheet->getStyle('K' . $current_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                $allRekapSheet->getStyle('L' . $current_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                $allRekapSheet->mergeCells('A'.$current_row.':D'.$current_row);
                
                $totalStyle = $allRekapSheet->getStyle('A' . $current_row . ':N' . $current_row);
                $totalStyle->getFont()->setBold(true);
                $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $totalStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $current_row++;
                $totalStatus = ($totalRowData[11] ?? 0) + ($totalRowData[12] ?? 0) + ($totalRowData[13] ?? 0);
                $totalAsiRowData = [
                    'TOTAL ' . $orgData['nama'],
                    '',
                    '',
                    '',
                    $totalStatus,
                    '',
                    '',
                    $totalRowData[14] ?? 0,
                    $totalRowData[15] ?? 0,
                    $totalRowData[16] ?? 0,
                    '',
                    '',
                    '',
                    ''
                ];
                $allRekapSheet->fromArray($totalAsiRowData, null, 'A' . $current_row);
                $allRekapSheet->mergeCells('A' . $current_row . ':D' . $current_row);
                $allRekapSheet->mergeCells('E' . $current_row . ':G' . $current_row);
                $totalAsiStyle = $allRekapSheet->getStyle('A' . $current_row . ':N' . $current_row);
                $totalAsiStyle->getFont()->setBold(true);
                $totalAsiStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $totalAsiStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $current_row += 2; // Add some space before the next table
            }
            
            foreach (range('A', 'N') as $columnID) {
                $allRekapSheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = 'Rekap Manpower ASI Plant 1 & 2 ' . $processDate->format('F Y') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}