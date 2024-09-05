<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Posisi;
use App\Models\JenisCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CutieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Dashboard",
            'page' => 'cutie-dashboard',
        ];
        return view('pages.cuti-e.index', $dataPage);
    }

    public function pengajuan_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Pengajuan Cuti",
            'page' => 'cutie-pengajuan-cuti',
        ];
        return view('pages.cuti-e.pengajuan-cuti', $dataPage);
    }

    public function member_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Member Cuti",
            'page' => 'cutie-member-cuti',
        ];
        return view('pages.cuti-e.member-cuti', $dataPage);
    }

    public function pengajuan_cuti_datatable(Request $request)
    {

        $columns = array(
            0 => 'id_cuti',
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'aktual_mulai_cuti',
            4 => 'aktual_selesai_cuti',
            5 => 'durasi_cuti',
            6 => 'jenis_cuti',
            7 => 'alasan_cuti',
            8 => 'kp.nama_pengganti',
            9 => 'checked_at',
            10 => 'approved_at',
            11 => 'legalize_at',
            12 => 'status_dokumen',
            13 => 'status_cuti',
            14 => 'created_at',
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

        if(auth()->user()->hasRole('user')){
            $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;
        }

        $totalData = Cutie::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $cutie = Cutie::getData($dataFilter, $settings);
        $totalFiltered = $cutie->count();
        $dataTable = [];

        if (!empty($cutie)) {
            foreach ($cutie as $data) {
                $nestedData['no'] = $data->id_cuti;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? $data->nama_pengganti : '-';
                $nestedData['checked'] = $data->checked_by ? $data->checked_by.'<br>'.$data->checked_at : '-';
                $nestedData['approved'] = $data->approved_by ? $data->approved_by.'<br>'.$data->approved_at : '-';
                $nestedData['legalized'] = $data->legalized_by ? $data->legalized_by.'<br>'.$data->legalized_at : '-';
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $data->status_cuti == 'SCHEDULED' ? '<span class="badge badge-pill badge-warning">'.$data->status_cuti.'</span>' : ($data->status_cuti == 'ON LEAVE' ? '<span class="badge badge-pill badge-secondary">'.$data->status_cuti.'</span>' : '-');
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    ($data->checked_by == null || $data->approved_by == null || $data->legalized_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_cuti.'"><i class="fas fa-edit"></i> Edit</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').'
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

    public function member_cuti_datatable(Request $request)
    {

        $columns = array(
            0 => 'karyawans.nama',
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'aktual_mulai_cuti',
            4 => 'aktual_selesai_cuti',
            5 => 'durasi_cuti',
            6 => 'jenis_cuti',
            7 => 'alasan_cuti',
            8 => 'kp.nama_pengganti',
            9 => 'checked_at',
            10 => 'approved_at',
            11 => 'legalize_at',
            12 => 'status_dokumen',
            13 => 'status_cuti',
            14 => 'created_at',
        );

        $totalData = Cutie::count();
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


        //MENCARI MEMBER
        if(auth()->user()->hasRole('user')){
            $data_posisi_member = [];
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = $this->get_member_posisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $dataFilter['member_posisi_id'] = $id_posisi_members;
        }
        
        $cutie = Cutie::getData($dataFilter, $settings);
        $totalFiltered = $cutie->count();
        // $totalFiltered = Cutie::countData($dataFilter);

        $dataTable = [];

        if (!empty($cutie)) {
            foreach ($cutie as $data) {
                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? $data->nama_pengganti : '-';
                $nestedData['checked'] = $data->checked_by ? $data->checked_by.'<br>'.$data->checked_at : '-';
                $nestedData['approved'] = $data->approved_by ? $data->approved_by.'<br>'.$data->approved_at : '-';
                $nestedData['legalized'] = $data->legalized_by ? $data->legalized_by.'<br>'.$data->legalized_at : '-';
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $data->status_cuti == 'SCHEDULED' ? '<span class="badge badge-pill badge-warning">'.$data->status_cuti.'</span>' : ($data->status_cuti == 'ON LEAVE' ? '<span class="badge badge-pill badge-secondary">'.$data->status_cuti.'</span>' : '-');
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    ($data->checked_by == null || $data->approved_by == null || $data->legalized_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_cuti.'"><i class="fas fa-edit"></i> Edit</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').'
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

    function get_member_posisi($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_posisi($ps->children));
            }
            $data[] = $ps->id_posisi;
        }
        return $data;
    }

    public function get_data_jenis_cuti_khusus(){
        $data = JenisCuti::all();
        foreach ($data as $jc) {
            $dataJenisCutiKhusus[] = [
                'id' => $jc->id_jenis_cuti,
                'text' => $jc->jenis,
                'durasi' => $jc->durasi
            ];
        }
        return response()->json(['data' => $dataJenisCutiKhusus],200);
    }

    public function get_data_detail_cuti(string $id_cuti)
    {
        $cutie = Cutie::find($id_cuti);
        $data = [
            'id_cuti' => $cutie->id_cuti,
            'durasi_cuti' => $cutie->durasi_cuti,
            'jenis_cuti' => $cutie->jenis_cuti,
            'jenis_cuti_id' => $cutie->jenis_cuti_id,
            'rencana_mulai_cuti' => $cutie->rencana_mulai_cuti,
            'rencana_selesai_cuti' => $cutie->rencana_selesai_cuti,
            'alasan_cuti' => $cutie->alasan_cuti,
            'durasi_cuti' => $cutie->durasi_cuti,
        ];
        return response()->json(['data' => $data], 200);
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

        $jenis_cuti = $request->jenis_cuti;
        $jenis_cuti_id = $request->jenis_cuti_khusus;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;
        $karyawan_id = auth()->user()->karyawan->id_karyawan;

        if($jenis_cuti == 'PRIBADI'){
            $dataValidate = [
                'jenis_cuti' => ['required'],
                'rencana_mulai_cuti' => ['date','required'],
                'rencana_selesai_cuti' => ['date','required'],
                'alasan_cuti' => ['required'],
                'durasi_cuti' => ['numeric','required'],
            ];

            $err_text = 'Periksa Form Dengan Benar!';
        } elseif ($jenis_cuti == 'SAKIT') {
            $dataValidate = [
                'jenis_cuti' => ['required'],
                'attachment' => ['image', 'max:2048', 'mimes:jpg,png,jpeg,PNG,JPEG', 'required'],
                'rencana_mulai_cuti' => ['date','required'],
                'rencana_selesai_cuti' => ['date','required'],
                'durasi_cuti' => ['numeric','required'],
            ];
            
            $err_text = 'Pastikan Attachment Tidak Lebih dari 2mb & Periksa kembali Form anda!';
        } else {
            $dataValidate = [
                'jenis_cuti' => ['required'],
                'jenis_cuti_khusus' => ['required','numeric'],
                'rencana_mulai_cuti' => ['date','required'],
                'rencana_selesai_cuti' => ['date','required'],
                'durasi_cuti' => ['numeric','required'],
            ];
            $err_text = 'Periksa kembali Form anda & Pilih Jenis Cuti Khusus!';
        }
        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $err_text], 402);
        }

        DB::beginTransaction();
        try{

            if($jenis_cuti == 'SAKIT'){
                if($request->hasFile('attachment')){
                    $file = $request->file('attachment');
                    $surat_dokter = 'SD_' . time() . '.' . $file->getClientOriginalExtension();
                    $attachment = $file->storeAs("attachment/surat_dokter", $surat_dokter);
                } else {
                    return response()->json(['message' => 'Attachment tidak boleh kosong!'], 402);
                }
            } else {
                $attachment = null;
            }

            $cuti = Cutie::create([
                'karyawan_id' => $karyawan_id,
                'jenis_cuti' => $jenis_cuti,
                'jenis_cuti_id' => $jenis_cuti_id,
                'attachment' => $attachment,
                'rencana_mulai_cuti' => $rencana_mulai_cuti,
                'rencana_selesai_cuti' => $rencana_selesai_cuti,
                'alasan_cuti' => $alasan_cuti,
                'durasi_cuti' => $durasi_cuti,
            ]);
            DB::commit();
            return response()->json(['message' => 'Pengajuan cuti berhasil dibuat, konfirmasi ke atasan untuk melakukan approval!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
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
    public function update(Request $request, string $id)
    {
        $jenis_cuti = $request->jenis_cuti;
        $jenis_cuti_id = $request->jenis_cuti_khusus;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;

        if($jenis_cuti == 'PRIBADI'){
            $dataValidate = [
                'jenis_cuti' => ['required'],
                'rencana_mulai_cuti' => ['date','required'],
                'rencana_selesai_cuti' => ['date','required'],
                'alasan_cuti' => ['required'],
                'durasi_cuti' => ['numeric','required'],
            ];

            $err_text = 'Periksa Form Dengan Benar!';
        } elseif ($jenis_cuti == 'SAKIT') {
            $dataValidate = [
                'jenis_cuti' => ['required'],
                'attachment' => ['image', 'max:2048', 'mimes:jpg,png,jpeg,PNG,JPEG', 'required'],
                'rencana_mulai_cuti' => ['date','required'],
                'rencana_selesai_cuti' => ['date','required'],
                'durasi_cuti' => ['numeric','required'],
            ];
            
            $err_text = 'Pastikan Attachment Tidak Lebih dari 2mb & Periksa kembali Form anda!';
        } else {
            $dataValidate = [
                'jenis_cuti' => ['required'] ,
                'jenis_cuti_khusus' => ['required','numeric'],
                'rencana_mulai_cuti' => ['date','required'],
                'rencana_selesai_cuti' => ['date','required'],
                'durasi_cuti' => ['numeric','required'],
            ];
            $err_text = 'Periksa kembali Form anda & Pilih Jenis Cuti Khusus!';
        }
        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $err_text], 402);
        }

        DB::beginTransaction();
        try{
            $cuti = Cutie::find($id);
            if($jenis_cuti == 'SAKIT'){
                if($request->hasFile('attachment')){
                    $file = $request->file('attachment');
                    $surat_dokter = 'SD_' . time() . '.' . $file->getClientOriginalExtension();
                    $attachment = $file->storeAs("attachment/surat_dokter", $surat_dokter);
                    if($cuti->attachment){
                        Storage::delete($cuti->attachment);
                    }
                    $cuti->attachment = $attachment;
                } 
            } 
            $cuti->jenis_cuti = $jenis_cuti;
            $cuti->jenis_cuti_id = $jenis_cuti_id;
            $cuti->rencana_mulai_cuti = $rencana_mulai_cuti;
            $cuti->rencana_selesai_cuti = $rencana_selesai_cuti;
            $cuti->alasan_cuti = $alasan_cuti;
            $cuti->durasi_cuti = $durasi_cuti;
            $cuti->save();
            DB::commit();
            return response()->json(['message' => 'Pengajuan cuti berhasil diubah, konfirmasi ke atasan untuk melakukan approval!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete(string $id_cuti)
    {
        DB::beginTransaction();
        try{
            $cutie = Cutie::find($id_cuti);
            $cutie->delete();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Cuti Dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
