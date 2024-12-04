<?php

namespace App\Http\Controllers\Izine;

use Throwable;
use Carbon\Carbon;
use App\Models\Izine;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class IzineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function pengajuan_izin_view()
    {
        $dataPage = [
            'pageTitle' => "Izin-E - Pengajuan Izin",
            'page' => 'izine-pengajuan-izin',
        ];
        return view('pages.izin-e.pengajuan-izin', $dataPage);
    }

    public function pengajuan_izin_datatable(Request $request)
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

        $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;
        $dataFilter['jenis_izin'] = ['TM', 'SH'];

        $totalData = Izine::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $izine = Izine::getData($dataFilter, $settings);
        $totalFiltered = Izine::countData($dataFilter);
        $dataTable = [];
        

        if (!empty($izine)) {
            foreach ($izine as $data) {
                if ($data->jenis_izin == 'TM') {
                    $jenis_izin = '<span class="badge badge-primary">Tidak Masuk</span>';
                    $durasi = $data->durasi . ' Hari';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y') : '-';

                    $aksi = '-';
                    if($data->checked_by && $data->approved_by && $data->legalized_by && !$data->rejected_by){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-success btnDone" data-id-izin="'.$data->id_izin.'"><i class="fas fa-check"></i> Done</button><button class="btn btn-sm btn-danger btnDelete" data-id-izin="'.$data->id_izin.'"><i class="fas fa-trash"></i> Delete</button></div>';
                    } 
                    
                    if((!$data->checked_by || !$data->approved_by || !$data->legalized_by) && !$data->rejected_by){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-warning btnEdit" data-id-izin="'.$data->id_izin.'"><i class="fas fa-edit"></i> Edit</button><button class="btn btn-sm btn-danger btnDelete" data-id-izin="'.$data->id_izin.'"><i class="fas fa-trash"></i> Delete</button></div>';
                    }

                } else {
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';

                    $aksi = '-';
                    if((!$data->checked_by || !$data->approved_by || !$data->legalized_by) && !$data->rejected_by){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-warning btnEdit" data-id-izin="'.$data->id_izin.'"><i class="fas fa-edit"></i> Edit</button><button class="btn btn-sm btn-danger btnDelete" data-id-izin="'.$data->id_izin.'"><i class="fas fa-trash"></i> Delete</button></div>';
                    }

                    if($data->checked_by && $data->approved_by && $data->legalized_by){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-primary btnShowQR"><i class="fas fa-qrcode"></i> Show QR</button><button class="btn btn-sm btn-danger btnDelete" data-id-izin="'.$data->id_izin.'"><i class="fas fa-trash"></i> Delete</button></div>';
                    }

                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['rencana_mulai_or_masuk'] = $rencana_mulai_or_masuk;
                $nestedData['rencana_selesai_or_keluar'] = $rencana_selesai_or_keluar;
                $nestedData['aktual_mulai_or_masuk'] = $aktual_mulai_or_masuk;
                $nestedData['aktual_selesai_or_keluar'] = $aktual_selesai_or_keluar;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['durasi'] = $durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $data->checked_by;
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
        $jenis_izin = $request->jenis_izin;
        $keterangan = $request->keterangan;
        
        //IZIN TIDAK MASUK
        $rencana_mulai_or_masuk = $request->rencana_mulai_or_masuk;
        $rencana_selesai_or_keluar = $request->rencana_selesai_or_keluar;

        //IZIN SETENGAH HARI
        $rencana_masuk_or_keluar = $request->rencana_masuk_or_keluar;
        $masuk_or_keluar = $request->masuk_or_keluar;
        if($jenis_izin == 'TM'){
            $dataValidate = [
                'jenis_izin' => ['required'],
                'keterangan' => ['required'],
                'rencana_mulai_or_masuk' => ['required', 'date_format:Y-m-d', 'before_or_equal:rencana_selesai_or_keluar', 'after_or_equal:today'],
                'rencana_selesai_or_keluar' => ['required', 'date_format:Y-m-d', 'after_or_equal:rencana_mulai_or_masuk'],
            ];
        } else {
            $dataValidate = [
                'jenis_izin' => ['required'],
                'keterangan' => ['required'],
                'masuk_or_keluar' => ['required', 'in:M,K'],
                'rencana_masuk_or_keluar' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            ];
        }

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try{
            $karyawan_id = auth()->user()->karyawan->id_karyawan;
            $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;
            $divisi_id = auth()->user()->karyawan->posisi[0]->divisi_id;
            $organisasi_id = auth()->user()->organisasi_id;

            if ($jenis_izin == 'TM'){
                $izin = Izine::create([
                    'id_izin' => 'IZIN-'.$jenis_izin.'-'. Str::random(4).'-'. date('YmdHis'),
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $departemen_id,
                    'divisi_id' => $divisi_id,
                    'jenis_izin' => $jenis_izin,
                    'durasi' => Carbon::parse($rencana_mulai_or_masuk)->diffInDays(Carbon::parse($rencana_selesai_or_keluar)) + 1,
                    'rencana_mulai_or_masuk' => $rencana_mulai_or_masuk,
                    'rencana_selesai_or_keluar' => $rencana_selesai_or_keluar,
                    'keterangan' => $keterangan
                ]);
            } else {
                if($masuk_or_keluar == 'M'){
                    $izin = Izine::create([
                        'id_izin' => 'IZIN-'.$jenis_izin.'-'. Str::random(4).'-'. date('YmdHis'),
                        'karyawan_id' => $karyawan_id,
                        'organisasi_id' => $organisasi_id,
                        'departemen_id' => $departemen_id,
                        'divisi_id' => $divisi_id,
                        'jenis_izin' => $jenis_izin,
                        'rencana_mulai_or_masuk' => $rencana_masuk_or_keluar,
                        'keterangan' => $keterangan
                    ]);
                } else {
                    $izin = Izine::create([
                        'id_izin' => 'IZIN-'.$jenis_izin.'-'. Str::random(4).'-'. date('YmdHis'),
                        'karyawan_id' => $karyawan_id,
                        'organisasi_id' => $organisasi_id,
                        'departemen_id' => $departemen_id,
                        'divisi_id' => $divisi_id,
                        'jenis_izin' => $jenis_izin,
                        'rencana_selesai_or_keluar' => $rencana_masuk_or_keluar,
                        'keterangan' => $keterangan
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Izin berhasil diajukan'], 200);
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
    public function update(Request $request, string $id_izin)
    {
        $izin = Izine::find($id_izin);
        $keterangan = $request->keteranganEdit;
        
        //IZIN TIDAK MASUK
        $rencana_mulai_or_masuk = $request->rencana_mulai_or_masukEdit;
        $rencana_selesai_or_keluar = $request->rencana_selesai_or_keluarEdit;

        //IZIN SETENGAH HARI
        $rencana_masuk_or_keluar = $request->rencana_masuk_or_keluarEdit;
        $masuk_or_keluar = $request->masuk_or_keluarEdit;
        if($izin->jenis_izin == 'TM'){
            $dataValidate = [
                'keteranganEdit' => ['required'],
                'rencana_mulai_or_masukEdit' => ['required', 'date_format:Y-m-d', 'before_or_equal:rencana_selesai_or_keluarEdit', 'after_or_equal:today'],
                'rencana_selesai_or_keluarEdit' => ['required', 'date_format:Y-m-d', 'after_or_equal:rencana_mulai_or_masukEdit'],
            ];
        } else {
            $dataValidate = [
                'keteranganEdit' => ['required'],
                'masuk_or_keluarEdit' => ['required', 'in:M,K'],
                'rencana_masuk_or_keluarEdit' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            ];
        }

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try{
            if ($izin->jenis_izin == 'TM'){
                $izin->update([
                    'durasi' => Carbon::parse($rencana_mulai_or_masuk)->diffInDays(Carbon::parse($rencana_selesai_or_keluar)) + 1,
                    'rencana_mulai_or_masuk' => $rencana_mulai_or_masuk,
                    'rencana_selesai_or_keluar' => $rencana_selesai_or_keluar,
                    'keterangan' => $keterangan
                ]);
            } else {
                if($masuk_or_keluar == 'M'){
                    $izin->update([
                        'rencana_mulai_or_masuk' => $rencana_masuk_or_keluar,
                        'rencana_selesai_or_keluar' => null,
                        'keterangan' => $keterangan
                    ]);
                } else {
                    $izin->update([
                        'rencana_mulai_or_masuk' => null,
                        'rencana_selesai_or_keluar' => $rencana_masuk_or_keluar,
                        'keterangan' => $keterangan
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Izin berhasil diubah!'], 200);
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

    public function delete(string $id_izin)
    {
        DB::beginTransaction();
        try{
            $izine = Izine::find($id_izin);

            if(!$izine){
                return response()->json(['message' => 'Data tidak ditemukan!'], 404);
            }

            $izine->delete();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Izin Dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_data_izin(string $id_izin)
    {
        $izine = Izine::find($id_izin);
        $data = [
            'id_izin' => $izine->id_izin,
            'jenis_izin' => $izine->jenis_izin,
            'rencana_mulai_or_masuk' => $izine->rencana_mulai_or_masuk,
            'rencana_selesai_or_keluar' => $izine->rencana_selesai_or_keluar,
            'keterangan' => $izine->keterangan,
            'masuk_or_keluar' => $izine->jenis_izin == 'TM' ? null : ($izine->rencana_mulai_or_masuk ? 'M' : 'K'),
        ];
        return response()->json(['data' => $data], 200);
    }
}
