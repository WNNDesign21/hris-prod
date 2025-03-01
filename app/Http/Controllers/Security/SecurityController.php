<?php

namespace App\Http\Controllers\Security;

use Throwable;
use Carbon\Carbon;
use App\Models\Izine;
use App\Models\Karyawan;
use App\Helpers\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TugasLuare\TugasLuar;
use Illuminate\Support\Facades\Crypt;

class SecurityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Security-E - Log Book",
            'page' => 'security-index',
        ];
        return view('pages.security-e.index', $dataPage);
    }

    public function izin_datatable(Request $request)
    {
        $columns = array(
            0 => 'izins.id_izin',
            1 => 'izins.rencana_mulai_or_masuk',
            2 => 'izins.rencana_selesai_or_keluar',
            3 => 'izins.aktual_mulai_or_masuk',
            4 => 'izins.aktual_selesai_or_keluar',
            5 => 'izins.jenis_izin',
            6 => 'izins.durasi',
            7 => 'izins.keterangan',
            8 => 'izins.checked_by',
            9 => 'izins.approved_by',
            10 => 'izins.legalized_by',
        );

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

        if (auth()->user()->hasRole('security')){
            $dataFilter['is_security'] = 'Y';
            $dataFilter['jenis_izin'] = ['SH', 'KP', 'PL'];
            $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
        }

        $totalData = Izine::count();
        $totalFiltered = $totalData;
        $izine = Izine::getData($dataFilter, $settings);
        $totalFiltered = Izine::countData($dataFilter);
        $dataTable = [];
        

        if (!empty($izine)) {
            $rencana = '-';
            $aktual = '-';

            foreach ($izine as $data) {
                if($data->jenis_izin == 'SH'){
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    if ($data->rencana_mulai_or_masuk){
                        $rencana = Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    } elseif ($data->rencana_selesai_or_keluar){
                        $rencana = Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    }
    
                    if ($data->aktual_mulai_or_masuk){
                        $aktual = Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    } elseif ($data->aktual_selesai_or_keluar){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    }
                } elseif ($data->jenis_izin == 'KP') {
                    $jenis_izin = '<span class="badge badge-light">Keluar Pabrik</span>';
                    if ($data->rencana_selesai_or_keluar && $data->rencana_mulai_or_masuk){
                        $rencana = Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB - '.Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    }
    
                    if ($data->aktual_selesai_or_keluar){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB - UNKNOWN';
                    } 
                    
                    if ($data->aktual_mulai_or_masuk){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB - '.Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    }
                } elseif ($data->jenis_izin == 'PL') {
                    $jenis_izin = '<span class="badge badge-dark">Pulang</span>';
                    if ($data->rencana_selesai_or_keluar){
                        $rencana = Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    } 

                    if ($data->aktual_selesai_or_keluar){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    } 
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['nama'] = $data->nama;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['rencana'] = $rencana;
                $nestedData['aktual'] = $aktual;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['keterangan'] = $data->keterangan;

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
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function tugasluar_datatable(Request $request)
    {
        $columns = array(
            0 => 'tugasluars.id_tugasluar',
            1 => 'tugasluars.karyawan_id',
            2 => 'tugasluars.tanggal',
            3 => 'tugasluars.jenis_kendaraan',
            4 => 'tugasluars.tanggal_pergi_planning',
            5 => 'tugasluars.tanggal_kembali_planning',
            6 => 'tugasluars.km_awal',
            7 => 'tugasluars.km_akhir',
            8 => 'tugasluars.km_selisih',
            9 => 'tugasluars.tempat_asal',
            10 => 'tugasluars.keterangan',
            11 => 'tugasluars.checked_at',
            12 => 'tugasluars.legalized_at',
            13 => 'tugasluars.status',
        );

        $totalData = TugasLuar::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];

        $is_can_checked = false;
        $is_can_legalized = false;
        $is_can_known = false;
        $organisasi_id = auth()->user()->organisasi_id;

        if(auth()->user()->hasRole('security')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $dataFilter['jenis_keberangkatan'] = 'KTR';
        } 

        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $filterNopol = $request->nopol;
        if(isset($filterNopol)){
            $dataFilter['nopol'] = $filterNopol;
        }

        $filterStatus = $request->status;
        if(isset($filterStatus)){
            $dataFilter['status'] = $filterStatus;
        }

        $filterFrom = $request->from;
        if(isset($filterFrom)){
            $dataFilter['from'] = $filterFrom;
        }

        $filterTo = $request->to;
        if(isset($filterTo)){
            $dataFilter['to'] = $filterTo;
        }

        $tugasluars = TugasLuar::getData($dataFilter, $settings);
        $totalFiltered = TugasLuar::countData($dataFilter);

        $dataTable = [];

        if (!empty($tugasluars)) {
            foreach ($tugasluars as $data) {
                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $has_leader = Approval::HasLeader($posisi);
                $has_section_head = Approval::HasSectionHead($posisi);
                $has_department_head = Approval::HasDepartmentHead($posisi);

                $checked = '';
                $legalized = '';
                $known = '';
                $rejected = '';
                $aksi = '-';
                $is_rejected = false;
                $formattedPengikut = '';
                if ($data->pengikut()->exists()) {
                    $pengikuts = $data->pengikut()->pluck('karyawan_id')->toArray();
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
                $rute = '<div class="d-flex gap-1 text-center">'.'<p><small class="text-fade">'.strtoupper($data->tempat_asal).'</small></p>'.' ➡️ '.'<p><small class="text-fade">'.strtoupper($data->tempat_tujuan).'</small></p>'.'</div><div class="d-flex justify-content-center"><p><small> Driver : '.$data->nama_pengemudi.'</small><br><small> From : '.$jenis_keberangkatan_text.'</small></p></div>';
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
                }

                if($data->legalized_by) {
                    $legalized = '✅<br><small class="text-bold">'.$data?->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                }


                // APPROVAL
                if($data->rejected_by) {
                    $is_rejected = true;
                    $rejected = '❌<br><small class="text-bold">'.$data?->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                $nestedData['id_tugasluar'] = $data->id_tugasluar;
                $nestedData['karyawan'] = $formattedPengikut;
                $nestedData['tanggal'] = Carbon::parse($data->tanggal)->format('d M Y');
                $nestedData['kendaraan'] = $kendaraan;
                $nestedData['pergi'] = $jam_pergi;
                $nestedData['kembali'] = $jam_kembali;
                $nestedData['km_awal'] = $data->km_awal . ' Km';
                $nestedData['km_akhir'] = $data->km_akhir . ' Km';
                $nestedData['km_selisih'] = $data->km_selisih . ' Km';
                $nestedData['rute'] = $rute;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['status'] = $status;
                $nestedData['checked'] = $is_rejected ? $rejected : $checked;
                $nestedData['legalized'] = $is_rejected ? $rejected : $legalized;

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

    public function get_qr_detail(string $id)
    {
        try {
            $decrypt_id = Crypt::decryptString($id);
            $realId = gzuncompress($decrypt_id);
            $type = substr($realId, 0, 2);
            if ($type == 'IZ') {
                $izin = Izine::find($realId);
                $html = '<div class="alert alert-primary text-white" style="text-align:start;" role="alert">'
                    .'<p style="text-align:start;"><strong>Tipe</strong> : IZIN</p>'
                    .'<p style="text-align:start;"><strong>ID Izin</strong> : '.$izin->id_izin.'</p>'
                    .'<p style="text-align:start;"><strong>Nama</strong> : '.$izin->nama.'</p>'
                    .'<p style="text-align:start;"><strong>Departemen</strong> : '.$izin->departemen.'</p>'
                    .'<p style="text-align:start;"><strong>Jenis Izin</strong> : '.$izin->jenis_izin.'</p>'
                    .'<p style="text-align:start;"><strong>Rencana</strong> : '.($izin->rencana_mulai_or_masuk ? Carbon::parse($izin->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-').'</p>'
                    .'<p style="text-align:start;"><strong>Keterangan</strong> : '.$izin->keterangan.'</p>'
                    .'</div>';
                return response()->json(['message' => 'Data retrieved successfully', 'html' => $html], 200);
            } elseif ($type == 'TL') {
                $tugasluar = TugasLuar::find($realId);
                $html = '<div class="alert alert-primary text-white" style="text-align:start;" role="alert">'
                    .'<p style="text-align:start;"><strong>Tipe</strong> : TUGAS LUAR</p>'
                    .'<p style="text-align:start;"><strong>ID TL</strong> : '.$tugasluar->id_tugasluar.'</p>'
                    .'<p style="text-align:start;"><strong>Pengemudi</strong> : '.Karyawan::find($tugasluar->pengemudi_id)->nama.'</p>'
                    .'<p style="text-align:start;"><strong>Nomor Polisi</strong> : '.$tugasluar->no_polisi.'</p>'
                    .'<p style="text-align:start;"><strong>Tempat Asal</strong> : '.$tugasluar->tempat_asal.'</p>'
                    .'<p style="text-align:start;"><strong>Tempat Tujuan</strong> : '.$tugasluar->tempat_tujuan.'</p>'
                    .'<p style="text-align:start;"><strong>Rencana Pergi</strong> : '.($tugasluar->tanggal_pergi_planning ? Carbon::parse($tugasluar->tanggal_pergi_planning)->format('H:i').' WIB' : '-').'</p>'
                    .'<p style="text-align:start;"><strong>Rencana Kembali</strong> : '.($tugasluar->tanggal_kembali_planning ? Carbon::parse($tugasluar->tanggal_kembali_planning)->format('H:i').' WIB' : '-').'</p>'
                    .'<p style="text-align:start;"><strong>Aktual Pergi</strong> : '.($tugasluar->tanggal_pergi_aktual ? Carbon::parse($tugasluar->tanggal_pergi_aktual)->format('H:i').' WIB' : '-').'</p>'
                    .'<p style="text-align:start;"><strong>Aktual Kembali</strong> : '.($tugasluar->tanggal_kembali_aktual ? Carbon::parse($tugasluar->tanggal_kembali_aktual)->format('H:i').' WIB' : '-').'</p>'
                    .'</div>';
                return response()->json(['message' => 'Data retrieved successfully', 'html' => $html, 'data' => $realId], 200);
            } else {
                return response()->json(['message' => 'Invalid QR Code'], 404);
            }
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }

    public function confirmed(Request $request, string $id)
    {
        $type = substr($id, 0, 2);
        try {
            if ($type == 'IZ') {
                $izin = Izine::find($id);
                if(!auth()->user()->hasRole('security')){
                    return response()->json(['message' => 'Anda tidak memiliki akses untuk melakukan konfirmasi!'], 403);
                }
    
                if ($izin->jenis_izin == 'SH') {
                    if ($izin->aktual_mulai_or_masuk || $izin->aktual_selesai_or_keluar) {
                        return response()->json(['message' => 'Data izin sudah di konfirmasi, silahkan reload halaman!'], 403);
                    } 
        
                    if($izin->rencana_mulai_or_masuk){
                        $izin->aktual_mulai_or_masuk = now();
                    } elseif ($izin->rencana_selesai_or_keluar){
                        $izin->aktual_selesai_or_keluar = now();
                    }
                } elseif ($izin->jenis_izin == 'KP') {
                    if ($izin->aktual_mulai_or_masuk) {
                        return response()->json(['message' => 'Data izin sudah di konfirmasi, silahkan reload halaman!'], 403);
                    } 
                    
                    if ($izin->aktual_selesai_or_keluar) {
                        $izin->aktual_mulai_or_masuk = now();
                    } else {
                        $izin->aktual_selesai_or_keluar = now();
                    }
                } elseif ($izin->jenis_izin == 'PL') {
                    if ($izin->aktual_selesai_or_keluar) {
                        return response()->json(['message' => 'Data izin sudah di konfirmasi, silahkan reload halaman!'], 403);
                    } 
        
                    $izin->aktual_selesai_or_keluar = now();
                } else {
                    return response()->json(['message' => 'Jenis izin tidak ditemukan'], 404);
                }
    
                $izin->save();
                $message = 'Izin berhasil di Konfirmasi!';
                $type = 'IZ';
            } else {
                $tugasluar = TugasLuar::find($id);
                if(!auth()->user()->hasRole('security')){
                    return response()->json(['message' => 'Anda tidak memiliki akses untuk melakukan konfirmasi!'], 403);
                }
    
                if($tugasluar->status == 'REJECTED'){
                    return response()->json(['message' => 'Tugas Luar sudah di Reject!'], 403);
                }
    
                if ($tugasluar->status == 'WAITING'){
                    $tugasluar->status = 'ONGOING';
                    $tugasluar->tanggal_pergi_aktual = now();
                } elseif ($tugasluar->status == 'ONGOING'){
                    $tugasluar->status = 'COMPLETED';
                    $tugasluar->tanggal_kembali_aktual = now();
                } else {
                    return response()->json(['message' => 'Status Tugas Luar tidak ditemukan'], 404);
                }
                $tugasluar->save();
                $message = 'Tugas Luar berhasil di Konfirmasi!';
                $type = 'TL';
            }
            DB::commit();
            return response()->json(['message' => $message, 'data' => $type], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
