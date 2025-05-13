<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Event;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\JenisCuti;
use App\Models\Departemen;
use App\Models\ApprovalCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\SendWhatsappNotification;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CutieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('atasan')){
            $jenis_cuti_title = 'Data Cuti by Jenis (Member) - '.Carbon::now()->format('F Y');
            $detail_cuti_title = 'Detail Cuti (Member) - '.date('Y');
        } else {
            $jenis_cuti_title = 'Data Cuti by Jenis (All Karyawan) - '.Carbon::now()->format('F Y');
            $detail_cuti_title = 'Detail Cuti (All Karyawan) - '.date('Y');
        }
        $dataPage = [
            'pageTitle' => "Cuti-E - Dashboard",
            'page' => 'cutie-dashboard',
            'jenis_cuti_title' => $jenis_cuti_title,
            'detail_cuti_title' => $detail_cuti_title,
        ];
        return view('pages.cuti-e.index', $dataPage);
    }

    public function export_cuti_view()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Cuti-E - Export Data",
            'page' => 'cutie-export',
            'departemen' => $departemens,
        ];
        return view('pages.cuti-e.export-cuti', $dataPage);
    }

    public function bypass_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cuti-E - Bypass Cuti",
            'page' => 'cutie-bypass-cuti',
        ];
        return view('pages.cuti-e.bypass-cuti', $dataPage);
    }

    public function setting_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cuti-E - Setting Cuti Khusus",
            'page' => 'cutie-setting',
        ];
        return view('pages.cuti-e.setting-cuti', $dataPage);
    }

    public function setting_cuti_datatable(Request $request)
    {
        $columns = array(
            0 => 'jenis',
            1 => 'durasi',
            2 => 'isUrgent',
            3 => 'isWorkday'
        );

        $totalData = JenisCuti::count();
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


        $jenis_cuti = JenisCuti::getData($dataFilter, $settings);
        $totalFiltered = JenisCuti::countData($dataFilter);

        $dataTable = [];


        if (!empty($jenis_cuti)) {
            foreach ($jenis_cuti as $data) {
                $nestedData['jenis'] = $data->jenis;
                $nestedData['durasi'] = $data->durasi.' Hari';
                $nestedData['isUrgent'] = $data->isUrgent == 'N' ? '❌' : '✅';
                $nestedData['isWorkday'] = $data->isWorkday == 'N' ? '❌' : '✅';
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm"><button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_jenis_cuti.'"><i class="fas fa-edit"></i> Edit </button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_jenis_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button></div>';

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

    // public function get_data_jenis_cuti_khusus(){
    //     $data = JenisCuti::all();
    //     foreach ($data as $jc) {
    //         $dataJenisCutiKhusus[] = [
    //             'id' => $jc->id_jenis_cuti,
    //             'text' => $jc->jenis,
    //             'durasi' => $jc->durasi,
    //             'isurgent' => $jc->isUrgent,
    //             'isworkday' => $jc->isWorkday
    //         ];
    //     }
    //     return response()->json(['data' => $dataJenisCutiKhusus],200);
    // }

    // public function get_data_detail_cuti(string $id_cuti)
    // {
    //     $cutie = Cutie::find($id_cuti);
    //     $data = [
    //         'id_cuti' => $cutie->id_cuti,
    //         'durasi_cuti' => $cutie->durasi_cuti,
    //         'jenis_cuti' => $cutie->jenis_cuti,
    //         'jenis_cuti_id' => $cutie->jenis_cuti_id,
    //         'rencana_mulai_cuti' => $cutie->rencana_mulai_cuti,
    //         'rencana_selesai_cuti' => $cutie->rencana_selesai_cuti,
    //         'alasan_cuti' => $cutie->alasan_cuti,
    //         'durasi_cuti' => $cutie->durasi_cuti,
    //     ];
    //     return response()->json(['data' => $data], 200);
    // }

    // public function get_data_detail_jenis_cuti(string $id_jenis_cuti)
    // {
    //     $jc = JenisCuti::find($id_jenis_cuti);
    //     $data = [
    //         'id_jenis_cuti' => $jc->id_jenis_cuti,
    //         'jenis' => $jc->jenis,
    //         'durasi' => $jc->durasi,
    //         'isUrgent' => $jc->isUrgent,
    //         'isWorkday' => $jc->isWorkday
    //     ];
    //     return response()->json(['data' => $data], 200);
    // }

    public function bypass_store(Request $request)
    {
        $dataValidate = [
            'id_karyawan' => ['required'],
            'penggunaan_sisa_cuti' => ['required','in:TL,TB'],
            'rencana_mulai_cuti' => ['date','required'],
            'rencana_selesai_cuti' => ['date','required', 'after_or_equal:rencana_mulai_cuti'],
            'alasan_cuti' => ['required'],
            'durasi_cuti' => ['numeric','required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id_karyawan = $request->id_karyawan;
        $karyawan = Karyawan::find($id_karyawan);
        $posisi = $karyawan->posisi;
        $sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi;
        $sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu;
        $expired_date_cuti_tahun_lalu = $karyawan->expired_date_cuti_tahun_lalu;

        $penggunaan_sisa_cuti = $request->penggunaan_sisa_cuti;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try{

            if($penggunaan_sisa_cuti == 'TB'){
                $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                if($sisa_cuti_pribadi < 0){
                    DB::rollback();
                    return response()->json(['message' => 'Sisa cuti pribadi karyawan tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                } else {
                    if($jatah_cuti < 0){
                        DB::rollback();
                        return response()->json(['message' => 'Sisa cuti pribadi karyawan tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                    } else {
                        $karyawan->sisa_cuti_pribadi = $jatah_cuti;
                        $karyawan->save();
                    }
                }
            } else {
                $jatah_cuti = $sisa_cuti_tahun_lalu - $durasi_cuti;
                if($sisa_cuti_tahun_lalu < 0){
                    DB::rollback();
                    return response()->json(['message' => 'Karyawan tidak memiliki sisa cuti tahun lalu!, Silahkan input menggunakan sisa cuti tahun berjalan karyawan!'], 402);
                } else {
                    if($jatah_cuti < 0){
                        DB::rollback();
                        return response()->json(['message' => 'Sisa cuti tahun lalu karyawan tidak mencukupi, Silahkan input menggunakan sisa cuti tahun berjalan karyawan!!'], 402);
                    } else {
                        $karyawan->sisa_cuti_tahun_lalu = $jatah_cuti;
                        $karyawan->save();
                    }
                }
            }

            $today = Carbon::now();
            if ($today->between(Carbon::parse($rencana_mulai_cuti), Carbon::parse($rencana_selesai_cuti))) {
                $status_cuti = 'ON LEAVE';
                $aktual_mulai_cuti = $rencana_mulai_cuti;
            } elseif (Carbon::parse($rencana_selesai_cuti)->lt($today)) {
                $status_cuti = 'COMPLETED';
                $aktual_mulai_cuti = $rencana_mulai_cuti;
                $aktual_selesai_cuti = $rencana_selesai_cuti;
            } else {
                $status_cuti = 'SCHEDULED';
                $aktual_mulai_cuti = null;
                $aktual_selesai_cuti = null;
            }

            $cuti = Cutie::create([
                'karyawan_id' => $id_karyawan,
                'organisasi_id' => $karyawan->user->organisasi_id,
                'jenis_cuti' => 'PRIBADI',
                'attachment' => null,
                'rencana_mulai_cuti' => $rencana_mulai_cuti,
                'rencana_selesai_cuti' => $rencana_selesai_cuti,
                'alasan_cuti' => $alasan_cuti,
                'durasi_cuti' => $durasi_cuti,
            ]);

            $approval_cuti = ApprovalCuti::storeApprovalCuti($cuti->id_cuti, $posisi);

            if (!empty($approval_cuti)) {
                ApprovalCuti::create([
                    'cuti_id' => $cuti->id_cuti,
                    'checked1_for' => $approval_cuti['checked1_for'],
                    'checked2_for' => $approval_cuti['checked2_for'],
                    'approved_for' => $approval_cuti['approved_for'],
                ]);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Approval cuti tidak ditemukan, hubungi HRD untuk informasi lebih lanjut!'], 402);
            }

            $message = "Nama : *" . $karyawan->nama . "*\n" .
                    "Jenis Cuti : PRIBADI (BYPASS) \n" .
                    "Pembuat dokumen tetap harus melakukan approval \nSegera lakukan approval pada sistem.\n" .
                    "Klik link dibawah untuk melakukan approval \n" .
                    ($organisasi_id == 1 ? env('URL_SERVER_HRIS_TCF2') : env('URL_SERVER_HRIS_TCF3'))."cutie/member-cuti";
            // $this->send_whatsapp($id_karyawan, $approval_cuti?->checked1_for, $message, $organisasi_id);
            DB::commit();
            return response()->json(['message' => 'Bypass Cuti Berhasil Dilakukan!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function store_jenis_cuti(Request $request)
    {
        $jenis = $request->jenis;
        $durasi = $request->durasi;
        $isUrgent = $request->isUrgent;
        $isWorkday = $request->isWorkday;

        $dataValidate = [
            'jenis' => ['required'],
            'durasi' => ['required', 'numeric', 'min:1'],
            'isUrgent' => ['required', 'string', 'in:Y,N'],
            'isWorkday' => ['required', 'string', 'in:Y,N'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        DB::beginTransaction();
        try{
            JenisCuti::create([
                'jenis' => $jenis,
                'durasi' => $durasi,
                'isUrgent' => $isUrgent,
                'isWorkday' => $isWorkday
            ]);
            DB::commit();
            return response()->json(['message' => 'Store Jenis Cuti Khusus Berhasil dilakukan!'], 200);
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

    public function update_jenis_cuti(Request $request, string $id_jenis_cuti)
    {
        $jenis = $request->jenis;
        $durasi = $request->durasi;
        $isUrgent = $request->isUrgent;
        $isWorkday = $request->isWorkday;

        $dataValidate = [
            'jenis' => ['required'],
            'durasi' => ['required', 'numeric', 'min:1'],
            'isUrgent' => ['required', 'string', 'in:Y,N'],
            'isWorkday' => ['required', 'string', 'in:Y,N'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        DB::beginTransaction();
        try{
            $jenis_cuti = JenisCuti::find($id_jenis_cuti);
            if($jenis_cuti){
                $jenis_cuti->jenis = $jenis;
                $jenis_cuti->durasi = $durasi;
                $jenis_cuti->isUrgent = $isUrgent;
                $jenis_cuti->isWorkday = $isWorkday;
            }

            $jenis_cuti->save();
            DB::commit();
            return response()->json(['message' => 'Update Jenis Cuti Khusus Berhasil dilakukan!'], 200);
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

    public function delete_jenis_cuti(string $id_jenis_cuti)
    {
        DB::beginTransaction();
        try{
            $jenis_cuti = JenisCuti::find($id_jenis_cuti);
            if($jenis_cuti){
                if($jenis_cuti->isUsed()){
                    DB::commit();
                    return response()->json(['message' => 'Jenis Cuti sudah digunakan, tidak bisa dihapus!'],400);
                } else {
                    $jenis_cuti->delete();
                }
            } else {
                DB::commit();
                return response()->json(['message' => 'Jenis Cuti tidak ditemukan!'],400);
            }

            DB::commit();
            return response()->json(['message' => 'Jenis Cuti Dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_data_cutie_calendar(){
        $organisasi_id = auth()->user()->organisasi_id;
        $cutie = Cutie::where('status_dokumen', '!=' ,'REJECTED')->where(function($query){
            $query->whereIn('status_cuti', ['SCHEDULED', 'ON LEAVE', 'COMPLETED'])->orWhereNull('status_cuti');
        });

        if(auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $members = $id_posisi_members;
        }

        if (isset($members)) {
            if (auth()->user()->karyawan->posisi[0]->jabatan_id == 1) {
                $cutie->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                    $query->whereIn('jabatan_id', [2,3,4]);
                })->orWhere('karyawan_id', auth()->user()->karyawan->id_karyawan);
            } else {
                $cutie->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                })->orWhere('karyawan_id', auth()->user()->karyawan->id_karyawan);
            }
            // $cutie = $cutie->orWhere('karyawan_id', auth()->user()->karyawan->id_karyawan)->where('status_dokumen', '!=' ,'REJECTED');
        } else {
            $cutie->organisasi($organisasi_id);
        }

        $event = Event::organisasi($organisasi_id)->where('jenis_event', 'CB')->get();
        $cutie = $cutie->get();
        $data = [];

        if($event){
            foreach ($event as $e) {
                if($e->jenis_event == 'CB'){
                    $className = 'bg-danger';
                } else {
                    $className = 'bg-info';
                }
                $data[] = [
                    'title' => $e->keterangan,
                    'start' => $e->tanggal_mulai,
                    'end' => $e->tanggal_selesai !== $e->tanggal_mulai ? Carbon::parse($e->tanggal_selesai)->addDay()->format('Y-m-d') : $e->tanggal_selesai,
                    'className' => $className,
                    'nama_karyawan' => 'Seluruh Karyawan',
                    'karyawan_pengganti' => '-',
                    'jenis_cuti' => $e->jenis_event == 'CB' ? 'CUTI BERSAMA' : 'EVENT PERUSAHAAN',
                    'rencana_mulai_cuti' => Carbon::parse($e->tanggal_mulai)->format('d M Y'),
                    'rencana_selesai_cuti' => Carbon::parse($e->tanggal_selesai)->format('d M Y'),
                    'alasan_cuti' => $e->keterangan,
                    'durasi_cuti' => $e->durasi.' Hari',
                    'status_cuti' => $e->tanggal_mulai > now() ? 'SCHEDULED' : 'COMPLETED',
                    'attachment' => 'No Attachment Needed',
                    'aktual_mulai_cuti' => $e->tanggal_mulai > now() ? '-' : Carbon::parse($e->tanggal_mulai)->format('d M Y'),
                    'aktual_selesai_cuti' => $e->tanggal_selesai > now() ? '-' : Carbon::parse($e->tanggal_selesai)->format('d M Y'),
                ];
            }
        }

        if($cutie){
            foreach ($cutie as $c) {
                if(Carbon::now()->format('Y-m-d') < $c->rencana_mulai_cuti){
                    $statusCuti = 'SCHEDULED';
                    $classname = 'bg-warning';
                }

                if (Carbon::now()->between(Carbon::createFromFormat('Y-m-d', $c->rencana_mulai_cuti), Carbon::createFromFormat('Y-m-d', $c->rencana_selesai_cuti))) {
                    $statusCuti = 'ON LEAVE';
                    $classname = 'bg-secondary';
                }

                if(Carbon::now()->format('Y-m-d') > $c->rencana_mulai_cuti){
                    $statusCuti = 'COMPLETED';
                    $classname = 'bg-success';
                }

                if ($c->status_dokumen == 'WAITING') {
                    $classname = 'bg-primary';
                }

                $data[] = [
                    'title' => $c->jenis_cuti.' - '.$c->karyawan->nama,
                    'start' => $c->rencana_mulai_cuti,
                    'end' => $c->rencana_selesai_cuti !== $c->rencana_mulai_cuti ? Carbon::parse($c->rencana_selesai_cuti)->addDay()->format('Y-m-d') : $c->rencana_selesai_cuti,
                    'className' => $classname,
                    'nama_karyawan' => $c->karyawan->nama,
                    'karyawan_pengganti' => $c->karyawan_pengganti_id ? $c->karyawanPengganti->nama : '-',
                    'jenis_cuti' => 'CUTI '.$c->jenis_cuti,
                    'rencana_mulai_cuti' => Carbon::parse($c->rencana_mulai_cuti)->format('d M Y'),
                    'rencana_selesai_cuti' => Carbon::parse($c->rencana_selesai_cuti)->format('d M Y'),
                    'alasan_cuti' => $c->alasan_cuti,
                    'durasi_cuti' => $c->durasi_cuti.' Hari',
                    'status_cuti' => !$c->status_cuti ? 'NEED APPROVE' : $statusCuti,
                    'attachment' => $c->attachment ? '<a href="'.asset('storage/'.$c->attachment).'" target="_blank">Lihat</a>' : 'No Attachment Needed',
                ];
            }
        }
        return response()->json($data, 200);
    }

    public function get_data_cuti_detail_chart(){
        $organisasi_id = auth()->user()->organisasi_id;

        //Data Cuti Detail perbulan dalam tahun berjalan
        $data['scheduled'] = [];
        $data['onleave'] = [];
        $data['canceled'] = [];
        $data['completed'] = [];
        $data['rejected'] = [];
        $data['total'] = [];

        $month = date('m');
        $year = date('Y');
        $month_array = ['01','02','03','04','05','06','07','08','09','10','11','12'];

        if(auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $members = $id_posisi_members;
        }


        for ($i = 0; $i <= 11; $i++) {

            //SCHEDULED
            $scheduledCount = Cutie::where('status_cuti', 'SCHEDULED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $scheduledCount = $scheduledCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $scheduledCount = $scheduledCount->organisasi($organisasi_id);
            }

            $scheduledCount = $scheduledCount->count();

            //ONLEAVE
            $onleaveCount = Cutie::where('status_cuti', 'ON LEAVE')
                ->where('status_dokumen', 'APPROVED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $onleaveCount = $onleaveCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $onleaveCount = $onleaveCount->organisasi($organisasi_id);
            }
            $onleaveCount = $onleaveCount->count();

            //COMPLETED
            $completedCount = Cutie::where('status_cuti', 'COMPLETED')
                ->where('status_dokumen', 'APPROVED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $completedCount = $completedCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $completedCount = $completedCount->organisasi($organisasi_id);
            }
            $completedCount = $completedCount->count();

            //CANCELED
            $canceledCount = Cutie::where('status_cuti', 'CANCELED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $canceledCount = $canceledCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $canceledCount = $canceledCount->organisasi($organisasi_id);
            }
            $canceledCount = $canceledCount->count();

            //REJECTED
            $rejectedCount = Cutie::where('status_dokumen', 'REJECTED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $rejectedCount = $rejectedCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $rejectedCount = $rejectedCount->organisasi($organisasi_id);
            }
            $rejectedCount = $rejectedCount->count();

            //UNLEGALIZED
            $unlegalizedCount = Cutie::whereNull('legalized_by')
                ->whereNull('rejected_by')
                ->where('status_cuti', '!=', 'CANCELED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $unlegalizedCount = $unlegalizedCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $unlegalizedCount = $unlegalizedCount->organisasi($organisasi_id);
            }
            $unlegalizedCount = $unlegalizedCount->count();

            //TOTAL
            $totalCount = Cutie::whereYear('rencana_mulai_cuti', $year)
            ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $totalCount = $totalCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $totalCount = $totalCount->organisasi($organisasi_id);
            }
            $totalCount = $totalCount->count();

            $data['scheduled'][] = $scheduledCount;
            $data['onleave'][] = $onleaveCount;
            $data['completed'][] = $completedCount;
            $data['canceled'][] = $canceledCount;
            $data['rejected'][] = $rejectedCount;
            $data['unlegalized'][] = $unlegalizedCount;
            $data['total'][] = $totalCount;
        }

        return response()->json(['data' => $data],200);
    }

    public function get_data_jenis_cuti_monthly_chart(){
        $month = date('m');
        $year = date('Y');
        $organisasi_id = auth()->user()->organisasi_id;

        if(auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $members = $id_posisi_members;
        }


        $monthly_pribadi = Cutie::where('jenis_cuti', 'PRIBADI')
            ->whereYear('rencana_mulai_cuti', $year)
            ->whereMonth('rencana_mulai_cuti', $month)
            ->where('status_cuti', '!=' ,'CANCELED')
            ->where('status_dokumen', 'APPROVED');

            if (isset($members)) {
                $monthly_pribadi = $monthly_pribadi->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else{
                $monthly_pribadi = $monthly_pribadi->organisasi($organisasi_id);
            }
        $monthly_pribadi = $monthly_pribadi->count();

        $monthly_khusus = Cutie::where('jenis_cuti', 'KHUSUS')
            ->whereYear('rencana_mulai_cuti', $year)
            ->whereMonth('rencana_mulai_cuti', $month)
            ->where('status_cuti', '!=' ,'CANCELED')
            ->where('status_dokumen', 'APPROVED');

            if (isset($members)) {
                $monthly_khusus = $monthly_khusus->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            } else {
                $monthly_khusus = $monthly_khusus->organisasi($organisasi_id);
            }
        $monthly_khusus = $monthly_khusus->count();
        $data = [$monthly_pribadi, $monthly_khusus];
        return response()->json(['data' => $data], 200);
    }

    public function export_cuti(Request $request){

        //GET DATA CUTI BY FILTER
        $departemen_id = $request->departemen_id;
        $organisasi_id = auth()->user()->organisasi_id;

        //Jenis Cuti
        $pribadi = $request->pribadi;
        $khusus = $request->khusus;
        // $sakit = $request->sakit;

        //Range Data Cuti
        $tahun = $request->tahun;
        $bulan = $request->bulan;


        $cutie = Cutie::organisasi($organisasi_id)->whereYear('rencana_mulai_cuti', $tahun);

        if($departemen_id !== 'all'){
            $cutie->whereHas('karyawan.posisi', function($query) use ($departemen_id){
                $query->where('departemen_id', $departemen_id);
            });
        }

        $jenis_cuti = [];
        if($pribadi == 'Y'){
           array_push($jenis_cuti, 'PRIBADI');
        }

        if($khusus == 'Y'){
            array_push($jenis_cuti, 'KHUSUS');
        }

        if(!empty($jenis_cuti)){
            $cutie->whereIn('jenis_cuti', $jenis_cuti);
        }

        //CREATE EXCEL FILE
        $spreadsheet = new Spreadsheet();

        $fillStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
           'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF000000'
                ]
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
        ];

        if($bulan){
            $monthlyCutie = $cutie->whereMonth('rencana_mulai_cuti', $bulan)->where('status_dokumen', 'APPROVED')->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED');
            })->orderBy('karyawan_id', 'DESC');
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(Carbon::createFromFormat('m', $bulan)->format('F Y'));
            $row = 1;
            $col = 'A';
            $headers = [
                'No',
                'Nomor Induk Karyawan',
                'Nama',
                'Departemen',
                'Jabatan',
                'Cuti Khusus',
                'Cuti Pribadi',
                'Cuti 1',
                'Cuti 2',
                'Cuti 3',
                'Cuti 4',
                'Cuti 5',
                'Cuti 6',
                'Sisa Cuti Pribadi'
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'N');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:N1');

            $monthlyCutie = $monthlyCutie->get();
            $jumlah_cuti_khusus = 0;
            $jumlah_cuti_pribadi = 0;
            $tanggal_cuti = [];
            foreach ($monthlyCutie as $index => $c) {

                if($c->jenis_cuti == 'KHUSUS'){
                    //MENDAPATKAN DURASI CUTI KHUSUS
                    $jumlah_cuti_khusus += $c->durasi_cuti;
                } else {
                    //MENDAPATKAN DURASI CUTI PRIBADI
                    $jumlah_cuti_pribadi += $c->durasi_cuti;
                    if($c->durasi_cuti > 1){
                        $range_date_cuti =  Carbon::parse($c->rencana_mulai_cuti)->toPeriod(Carbon::parse($c->rencana_selesai_cuti))->toArray();
                        foreach ($range_date_cuti as $r) {
                            $tanggal_cuti[] = $r->format('Y-m-d');
                        }
                    } else {
                        $tanggal_cuti[] = $c->rencana_mulai_cuti;
                    }
                }

                if(isset($monthlyCutie[$index+1])){
                    if($monthlyCutie[$index+1]->karyawan_id !== $c->karyawan_id){
                        $sheet->setCellValue('A' . $row, $row - 1);
                        $sheet->setCellValue('B' . $row, $c->karyawan->ni_karyawan);
                        $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                        $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                        $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                        $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                        $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                        $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                        $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                        $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                        $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                        $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                        $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                        $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');

                        $row++;

                        $jumlah_cuti_khusus = 0;
                        $jumlah_cuti_pribadi = 0;
                        $tanggal_cuti = [];
                    } else {
                        continue;
                    }
                } else {
                    $sheet->setCellValue('A' . $row, $row - 1);
                    $sheet->setCellValue('B' . $row, $c->karyawan->ni_karyawan);
                    $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                    $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                    $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                    $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                    $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                    $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                    $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                    $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                    $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                    $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                    $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                    $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');
                }
            }
        } else {
            for($i = 1; $i <= 12; $i++){
                $i = str_pad($i, 2, '0', STR_PAD_LEFT);
                $monthlyCutie = clone $cutie;
                $monthlyCutie = $monthlyCutie->whereMonth('rencana_mulai_cuti', Carbon::createFromFormat('m', $i)->format('m'))->where('status_dokumen', 'APPROVED')->where(function($query) {
                    $query->where('status_cuti', '!=', 'CANCELED');
                })->orderBy('karyawan_id', 'DESC');
                $monthlyCuties = $monthlyCutie->get();

                //Kalo bulan itu kosong jangan di export
                if($monthlyCuties->isEmpty()){
                    continue;
                }

                $sheet = $spreadsheet->createSheet($i - 1);
                $sheet->setTitle(Carbon::createFromFormat('m', $i)->format('F Y'));
                $row = 1;
                $col = 'A';
                $headers = [
                    'No',
                    'Nomor Induk Karyawan',
                    'Nama',
                    'Departemen',
                    'Jabatan',
                    'Cuti Khusus',
                    'Cuti Pribadi',
                    'Cuti 1',
                    'Cuti 2',
                    'Cuti 3',
                    'Cuti 4',
                    'Cuti 5',
                    'Cuti 6',
                    'Sisa Cuti Pribadi'
                ];

                foreach ($headers as $header) {
                    $sheet->setCellValue($col . '1', $header);
                    $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                    $col++;
                }

                $row = 2;

                $columns = range('A', 'N');
                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                $sheet->setAutoFilter('A1:N1');

                $jumlah_cuti_khusus = 0;
                $jumlah_cuti_pribadi = 0;
                $tanggal_cuti = [];
                foreach ($monthlyCuties as $index => $c) {

                    if($c->jenis_cuti == 'KHUSUS'){
                        //MENDAPATKAN DURASI CUTI KHUSUS
                        $jumlah_cuti_khusus += $c->durasi_cuti;
                    } else {
                        //MENDAPATKAN DURASI CUTI PRIBADI
                        $jumlah_cuti_pribadi += $c->durasi_cuti;
                        if($c->durasi_cuti > 1){
                            $range_date_cuti =  Carbon::parse($c->rencana_mulai_cuti)->toPeriod(Carbon::parse($c->rencana_selesai_cuti))->toArray();
                            foreach ($range_date_cuti as $r) {
                                $tanggal_cuti[] = $r->format('Y-m-d');
                            }
                        } else {
                            $tanggal_cuti[] = $c->rencana_mulai_cuti;
                        }
                    }

                    if(isset($monthlyCuties[$index+1])){
                        if($monthlyCuties[$index+1]->karyawan_id !== $c->karyawan_id){
                            $sheet->setCellValue('A' . $row, $row - 1);
                            $sheet->setCellValue('B' . $row, $c->karyawan->ni_karyawan);
                            $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                            $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                            $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                            $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                            $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                            $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                            $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                            $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                            $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                            $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                            $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                            $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');

                            $row++;

                            $jumlah_cuti_khusus = 0;
                            $jumlah_cuti_pribadi = 0;
                            $tanggal_cuti = [];
                        } else {
                            continue;
                        }
                    } else {
                        $sheet->setCellValue('A' . $row, $row - 1);
                        $sheet->setCellValue('B' . $row, $c->karyawan->ni_karyawan);
                        $sheet->setCellValue('C' . $row, $c->karyawan->nama);
                        $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]?->departemen?->nama);
                        $sheet->setCellValue('E' . $row, $c->karyawan->posisi[0]?->nama);
                        $sheet->setCellValue('F' . $row, $jumlah_cuti_khusus.' Hari');
                        $sheet->setCellValue('G' . $row, $jumlah_cuti_pribadi.' Hari');
                        $sheet->setCellValue('H' . $row, isset($tanggal_cuti[0]) ? $tanggal_cuti[0] : '');
                        $sheet->setCellValue('I' . $row, isset($tanggal_cuti[1]) ? $tanggal_cuti[1] : '');
                        $sheet->setCellValue('J' . $row, isset($tanggal_cuti[2]) ? $tanggal_cuti[2] : '');
                        $sheet->setCellValue('K' . $row, isset($tanggal_cuti[3]) ? $tanggal_cuti[3] : '');
                        $sheet->setCellValue('L' . $row, isset($tanggal_cuti[4]) ? $tanggal_cuti[4] : '');
                        $sheet->setCellValue('M' . $row, isset($tanggal_cuti[5]) ? $tanggal_cuti[5] : '');
                        $sheet->setCellValue('N' . $row, $c->karyawan->sisa_cuti_pribadi.' Hari');
                    }
                }
            }
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-cuti-export.xlsx');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    // public function get_karyawan_cuti(Request $request)
    // {
    //     $search = $request->input('search');
    //     $page = $request->input("page");
    //     $idCats = $request->input('catsProd');
    //     $adOrg = $request->input('adOrg');

    //     $query = Karyawan::select(
    //         'karyawans.id_karyawan',
    //         'karyawans.nama',
    //         'posisis.nama as posisi',
    //     );

    //     if(auth()->user()->hasRole('atasan')){
    //         $posisi = auth()->user()->karyawan->posisi;
    //         $id_posisi_members = $this->get_member_posisi($posisi);

    //         foreach ($posisi as $ps){
    //             $index = array_search($ps->id_posisi, $id_posisi_members);
    //             array_splice($id_posisi_members, $index, 1);
    //         }
    //     } else {
    //         $id_posisi_members = [];
    //     }

    //     $query->aktif();
    //     $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
    //     ->leftJoin('users', 'karyawans.user_id', 'users.id')
    //     ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
    //     ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
    //     ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

    //     $organisasi_id = auth()->user()->organisasi_id;
    //     // $query->where('users.organisasi_id', $organisasi_id);

    //     if(!empty($id_posisi_members)){
    //         $query->whereIn('posisis.id_posisi', $id_posisi_members);
    //     }

    //     if (!empty($search)) {
    //         $query->where(function ($dat) use ($search) {
    //             $dat->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
    //                 ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
    //         });
    //     }

    //     $query->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama');
    //     $data = $query->simplePaginate(10);

    //     $morePages = true;
    //     $pagination_obj = json_encode($data);
    //     if (empty($data->nextPageUrl())) {
    //         $morePages = false;
    //     }

    //     foreach ($data->items() as $karyawan) {
    //         $dataUser[] = [
    //             'id' => $karyawan->id_karyawan,
    //             'text' => $karyawan->nama
    //         ];
    //     }

    //     $results = array(
    //         "results" => $dataUser,
    //         "pagination" => array(
    //             "more" => $morePages
    //         )
    //     );

    //     return response()->json($results);
    // }

    // function get_member_posisi($posisis)
    // {
    //     $data = [];
    //     foreach ($posisis as $ps) {
    //         if ($ps->children) {
    //             $data = array_merge($data, $this->get_member_posisi($ps->children));
    //         }
    //         $data[] = $ps->id_posisi;
    //     }
    //     return $data;
    // }

    function send_whatsapp($id_karyawan, $for_posisi, $message, $organisasi_id)
    {
        $karyawan = Karyawan::find($id_karyawan);
        $karyawanPosisi = Posisi::where('id_posisi', $for_posisi)->first();
        $phoneNumbers = [];

        if ($karyawanPosisi){
            $karyawanPosisis = $karyawanPosisi->karyawan;
            foreach($karyawanPosisis as $kp){
                $phoneNumber = $kp->no_telp;
                if (substr($phoneNumber, 0, 1) == '0') {
                    $phoneNumber = '62' . substr($phoneNumber, 1);
                }
                $phoneNumbers[] = $phoneNumber . '@c.us';
            }

            if(count($phoneNumbers) > 0){
                $phoneNumber = $phoneNumbers;
            } else {
                $phoneNumber = $phoneNumbers[0];
            }

            SendWhatsappNotification::send($message, $organisasi_id, $phoneNumber);
        }
    }
}
