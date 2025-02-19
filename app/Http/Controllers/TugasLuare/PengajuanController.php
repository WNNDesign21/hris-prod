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
            return redirect()->route('root');
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
            'jam_keluar' => ['required', 'date_format:H:i'],
            'jenis_kendaraan' => ['required','in:MOTOR,MOBIL'],
            'kepemilikan_kendaraan' => ['required','in:OP,OJ,PR'],
            'kode_wilayah' => ['required', 'regex:/^[A-Za-z]+$/'],
            'nomor_polisi' => ['required','numeric'],
            'seri_akhir' => ['required','regex:/^[A-Za-z]+$/'],
            'tempat_asal' => ['required'],
            'tempat_tujuan' => ['required'],
            'keterangan' => ['required'],
            'id_pengikut.*' => ['required','exists:karyawans,id_karyawan', 'distinct'],
            'id_pengikut' => ['required','array'],
            'pengemudi' => ['required','exists:karyawans,id_karyawan']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $tanggal_pergi = date('Y-m-d H:i:s', strtotime("$request->jam_keluar"));
        $jenis_kendaraan = $request->jenis_kendaraan;
        $kepemilikan_kendaraan = $request->kepemilikan_kendaraan;
        $kode_wilayah = strtoupper(trim($request->kode_wilayah));
        $no_polisi = $request->nomor_polisi;
        $seri_akhir = strtoupper(trim($request->seri_akhir));
        $no_polisi_formatted = $kode_wilayah.'-'.$no_polisi.'-'.$seri_akhir;
        $tempat_asal = $request->tempat_asal;
        $tempat_tujuan = $request->tempat_tujuan;
        $keterangan = $request->keterangan;
        $pengemudi = $request->pengemudi;
        $id_pengikut = $request->id_pengikut;

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
                'tanggal_pergi' => $tanggal_pergi,
                'jenis_kendaraan' => $jenis_kendaraan,
                'kepemilikan_kendaraan' => $kepemilikan_kendaraan,
                'no_polisi' => $no_polisi_formatted,
                'tempat_asal' => $tempat_asal,
                'tempat_tujuan' => $tempat_tujuan,
                'keterangan' => $keterangan,
                'pengemudi_id' => $pengemudi,
                'pembagi' => $pembagi,
                'rate' => $rate,
            ]);

            $pengikuts = Karyawan::whereIn('id_karyawan', $id_pengikut)->get();
            $data_pengikut = [];
            foreach ($pengikuts as $pengikut) {
                $data_pengikut[] = [
                    'karyawan_id' => $pengikut->id_karyawan,
                    'organisasi_id' => $pengikut->organisasi_id,
                    'departemen_id' => $pengikut->posisi[0]->departemen_id,
                    'divisi_id' => $pengikut->posisi[0]->divisi_id,
                    'ni_karyawan' => $pengikut->ni_karyawan,
                    'pin' => $pengikut->pin,
                ];
            }

            $tugasLuar->pengikut()->createMany($data_pengikut);

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
            1 => 'tugasluars.created_date',
            2 => 'tugasluars.jenis_kendaraan',
            3 => 'tugasluars.tanggal_pergi',
            4 => 'tugasluars.tanggal_kembali',
            5 => 'tugasluars.jarak_tempuh',
            6 => 'tugasluars.tempat_asal',
            7 => 'tugasluars.keterangan',
            8 => 'tugasluars.checked_at',
            9 => 'tugasluars.legalized_at',
            10 => 'tugasluars.known_at',
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
                $pengikuts = $data->pengikut()->pluck('karyawan_id')->toArray();
                $pengikutNames = Karyawan::whereIn('id_karyawan', $pengikuts)->pluck('nama')->toArray();
                $formattedPengikut = array_map(function($pengikut) {
                    return '<span class="badge badge-primary m-1">' . $pengikut . '</span>';
                }, $pengikutNames);
                $no_polisi_array = explode('-', $data->no_polisi);
                $kode_wilayah = $no_polisi_array[0];
                $nomor_polisi = $no_polisi_array[1];
                $seri_akhir = $no_polisi_array[2];
                $kepemilikan_kendaraan = $data->kepemilikan_kendaraan == 'OP' ? 'OPERASIONAL' : ($data->kepemilikan_kendaraan == 'OJ' ? 'OPERASIONAL JABATAN' : 'PRIBADI');
                $jenis_kendaraan = $data->jenis_kendaraan == 'MOTOR' ? '🏍️' : '🚗';
                $kendaraan = '<small class="text-center">'.$jenis_kendaraan.' '.$data->no_polisi.'<br><span class="text-center">'.$kepemilikan_kendaraan.'</span></small>';
                $rute = '<div class="d-flex gap-1 text-center">'.'<p><small class="text-fade">'.strtoupper($data->tempat_asal).'</small></p>'.' ➡️ '.'<p><small class="text-fade">'.strtoupper($data->tempat_tujuan).'</small></p>'.'</div>';
                $status = $data->status == 'WAITING' ? '<span class="badge badge-warning">WAITING</span>' : ($data->status == 'ONGOING' ? '<span class="badge badge-info">ON GOING</span>' : ($data->status == 'COMPLETED' ? '<span class="badge badge-success">COMPLETED</span>' : '<span class="badge badge-danger">REJECTED</span>'));

                if($data->checked_by) {
                    $checked = '✅<br><small class="text-bold">'.$data?->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                } else {
                    $checked = 'NEED CHECKED';
                    $aksi = '<div class="btn-group">
                        <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" 
                            data-id-tugasluar="'.$data->id_tugasluar.'" 
                            data-jam-keluar="'.Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal_pergi)->format('H:i').'" 
                            data-jenis-kendaraan="'.$data->jenis_kendaraan.'" 
                            data-kepemilikan-kendaraan="'.$data->kepemilikan_kendaraan.'" 
                            data-kode-wilayah="'.$kode_wilayah.'" 
                            data-nomor-polisi="'.$nomor_polisi.'" 
                            data-seri-akhir="'.$seri_akhir.'" 
                            data-tempat-asal="'.$data->tempat_asal.'" 
                            data-tempat-tujuan="'.$data->tempat_tujuan.'" 
                            data-keterangan="'.$data->keterangan.'"
                            data-pengemudi="'.$data->pengemudi_id.'"
                            >
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id-tugasluar="'.$data->id_tugasluar.'"><i class="fas fa-trash-alt"></i></button>
                    </div>';
                }

                if($data->legalized_by) {
                    $legalized = '✅<br><small class="text-bold">'.$data?->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                } else {
                    $legalized = 'NEED LEGALIZED';
                }

                if($data->known_by) {
                    $known = '✅<br><small class="text-bold">'.$data?->known_by.'</small><br><small class="text-fade">'.Carbon::parse($data->known_by)->diffForHumans().'</small>';
                } else {
                    $known = 'NEED KNOWN BY SECURITY';
                }

                if($data->rejected_by) {
                    $is_rejected = true;
                    $rejected = '❌<br><small class="text-bold">'.$data?->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                $nestedData['id_tugasluar'] = $data->id_tugasluar;
                $nestedData['tanggal'] = Carbon::parse($data->created_date)->format('d M Y');
                $nestedData['kendaraan'] = $kendaraan;
                $nestedData['pergi'] = Carbon::parse($data->tanggal_pergi)->format('H:i').' WIB';
                $nestedData['kembali'] = $data->tanggal_kembali ? Carbon::parse($data->tanggal_kembali)->format('H:i').' WIB' : '-';
                $nestedData['jarak'] = $data?->jarak_tempuh ? $data->jarak_tempuh.' KM' : '-';
                $nestedData['rute'] = $rute;
                $nestedData['pengikut'] = $formattedPengikut;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked'] = $is_rejected ? $rejected : $checked;
                $nestedData['legalized'] = $is_rejected ? $rejected : $legalized;
                $nestedData['known'] = $is_rejected ? $rejected : $known;
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
        $dataValidate = [
            'jam_keluarEdit' => ['required', 'date_format:H:i'],
            'jenis_kendaraanEdit' => ['required','in:MOTOR,MOBIL'],
            'kepemilikan_kendaraanEdit' => ['required','in:OP,OJ,PR'],
            'kode_wilayahEdit' => ['required', 'regex:/^[A-Za-z]+$/'],
            'nomor_polisiEdit' => ['required','numeric'],
            'seri_akhirEdit' => ['required','regex:/^[A-Za-z]+$/'],
            'tempat_asalEdit' => ['required'],
            'tempat_tujuanEdit' => ['required'],
            'keteranganEdit' => ['required'],
            'id_pengikutEdit.*' => ['required','exists:karyawans,id_karyawan', 'distinct'],
            'id_pengikutEdit' => ['required','array'],
            'pengemudiEdit' => ['required','exists:karyawans,id_karyawan']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $tanggal_pergi = date('Y-m-d H:i:s', strtotime("$request->jam_keluarEdit"));
        $jenis_kendaraan = $request->jenis_kendaraanEdit;
        $kepemilikan_kendaraan = $request->kepemilikan_kendaraanEdit;
        $kode_wilayah = strtoupper(trim($request->kode_wilayahEdit));
        $no_polisi = $request->nomor_polisiEdit;
        $seri_akhir = strtoupper(trim($request->seri_akhirEdit));
        $no_polisi_formatted = $kode_wilayah.'-'.$no_polisi.'-'.$seri_akhir;
        $tempat_asal = $request->tempat_asalEdit;
        $tempat_tujuan = $request->tempat_tujuanEdit;
        $keterangan = $request->keteranganEdit;
        $pengemudi = $request->pengemudiEdit;
        $id_pengikut = $request->id_pengikutEdit;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try {
            //STATIC PARAMS
            $rate = 10000;
            $pembagi = $jenis_kendaraan == 'MOTOR' ? 25 : 10;

            // DINAMIC PARAMS
            // $rate = SettingTugasLuar::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'RATE_BBM')->first()->value;
            // $pembagi = SettingTugasLuar::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'PEMBAGI_'.$jenis_kendaraan)->first()->value;
            $tugasLuar = TugasLuar::findOrFail($id_tugasluar);
            $tugasLuar->tanggal_pergi = $tanggal_pergi;
            $tugasLuar->jenis_kendaraan = $jenis_kendaraan;
            $tugasLuar->kepemilikan_kendaraan = $kepemilikan_kendaraan;
            $tugasLuar->no_polisi = $no_polisi_formatted;
            $tugasLuar->tempat_asal = $tempat_asal;
            $tugasLuar->tempat_tujuan = $tempat_tujuan;
            $tugasLuar->keterangan = $keterangan;
            $tugasLuar->pengemudi_id = $pengemudi;
            $tugasLuar->pembagi = $pembagi;
            $tugasLuar->rate = $rate;
            $tugasLuar->save();

            $tugasLuar->pengikut()->delete();

            $pengikuts = Karyawan::whereIn('id_karyawan', $id_pengikut)->get();
            $data_pengikut = [];
            foreach ($pengikuts as $pengikut) {
                $data_pengikut[] = [
                    'karyawan_id' => $pengikut->id_karyawan,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $pengikut->posisi[0]->departemen_id,
                    'divisi_id' => $pengikut->posisi[0]->divisi_id,
                    'ni_karyawan' => $pengikut->ni_karyawan,
                    'pin' => $pengikut->pin,
                ];
            }

            $tugasLuar->pengikut()->createMany($data_pengikut);

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
}
