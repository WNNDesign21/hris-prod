<?php

namespace App\Http\Controllers\Sakite;

use Throwable;
use Carbon\Carbon;
use App\Models\Sakite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SakiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function lapor_skd_view()
    {
        $dataPage = [
            'pageTitle' => "Izin-E - Lapor SKD",
            'page' => 'izine-lapor-skd',
        ];
        return view('pages.izin-e.lapor-skd', $dataPage);
    }

    public function approval_skd_view()
    {
        $dataPage = [
            'pageTitle' => "Izin-E - Approval SKD",
            'page' => 'izine-approval-skd',
        ];
        return view('pages.izin-e.approval-skd', $dataPage);
    }

    public function lapor_skd_datatable(Request $request)
    {

        $columns = array(
            0 => 'sakits.tanggal_mulai',
            1 => 'sakits.tanggal_selesai',
            2 => 'sakits.durasi',
            3 => 'sakits.keterangan',
            4 => 'sakits.approved_by',
            5 => 'sakits.legalized_at',
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

        $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;

        $totalData = Sakite::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $sakite = Sakite::getData($dataFilter, $settings);
        $totalFiltered = Sakite::countData($dataFilter);
        $dataTable = [];

        if (!empty($sakite)) {
            foreach ($sakite as $data) {
                $durasi = $data->tanggal_selesai ? $data->durasi.' Hari' : '-';
                $tanggal_mulai = $data->tanggal_mulai ? Carbon::parse($data->tanggal_mulai)->format('d M Y') : '-';
                $tanggal_selesai = $data->tanggal_selesai ? Carbon::parse($data->tanggal_selesai)->format('d M Y') : '-';

                if($data->attachment){
                    $lampiran = '<a id="linkFoto'.$data->id_sakit.'" href="' . asset('storage/'.$data->attachment) . '"
                                    class="image-popup-vertical-fit" data-title="Lampiran SKD">
                                    <img id="imageReview'.$data->id_sakit.'" src="' . asset('storage/'.$data->attachment) . '" alt="Image Foto"
                                        style="width: 150px;height: 150px;" class="img-fluid">
                                </a>';
                } else {
                    $lampiran = 'Need Upload';
                }

                //Kondisi tombol aksi
                if ($data->attachment && $data->approved_by) {
                    $aksi = '-';
                } else {
                    $aksi = '<div class="btn-group">
                                <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id-sakit="'.$data->id_sakit.'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id-sakit="'.$data->id_sakit.'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>';
                }

                $nestedData['tanggal_mulai'] = $tanggal_mulai;
                $nestedData['tanggal_selesai'] = $tanggal_selesai;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['durasi'] = $durasi;
                $nestedData['lampiran'] = $lampiran;
                $nestedData['approved_by'] = $data->approved_by;
                $nestedData['legalized_by'] = $data->legalized_by;
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
            "statusFilter" => !empty($dataFilter['statusFilter']) ? $dataFilter['statusFilter'] : "Kosong",
            "dir" => $dir,
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function approval_skd_datatable(Request $request)
    {

        $columns = array(
            0 => 'karyawans.nama',
            1 => 'departemens.nama',
            2 => 'posisis.nama',
            3 => 'sakits.tanggal_mulai',
            4 => 'sakits.tanggal_selesai',
            5 => 'sakits.durasi',
            6 => 'sakits.keterangan',
            7 => 'sakits.attachment',
            8 => 'sakits.approved_by',
            9 => 'sakits.legalized_by',
        );

        $totalData = Sakite::count();
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

        $is_can_approved = false;
        $is_can_legalized = false;
        $organisasi_id = auth()->user()->organisasi_id;

        // FILTER PERSONALIA
        if(auth()->user()->hasRole('personalia')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_legalized = true;
        } 

        //FILTER MEMBER
        if (auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = $this->get_member_posisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id >= 4){
                $is_can_checked = true;
            } 

            if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 4){
                $is_can_approved = true;
            }

            $dataFilter['member_posisi_id'] = $id_posisi_members;
        } 

        $izine = Sakite::getData($dataFilter, $settings);
        $totalFiltered = Sakite::countData($dataFilter);

        $dataTable = [];

        if (!empty($izine)) {
            foreach ($izine as $data) {
                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $has_leader = $this->has_leader($posisi);
                $has_section_head = $this->has_section_head($posisi);
                $has_department_head = $this->has_department_head($posisi);
                $legalized_by = 'Need Legalized';
                $approved_by = 'Need Approved';
                $checked_by = 'Need Checked';
                $karyawan_pengganti = '-';

                //JENIS IZIN
                if ($data->jenis_izin == 'TM') {
                    $jenis_izin = '<span class="badge badge-primary">Tidak Masuk</span>';
                    $durasi = $data->durasi . ' Hari';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y') : '-';
                } else {
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                }
        

                //TOMBOL APPROVED
                if ($is_can_approved){
                    $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;

                    //KONDISI UNTUK SECTION HEAD
                    if($has_leader && $my_posisi == 4){
                        if($data->checked_by && !$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    }

                    if(!$has_leader && $my_posisi == 4){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    }

                    //KONDISI UNTUK DEPT HEAD
                    if($has_leader && !$has_section_head && $my_posisi == 3){
                        if($data->checked_by && !$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    }

                    if(!$has_leader && !$has_section_head && $my_posisi == 3){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    }

                    //KONDISI UNTUK DIV / PLANT HEAD
                    if(!$has_leader && !$has_section_head && !$has_department_head){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    }
                }

                //TOMBOL LEGALIZED
                if ($is_can_legalized){
                    if(!$data->legalized_by){
                        $legalized_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                    }
                }

                //REJECTED
                if ($data->rejected_by){
                    $checked_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    $approved_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    $legalized_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['nama'] = $data->nama;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['rencana_mulai_or_masuk'] = $rencana_mulai_or_masuk;
                $nestedData['rencana_selesai_or_keluar'] = $rencana_selesai_or_keluar;
                $nestedData['aktual_mulai_or_masuk'] = $aktual_mulai_or_masuk;
                $nestedData['aktual_selesai_or_keluar'] = $aktual_selesai_or_keluar;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['durasi'] = $durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $checked_by;
                $nestedData['approved_by'] = $approved_by;
                $nestedData['legalized_by'] = $legalized_by;

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

        if($request->hasFile('lampiran_skd')){
            $dataValidate = [
                'lampiran_skd' => ['mimes:jpg,png,jpeg', 'max:2048'],
                'tanggal_mulai' => ['required', 'date_format:Y-m-d', 'before_or_equal:tanggal_selesai'],
                'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            ];
        } else {
            $dataValidate = [
                'lampiran_skd' => ['mimes:jpg,png,jpeg', 'max:2048'],
                'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
                'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
                'keterangan' => ['required'],
            ];
        }

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $lampiran_skd = $request->file('lampiran_skd');
            $tanggal_mulai = $request->tanggal_mulai;
            $tanggal_selesai = $request->tanggal_selesai;
            $keterangan = $request->keterangan;
            $karyawan_id = auth()->user()->karyawan->id_karyawan;
            $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;
            $divisi_id = auth()->user()->karyawan->posisi[0]->divisi_id;
            $organisasi_id = auth()->user()->organisasi_id;
            $durasi = $tanggal_selesai ? Carbon::parse($tanggal_mulai)->diffInDays(Carbon::parse($tanggal_selesai)) + 1 : 0;

            if ($request->hasFile('lampiran_skd')) {
                $fileName = 'SKD-'.Str::random(5).'-'.date('YmdHis').'.'.$lampiran_skd->getClientOriginalExtension();
                $file_path = $lampiran_skd->storeAs("attachment/skd", $fileName);

                $sakit = Sakite::create([
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $departemen_id,
                    'divisi_id' => $divisi_id,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,  
                    'keterangan' => $keterangan,    
                    'attachment' => $file_path,
                    'durasi' => $durasi,
                ]);
            } else {
                $sakit = Sakite::create([
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $departemen_id,
                    'divisi_id' => $divisi_id,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,  
                    'keterangan' => $keterangan,  
                    'durasi' => $durasi,  
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Lapor SKD berhasil dibuat'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
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
    public function update(Request $request, string $id_sakit)
    {
        if($request->hasFile('lampiran_skdEdit')){
            $dataValidate = [
                'lampiran_skdEdit' => ['mimes:jpg,png,jpeg', 'max:2048'],
                'tanggal_mulaiEdit' => ['required', 'date_format:Y-m-d', 'before_or_equal:tanggal_selesaiEdit'],
                'tanggal_selesaiEdit' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulaiEdit'],
            ];
        } else {
            $dataValidate = [
                'lampiran_skdEdit' => ['mimes:jpg,png,jpeg', 'max:2048'],
                'tanggal_mulaiEdit' => ['required', 'date_format:Y-m-d'],
                'tanggal_selesaiEdit' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulaiEdit'],
                'keteranganEdit' => ['required'],
            ];
        }

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $lampiran_skd = $request->file('lampiran_skdEdit');
            $tanggal_mulai = $request->tanggal_mulaiEdit;
            $tanggal_selesai = $request->tanggal_selesaiEdit;
            $keterangan = $request->keteranganEdit;
            $durasi = $tanggal_selesai ? Carbon::parse($tanggal_mulai)->diffInDays(Carbon::parse($tanggal_selesai)) + 1 : 0;
            $sakit = Sakite::find($id_sakit);

            if ($request->hasFile('lampiran_skdEdit')) {
                $fileName = 'SKD-'.Str::random(5).'-'.date('YmdHis').'.'.$lampiran_skd->getClientOriginalExtension();
                $file_path = $lampiran_skd->storeAs("attachment/skd", $fileName);

                if ($sakit->attachment) {
                    Storage::delete($sakit->attachment);
                }

                $sakit->update([
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,  
                    'keterangan' => $keterangan,    
                    'attachment' => $file_path,
                    'durasi' => $durasi,
                ]);
            } else {
                if(!$tanggal_selesai){
                    if($sakit->attachment){
                        Storage::delete($sakit->attachment);
                        $sakit->update([
                            'tanggal_mulai' => $tanggal_mulai,
                            'tanggal_selesai' => $tanggal_selesai,  
                            'keterangan' => $keterangan,  
                            'attachment' => null,
                            'durasi' => $durasi,
                        ]);
                    } else {
                        $sakit->update([
                            'tanggal_mulai' => $tanggal_mulai,
                            'tanggal_selesai' => $tanggal_selesai,  
                            'keterangan' => $keterangan,  
                            'durasi' => $durasi,  
                        ]);
                    }
                } else {
                    if($sakit->attachment){
                        $sakit->update([
                            'tanggal_mulai' => $tanggal_mulai,
                            'tanggal_selesai' => $tanggal_selesai,  
                            'keterangan' => $keterangan,  
                            'durasi' => $durasi,  
                        ]);
                    } else {
                        DB::rollBack();
                        return response()->json(['message' => 'Lampirkan SKD jika tanggal selesai sudah ada!'], 402);
                    }
                } 
            }

            DB::commit();
            return response()->json(['message' => 'Lapor SKD berhasil diupdate!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function get_data_sakit(string $id_sakit)
    {
        $sakit = Sakite::find($id_sakit);
        $data = [];

        try {

            if ($sakit) {
                $data['id_sakit'] = $sakit->id_sakit;
                $data['tanggal_mulai'] = $sakit->tanggal_mulai;
                $data['tanggal_selesai'] = $sakit->tanggal_selesai;
                $data['keterangan'] = $sakit->keterangan;
                $data['attachment'] = $sakit->attachment ? asset('storage/'.$sakit->attachment) : asset('img/no-image.png');
            } else {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
            return response()->json(['message' => 'Data berhasil ditemukan', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(string $id_sakit)
    {
        DB::beginTransaction();
        try {
            $sakit = Sakite::find($id_sakit);
            if ($sakit->attachment) {
                Storage::delete($sakit->attachment);
            }
            $sakit->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
