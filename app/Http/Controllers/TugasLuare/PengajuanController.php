<?php

namespace App\Http\Controllers\TugasLuare;

use Throwable;
use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TugasLuare\TugasLuar;
use Illuminate\Support\Facades\Validator;
use App\Models\TugasLuare\PengikutTugasLuar;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->hasRole('personalia') || auth()->user()->hasRole('security')) {
            return redirect()->route('tugasluare.approval');
        }

        $karyawans = Karyawan::where('status_karyawan', 'AT')
            ->where('organisasi_id', auth()->user()->organisasi_id)
            ->get();

        $dataPage = [
            'pageTitle' => "TugasLuar-E - Pengajuan TL",
            'page' => 'tugasluare-pengajuan',
            'karyawans' => $karyawans
        ];
        return view('pages.tugasluar-e.pengajuan.index', $dataPage);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->jenis_keberangkatan !== 'KTR') {
            $dataValidate = [
                'jam_pergi' => ['required', 'date_format:H:i'],
                'jam_kembali' => ['nullable', 'date_format:H:i'],
                'jenis_kendaraan' => ['required','in:MOTOR,MOBIL'],
                'jenis_keberangkatan' => ['required','in:RMH,KTR,LNA'],
                'jenis_kepemilikan' => ['required','in:OP,OJ,PR'],
                'kode_wilayah' => ['required', 'regex:/^[A-Za-z]+$/'],
                'nomor_polisi' => ['required','numeric'],
                'seri_akhir' => ['required','regex:/^[A-Za-z]+$/'],
                'km_awal' => ['required', 'numeric'],
                'tempat_asal' => ['required'],
                'tempat_tujuan' => ['required'],
                'keterangan' => ['required'],
                'id_pengikut.*' => ['exists:karyawans,id_karyawan', 'distinct'],
                'id_pengikut' => ['array'],
                'pengemudi' => ['required','exists:karyawans,id_karyawan']
            ];

            $tanggal_pergi = date('Y-m-d H:i:s', strtotime("$request->jam_pergi"));
            $tanggal_kembali = $request->jam_kembali ? date('Y-m-d H:i:s', strtotime("$request->jam_kembali")) : null;
            $jenis_kendaraan = $request->jenis_kendaraan;
            $jenis_kepemilikan = $request->jenis_kepemilikan;
            $jenis_keberangkatan = $request->jenis_keberangkatan;
            $kode_wilayah = strtoupper(trim($request->kode_wilayah));
            $no_polisi = $request->nomor_polisi;
            $seri_akhir = strtoupper(trim($request->seri_akhir));
            $no_polisi_formatted = $kode_wilayah.'-'.$no_polisi.'-'.$seri_akhir;
            $tempat_asal = $request->tempat_asal;
            $tempat_tujuan = $request->tempat_tujuan;
            $km_awal = $request->km_awal;
            $keterangan = $request->keterangan;
            $pengemudi = $request->pengemudi;
            $id_pengikut = $request->id_pengikut;

        } else {
            $dataValidate = [
                'jam_pergi' => ['required', 'date_format:H:i'],
                'jam_kembali' => ['nullable', 'date_format:H:i'],
                'jenis_kendaraan' => ['required','in:MOTOR,MOBIL'],
                'jenis_kepemilikan' => ['required','in:OP,OJ,PR'],
                'jenis_keberangkatan' => ['required','in:RMH,KTR,LNA'],
                'tempat_asal' => ['required'],
                'tempat_tujuan' => ['required'],
                'keterangan' => ['required'],
                'id_pengikut.*' => ['exists:karyawans,id_karyawan', 'distinct'],
                'id_pengikut' => ['array'],
                'pengemudi' => ['required','exists:karyawans,id_karyawan']
            ];

            $tanggal_pergi = date('Y-m-d H:i:s', strtotime("$request->jam_pergi"));
            $tanggal_kembali = $request->jam_kembali ? date('Y-m-d H:i:s', strtotime("$request->jam_kembali")) : null;
            $jenis_kendaraan = $request->jenis_kendaraan;
            $jenis_kepemilikan = $request->jenis_kepemilikan;
            $jenis_keberangkatan = $request->jenis_keberangkatan;
            $tempat_asal = $request->tempat_asal;
            $tempat_tujuan = $request->tempat_tujuan;
            $keterangan = $request->keterangan;
            $pengemudi = $request->pengemudi;
            $id_pengikut = $request->id_pengikut;
        }

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $posisi = auth()->user()->karyawan->posisi[0];

            //STATIC PARAMS
            $rate = 10000;
            $pembagi = $jenis_kendaraan == 'MOTOR' ? 25 : 10;

            // DINAMIC PARAMS
            // $rate = SettingTugasLuar::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'RATE_BBM')->first()->value;
            // $pembagi = SettingTugasLuar::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'PEMBAGI_'.$jenis_kendaraan)->first()->value;
            $tugasLuar = TugasLuar::create([
                'id_tugasluar' => 'TL-' . Str::random(4).'-'. date('YmdHis'),
                'organisasi_id' => auth()->user()->organisasi_id,
                'karyawan_id' => auth()->user()->karyawan->id_karyawan,
                'ni_karyawan' => auth()->user()->karyawan->ni_karyawan,
                'departemen_id' => $posisi->departemen_id,
                'divisi_id' => $posisi->divisi_id,
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'tanggal_pergi_planning' => $tanggal_pergi,
                'tanggal_kembali_planning' => $tanggal_kembali,
                'jenis_kendaraan' => $jenis_kendaraan,
                'jenis_kepemilikan' => $jenis_kepemilikan,
                'jenis_keberangkatan' => $jenis_keberangkatan,
                'no_polisi' => $no_polisi_formatted ?? null,
                'km_awal' => $km_awal ?? 0,
                'tempat_asal' => $tempat_asal,
                'tempat_tujuan' => $tempat_tujuan,
                'keterangan' => $keterangan,
                'pengemudi_id' => $pengemudi,
                'pembagi' => $pembagi,
                'rate' => $rate,
            ]);

            if (!empty($id_pengikut)){
                $pengikuts = Karyawan::whereIn('id_karyawan', $id_pengikut)->get();
                $details = [];
                $details[] = [
                    'karyawan_id' => auth()->user()->karyawan->id_karyawan,
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'departemen_id' => auth()->user()->karyawan->posisi[0]->departemen_id,
                    'divisi_id' => auth()->user()->karyawan->posisi[0]->divisi_id,
                    'ni_karyawan' => auth()->user()->karyawan->ni_karyawan,
                    'pin' => auth()->user()->karyawan->pin,
                    'role' => 'M'
                ];

                foreach ($pengikuts as $pengikut) {
                    $details[] = [
                        'karyawan_id' => $pengikut->id_karyawan,
                        'organisasi_id' => $pengikut->organisasi_id,
                        'departemen_id' => $pengikut->posisi[0]->departemen_id,
                        'divisi_id' => $pengikut->posisi[0]->divisi_id,
                        'ni_karyawan' => $pengikut->ni_karyawan,
                        'pin' => $pengikut->pin,
                        'role' => 'F'
                    ];
                }
    
                $tugasLuar->pengikut()->createMany($details);
            } else {
                $tugasLuar->pengikut()->create([
                    'karyawan_id' => auth()->user()->karyawan->id_karyawan,
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'departemen_id' => $posisi->departemen_id,
                    'divisi_id' => $posisi->divisi_id,
                    'ni_karyawan' => auth()->user()->karyawan->ni_karyawan,
                    'pin' => auth()->user()->karyawan->pin,
                    'role' => 'M'
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Pengajuan Tugas Luar Berhasil Dibuat'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'tugasluars.id_tugasluar',
            1 => 'tugasluars.tanggal',
            2 => 'tugasluars.jenis_kendaraan',
            3 => 'tugasluars.tanggal_pergi_planning',
            4 => 'tugasluars.tanggal_kembali_planning',
            5 => 'tugasluars.tempat_asal',
            6 => 'tugasluars.km_awal',
            8 => 'tugasluars.km_selisih',
            9 => 'tugasluars.keterangan',
            10 => 'tugasluars.checked_at',
            11 => 'tugasluars.legalized_at',
        );

        $totalData = TugasLuar::count();
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

        $id_karyawan = auth()->user()->karyawan->id_karyawan;
        $dataFilter['id_karyawan'] = $id_karyawan;

        $tugasluars = TugasLuar::getData($dataFilter, $settings);
        $totalFiltered = TugasLuar::countData($dataFilter);

        $dataTable = [];

        if (!empty($tugasluars)) {
            foreach ($tugasluars as $data) {
                $checked = '';
                $legalized = '';
                $known = '';
                $rejected = '';
                $aksi = '-';
                $is_rejected = false;
                $formattedPengikut = '';
                if ($data->pengikut()->where('role', 'F')->exists()) {
                    $pengikuts = $data->pengikut()->where('role', 'F')->pluck('karyawan_id')->toArray();
                    $pengikutNames = Karyawan::whereIn('id_karyawan', $pengikuts)->pluck('nama')->toArray();
                    $formattedPengikut = array_map(function($pengikut) {
                        return '<span class="badge badge-primary m-1">' . $pengikut . '</span>';
                    }, $pengikutNames);
                }
            
                if($data->no_polisi) {
                    $no_polisi_array = explode('-', $data->no_polisi);
                    $kode_wilayah = $no_polisi_array[0];
                    $nomor_polisi = $no_polisi_array[1];
                    $seri_akhir = $no_polisi_array[2];
                }

                $jenis_kepemilikan = $data->jenis_kepemilikan == 'OP' ? 'OPERASIONAL' : ($data->jenis_kepemilikan == 'OJ' ? 'OPERASIONAL JABATAN' : 'PRIBADI');
                $jenis_kendaraan = $data->jenis_kendaraan == 'MOTOR' ? '🏍️' : '🚗';
                $kendaraan = '<small class="text-center">'.$jenis_kendaraan.' '.$data?->no_polisi.'<br><span class="text-center">'.$jenis_kepemilikan.'</span></small>';
                $jenis_keberangkatan_text = $data->jenis_keberangkatan == 'RMH' ? 'RUMAH' : ($data->jenis_keberangkatan == 'KTR' ? 'KANTOR' : 'LAINNYA');
                $rute = '<div class="d-flex gap-1 text-center">'.'<p><small class="text-fade">'.strtoupper($data->tempat_asal).'</small></p>'.' ➡️ '.'<p><small class="text-fade">'.strtoupper($data->tempat_tujuan).'</small></p>'.'</div><div class="row"><p><small> Driver : '.$data->nama_pengemudi.'</small><br><small> Asal : '.$jenis_keberangkatan_text.'</small></p></div>';
                $status = $data->status == 'WAITING' ? '<span class="badge badge-warning">WAITING</span>' : ($data->status == 'ONGOING' ? '<span class="badge badge-info">ON GOING</span>' : ($data->status == 'COMPLETED' ? '<span class="badge badge-success">COMPLETED</span>' : '<span class="badge badge-danger">REJECTED</span>'));
                $jam_pergi = '<div class="d-flex gap-1 text-center">
                                <p>' . Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_pergi_planning)->format('H:i') . ' WIB <span class="badge badge-warning">Planning</span></p>
                                <br>
                                <p>' . ($data->tanggal_pergi_aktual ? Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_pergi_aktual)->format('H:i') . ' WIB <span class="badge badge-success">Aktual</span>' : '') . '</p>
                              </div>';
                $jam_kembali = $data->tanggal_kembali_planning ? '<div class="d-flex gap-1 text-center">
                                <p>' . Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_kembali_planning)->format('H:i') . ' WIB <span class="badge badge-warning">Planning</span></p>
                                <br>
                                <p>' . ($data->tanggal_kembali_aktual ? Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_kembali_aktual)->format('H:i') . ' WIB <span class="badge badge-success">Aktual</span>' : '') . '</p>
                            </div>' : '-';


                if($data->checked_by) {
                    $checked = '✅<br><small class="text-bold">'.$data?->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                } else {
                    $checked = 'NEED CHECKED';
                }

                if($data->legalized_by) {
                    $legalized = '✅<br><small class="text-bold">'.$data?->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                    
                    if($data->jenis_keberangkatan == 'KTR'){
                        if($data->status == 'WAITING') {
                            $aksi = '
                            <div class="btn-group"><button class="btn btn-sm btn-primary btnPergi" data-id-tugasluar="'.$data->id_tugasluar.'" data-kode-wilayah="'.($kode_wilayah ?? '').'" data-nomor-polisi="'.($nomor_polisi ?? '').'" data-seri-akhir="'.($seri_akhir ?? '').'" data-km="'.($data->km_awal ?? '').'"><i class="fas fa-running"></i>&nbsp;Pergi</button><button class="btn btn-sm btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-times"></i> Delete</button></div>
                            ';
                            if($data->km_awal) {
                                $aksi = '
                                <div class="btn-group"><button class="btn btn-sm btn-primary btnShowQR" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-qrcode"></i>&nbsp;Show</button><button class="btn btn-sm btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-times"></i> Delete</button></div>
                                ';
                            };
                        } elseif ($data->status == 'ONGOING') {
                            $aksi = '
                            <div class="btn-group"><button class="btn btn-sm btn-primary btnKembali" data-id-tugasluar="'.$data->id_tugasluar.'" data-kode-wilayah="'.($kode_wilayah ?? '').'" data-nomor-polisi="'.($nomor_polisi ?? '').'" data-seri-akhir="'.($seri_akhir ?? '').'" data-km="'.($data->km_awal ?? '').'"><i class="fas fa-home"></i>&nbsp;Kembali</button><button class="btn btn-sm btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-times"></i> Delete</button></div>
                            ';

                            if($data->km_akhir) {
                                $aksi = '
                                <div class="btn-group"><button class="btn btn-sm btn-primary btnShowQR" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-qrcode"></i>&nbsp;Show</button><button class="btn btn-sm btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-times"></i> Delete</button></div>
                                ';
                            };
                        }
                    } else {
                        if($data->status == 'WAITING') {
                            $aksi = '
                                <div class="btn-group"><button class="btn btn-sm btn-primary btnPergi" data-id-tugasluar="'.$data->id_tugasluar.'" data-kode-wilayah="'.($kode_wilayah ?? '').'" data-nomor-polisi="'.($nomor_polisi ?? '').'" data-seri-akhir="'.($seri_akhir ?? '').'" data-km="'.($data->km_awal ?? '').'"><i class="fas fa-running"></i>&nbsp;Pergi</button><button class="btn btn-sm btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-times"></i> Delete</button></div>
                            ';
                        } elseif ($data->status == 'ONGOING'){
                            $aksi = '
                                <div class="btn-group"><button class="btn btn-sm btn-primary btnKembali" data-id-tugasluar="'.$data->id_tugasluar.'" data-kode-wilayah="'.($kode_wilayah ?? '').'" data-nomor-polisi="'.($nomor_polisi ?? '').'" data-seri-akhir="'.($seri_akhir ?? '').'" data-km="'.($data->km_awal ?? '').'"><i class="fas fa-home"></i>&nbsp;Kembali</button><button class="btn btn-sm btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-times"></i> Delete</button></div>
                            ';
                        }
                    }
                } else {
                    $legalized = 'NEED LEGALIZED';
                    $aksi = '<div class="btn-group">
                        <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" 
                            data-id-tugasluar="'.$data->id_tugasluar.'" 
                            data-jam-pergi="'.Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_pergi_planning)->format('H:i').'" 
                            data-jam-kembali="'.Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_kembali_planning)->format('H:i').'" 
                            data-jenis-kendaraan="'.$data->jenis_kendaraan.'" 
                            data-jenis-kepemilikan="'.$data->jenis_kepemilikan.'" 
                            data-jenis-keberangkatan="'.$data->jenis_keberangkatan.'" 
                            data-kode-wilayah="'.($kode_wilayah ?? '').'" 
                            data-nomor-polisi="'.($nomor_polisi ?? '').'" 
                            data-seri-akhir="'.($seri_akhir ?? '').'" 
                            data-tempat-asal="'.$data->tempat_asal.'" 
                            data-tempat-tujuan="'.$data->tempat_tujuan.'" 
                            data-pengemudi="'.$data->pengemudi_id.'"
                            data-km-awal="'.$data->km_awal.'" 
                            data-keterangan="'.$data->keterangan.'"
                            >
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                }

                if($data->rejected_by) {
                    $is_rejected = true;
                    $rejected = '❌<br><small class="text-bold">'.$data?->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                $nestedData['id_tugasluar'] = $data->id_tugasluar;
                $nestedData['tanggal'] = Carbon::parse($data->tanggal)->format('d M Y');
                $nestedData['kendaraan'] = $kendaraan;
                $nestedData['pergi'] = $jam_pergi;
                $nestedData['kembali'] = $jam_kembali;
                $nestedData['km_awal'] = $data->km_awal. ' Km';
                $nestedData['km_akhir'] = $data->km_akhir. ' Km';
                $nestedData['km_selisih'] = $data->km_selisih. ' Km';
                $nestedData['rute'] = $rute;
                $nestedData['pengikut'] = $formattedPengikut;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked'] = $is_rejected ? $rejected : $checked;
                $nestedData['legalized'] = $is_rejected ? $rejected : $legalized;
                $nestedData['status'] = $status;
                $nestedData['aksi'] = $aksi;

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
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
    public function update(Request $request, string $id_tugasluar)
    {
        if ($request->jenis_keberangkatanEdit !== 'KTR') {
            $dataValidate = [
                'jam_pergiEdit' => ['required', 'date_format:H:i'],
                'jam_kembaliEdit' => ['nullable', 'date_format:H:i'],
                'jenis_kendaraanEdit' => ['required','in:MOTOR,MOBIL'],
                'jenis_keberangkatanEdit' => ['required','in:RMH,KTR,LNA'],
                'jenis_kepemilikanEdit' => ['required','in:OP,OJ,PR'],
                'kode_wilayahEdit' => ['required', 'regex:/^[A-Za-z]+$/'],
                'nomor_polisiEdit' => ['required','numeric'],
                'seri_akhirEdit' => ['required','regex:/^[A-Za-z]+$/'],
                'km_awalEdit' => ['required', 'numeric'],
                'tempat_asalEdit' => ['required'],
                'tempat_tujuanEdit' => ['required'],
                'keteranganEdit' => ['required'],
                'id_pengikutEdit.*' => ['exists:karyawans,id_karyawan', 'distinct'],
                'id_pengikutEdit' => ['array'],
                'pengemudiEdit' => ['required','exists:karyawans,id_karyawan']
            ];

            $tanggal_pergi = date('Y-m-d H:i:s', strtotime("$request->jam_pergiEdit"));
            $tanggal_kembali = $request->jam_kembaliEdit ? date('Y-m-d H:i:s', strtotime("$request->jam_kembaliEdit")) : null;
            $jenis_kendaraan = $request->jenis_kendaraanEdit;
            $jenis_kepemilikan = $request->jenis_kepemilikanEdit;
            $jenis_keberangkatan = $request->jenis_keberangkatanEdit;
            $kode_wilayah = strtoupper(trim($request->kode_wilayahEdit));
            $no_polisi = $request->nomor_polisiEdit;
            $seri_akhir = strtoupper(trim($request->seri_akhirEdit));
            $no_polisi_formatted = $kode_wilayah.'-'.$no_polisi.'-'.$seri_akhir;
            $tempat_asal = $request->tempat_asalEdit;
            $tempat_tujuan = $request->tempat_tujuanEdit;
            $km_awal = $request->km_awalEdit;
            $keterangan = $request->keteranganEdit;
            $pengemudi = $request->pengemudiEdit;
            $id_pengikut = $request->id_pengikutEdit;

        } else {
            $dataValidate = [
                'jam_pergiEdit' => ['required', 'date_format:H:i'],
                'jam_kembaliEdit' => ['nullable', 'date_format:H:i'],
                'jenis_kendaraanEdit' => ['required','in:MOTOR,MOBIL'],
                'jenis_kepemilikanEdit' => ['required','in:OP,OJ,PR'],
                'jenis_keberangkatanEdit' => ['required','in:RMH,KTR,LNA'],
                'tempat_asalEdit' => ['required'],
                'tempat_tujuanEdit' => ['required'],
                'keteranganEdit' => ['required'],
                'id_pengikutEdit.*' => ['exists:karyawans,id_karyawan', 'distinct'],
                'id_pengikutEdit' => ['array'],
                'pengemudiEdit' => ['required','exists:karyawans,id_karyawan']
            ];

            $tanggal_pergi = date('Y-m-d H:i:s', strtotime("$request->jam_pergiEdit"));
            $tanggal_kembali = $request->jam_kembaliEdit ? date('Y-m-d H:i:s', strtotime("$request->jam_kembaliEdit")) : null;
            $jenis_kendaraan = $request->jenis_kendaraanEdit;
            $jenis_kepemilikan = $request->jenis_kepemilikanEdit;
            $jenis_keberangkatan = $request->jenis_keberangkatanEdit;
            $tempat_asal = $request->tempat_asalEdit;
            $tempat_tujuan = $request->tempat_tujuanEdit;
            $keterangan = $request->keteranganEdit;
            $pengemudi = $request->pengemudiEdit;
            $id_pengikut = $request->id_pengikutEdit;
        }

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            //STATIC PARAMS
            $rate = 10000;
            $pembagi = $jenis_kendaraan == 'MOTOR' ? 25 : 10;

            // DINAMIC PARAMS
            // $rate = SettingTugasLuar::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'RATE_BBM')->first()->value;
            // $pembagi = SettingTugasLuar::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'PEMBAGI_'.$jenis_kendaraan)->first()->value;
            $tugasLuar = TugasLuar::findOrFail($id_tugasluar);
            $tugasLuar->tanggal_pergi_planning = $tanggal_pergi;
            $tugasLuar->tanggal_kembali_planning = $tanggal_kembali;
            $tugasLuar->jenis_kendaraan = $jenis_kendaraan;
            $tugasLuar->jenis_kepemilikan = $jenis_kepemilikan;
            $tugasLuar->jenis_keberangkatan = $jenis_keberangkatan;
            $tugasLuar->no_polisi = $no_polisi_formatted ?? null;
            $tugasLuar->km_awal = $km_awal ?? 0;
            $tugasLuar->tempat_asal = $tempat_asal;
            $tugasLuar->tempat_tujuan = $tempat_tujuan;
            $tugasLuar->keterangan = $keterangan;
            $tugasLuar->pengemudi_id = $pengemudi;
            $tugasLuar->pembagi = $pembagi;
            $tugasLuar->rate = $rate;
            $tugasLuar->save();

            $tugasLuar->pengikut()->delete();

            if(!empty($id_pengikut)){
                $pengikuts = Karyawan::whereIn('id_karyawan', $id_pengikut)->get();
                $details = [];

                $details[] = [
                    'karyawan_id' => auth()->user()->karyawan->id_karyawan,
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'departemen_id' => auth()->user()->karyawan->posisi[0]->departemen_id,
                    'divisi_id' => auth()->user()->karyawan->posisi[0]->divisi_id,
                    'ni_karyawan' => auth()->user()->karyawan->ni_karyawan,
                    'pin' => auth()->user()->karyawan->pin,
                    'role' => 'M'
                ];

                foreach ($pengikuts as $pengikut) {
                    $details[] = [
                        'karyawan_id' => $pengikut->id_karyawan,
                        'organisasi_id' => $organisasi_id,
                        'departemen_id' => $pengikut->posisi[0]->departemen_id,
                        'divisi_id' => $pengikut->posisi[0]->divisi_id,
                        'ni_karyawan' => $pengikut->ni_karyawan,
                        'pin' => $pengikut->pin,
                    ];
                }
    
                $tugasLuar->pengikut()->createMany($details);
            } else {
                $tugasLuar->pengikut()->create([
                    'karyawan_id' => auth()->user()->karyawan->id_karyawan,
                    'organisasi_id' => auth()->user()->organisasi_id,
                    'departemen_id' => auth()->user()->karyawan->posisi[0]->departemen_id,
                    'divisi_id' => auth()->user()->karyawan->posisi[0]->divisi_id,
                    'ni_karyawan' => auth()->user()->karyawan->ni_karyawan,
                    'pin' => auth()->user()->karyawan->pin,
                    'role' => 'M'
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Pengajuan Tugas Luar Berhasil Diupdate'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_tugasluar)
    {
        DB::beginTransaction();
        try{
            $tugasluar = TugasLuar::findOrFail($id_tugasluar);
            $tugasluar->pengikut()->delete();
            $tugasluar->delete();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Tugas Luar Dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

     public function verifikasi(Request $request, string $id_tugasluar)
    {

        $dataValidate = [
            'kode_wilayahVerif' => ['required','regex:/^[A-Za-z]+$/'],
            'nomor_polisiVerif' => ['required', 'numeric'],
            'seri_akhirVerif' => ['required', 'regex:/^[A-Za-z]+$/'],
            'kilometerVerif' => ['required', 'numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        
        DB::beginTransaction();
        try{
            $tugasluar = TugasLuar::find($id_tugasluar);
            $km = $request->kilometerVerif;
            if ($tugasluar->rejected_by) {
                DB::rollBack();
                return response()->json(['message' => 'Pengajuan TL yang sudah di reject tidak dapat di Verifikasi!'], 403);
            }

            if($tugasluar->status == 'WAITING') {
                $tugasluar->km_awal = $km;
                if($tugasluar->jenis_keberangkatan !== 'KTR') {
                    $tugasluar->tanggal_pergi_aktual = now();
                    $tugasluar->status = 'ONGOING';
                }
                $tugasluar->no_polisi = $request->kode_wilayahVerif.'-'.$request->nomor_polisiVerif.'-'.$request->seri_akhirVerif;
            } else {
                if ($km < $tugasluar->km_awal) {
                    DB::rollBack();
                    return response()->json(['message' => 'Kilometer akhir harus lebih besar dari kilometer awal!'], 403);
                }
                $tugasluar->km_akhir = $km;
                $tugasluar->km_selisih = $km - $tugasluar->km_awal;
                if($tugasluar->jenis_keberangkatan !== 'KTR') {
                    $tugasluar->tanggal_kembali_aktual = now();
                    $tugasluar->status = 'COMPLETED';
                }
            }
            $tugasluar->save();

            DB::commit();
            return response()->json(['message' => 'TL berhasil di Verifikasi!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function aktual(Request $request, string $id_tugasluar)
    {
        $tugasluar = TugasLuar::find($id_tugasluar);

        DB::beginTransaction();
        try{
            if ($tugasluar->rejected_by) {
                return response()->json(['message' => 'Pengajuan TL yang sudah di reject!'], 403);
            }

            if (!$tugasluar->tanggal_pergi_aktual) {
                $tugasluar->tanggal_pergi_aktual = now();
                $tugasluar->status = 'ONGOING';
            } else {
                $tugasluar->tanggal_kembali_aktual = now();
                $tugasluar->status = 'COMPLETED';
            }
            $tugasluar->save();

            DB::commit();
            return response()->json(['message' => 'TL berhasil di Checked!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
