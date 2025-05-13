<?php

namespace App\Http\Controllers\Cutie;

use Carbon\Carbon;
use App\Helpers\Approval;
use Illuminate\Http\Request;
use App\Services\CutiService;
use App\Services\KaryawanService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PengajuanController extends Controller
{
    private $cutiService, $karyawanService;

    public function __construct(
        CutiService $cutiService,
        KaryawanService $karyawanService
    )
    {
        $this->cutiService = $cutiService;
        $this->karyawanService = $karyawanService;
    }

    public function index()
    {
        $dataPage = [
            'pageTitle' => "Cuti-E - Pengajuan Cuti",
            'page' => 'cutie-pengajuan-cuti',
        ];
        return view('pages.cuti-e.pengajuan-cuti', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'durasi_cuti',
            4 => 'jenis_cuti',
            5 => 'checked1_at',
            6 => 'checked2_at',
            7 => 'approved_at',
            8 => 'legalize_at',
            9 => 'status_dokumen',
            10 => 'status_cuti',
            11 => 'alasan_cuti',
            12 => 'kp.nama_pengganti',
            13 => 'created_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $totalData = $this->cutiService->countPengajuanDatatable($dataFilter);
        $totalFiltered = $totalData;
        $cutie = $this->cutiService->getPengajuanDatatable($dataFilter, $settings);
        $totalFiltered = $this->cutiService->countPengajuanDatatable($dataFilter);
        $dataTable = [];

        if (!empty($cutie)) {

            $cuti_id = null;
            $count_duplicate_cuti = 0;

            foreach ($cutie as $data) {
                if($data->checked1_by){
                    $btn_group_1 = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $btn_group_1 = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                } else {
                    $btn_group_1 = '-';
                }

                if($data->checked2_by){
                    $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $btn_group_2 = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                } else {
                    $btn_group_2 = '-';
                }

                if($data->approved_by){
                    $btn_group_3 = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $btn_group_3 = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                } else {
                    $btn_group_3 = '-';
                }

                if($data->legalized_by){
                    $btn_group_4 = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $btn_group_4 = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                } else {
                    if($data->approved_by){
                        $btn_group_4 = 'Waiting For Legalized';
                    } else {
                        $btn_group_4 = 'Waiting Approved';
                    }
                }

                //Status Cuti
                if(!$data->rejected_by){
                    if($data->status_cuti == 'SCHEDULED'){
                        $status_cuti = '<span class="badge badge-pill badge-warning">'.$data->status_cuti.'</span>';
                    } elseif($data->status_cuti == 'ON LEAVE'){
                        $status_cuti = '<span class="badge badge-pill badge-secondary">'.$data->status_cuti.'</span>';
                    } elseif($data->status_cuti == 'COMPLETED'){
                        $status_cuti = '<span class="badge badge-pill badge-success">'.$data->status_cuti.'</span>';
                    } elseif ($data->status_cuti == 'CANCELED') {
                        $status_cuti = '<span class="badge badge-pill badge-danger">'.$data->status_cuti.'</span>';
                    } else {
                        $status_cuti = '-';
                    }
                } else {
                    $status_cuti = '<span class="badge badge-pill badge-danger btnAlasan" data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">REJECTED</span>';
                }

                if($data->status_cuti == 'CANCELED'){
                    if(!$data->checked1_by){
                        $btn_group_1 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }

                    if(!$data->checked2_by){
                        $btn_group_2 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }

                    if(!$data->approved_by){
                        $btn_group_3 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }

                    if (!$data->legalized_by){
                        $btn_group_4 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }

                    $btn_karyawan_pengganti = '-';
                }


                $nestedData['no'] = $data->id_cuti;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['checked_1'] = $btn_group_1;
                $nestedData['checked_2'] = $btn_group_2;
                $nestedData['approved'] = $btn_group_3;
                $nestedData['legalized'] = $btn_group_4;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger btnAlasan" data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? '<small class="text-bold">'.$data->nama_pengganti.'</small>' : '-';
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    ($data->status_cuti !== 'CANCELED' ? (date('Y-m-d') < $data->rencana_mulai_cuti && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnCancel" data-id="'.$data->id_cuti.'"><i class="fas fa-history"></i> Cancel </button>' : '') : '').
                    ($data->checked1_by == null && $data->checked2_by == null && $data->approved_by == null && $data->legalized_by == null && $data->rejected_by == null && $data->status_cuti !== 'CANCELED' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                    '
                </div>
                ';

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
            "column" => $request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:PRIBADI,KHUSUS',
        ]);

        if($request->jenis_cuti == 'PRIBADI'){
            $request->validate([
                'jenis_cuti' => 'required',
                'rencana_mulai_cuti' => 'date|required|after_or_equal:'.Carbon::now()->addDays(7)->format('Y-m-d'),
                'rencana_selesai_cuti' => 'date|required|after_or_equal:rencana_mulai_cuti',
                'alasan_cuti' => 'required',
                'durasi_cuti' => 'numeric|required',
            ]);
        } else {
            $request->validate([
                'jenis_cuti' => 'required',
                'rencana_mulai_cuti' => 'date|required|after_or_equal:'.Carbon::now()->addDays(7)->format('Y-m-d'),
                'rencana_selesai_cuti' => 'date|required|after_or_equal:rencana_mulai_cuti',
                'alasan_cuti' => 'required',
                'durasi_cuti' => 'numeric|required',
            ]);
        }

        $jenis_cuti = $request->jenis_cuti;
        $jenis_cuti_id = $request->jenis_cuti_khusus;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;
        $kry = auth()->user()->karyawan;
        $posisi = $kry->posisi;
        $karyawan_id = $kry->id_karyawan;
        $sisa_cuti_pribadi = $kry->sisa_cuti_pribadi;
        $sisa_cuti_tahun_lalu = $kry->sisa_cuti_tahun_lalu;
        $expired_date_cuti_tahun_lalu = $kry->expired_date_cuti_tahun_lalu;
        $organisasi_id = auth()->user()->organisasi_id;
        $attachment = null;

        DB::beginTransaction();
        try{
            if ($jenis_cuti == 'PRIBADI') {
                $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                if($sisa_cuti_pribadi < 0){
                    DB::rollback();
                    return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                } else {
                    if($jatah_cuti < 0){
                        DB::rollback();
                        return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                    } else {
                        $kryData = [
                            'sisa_cuti_pribadi' => $jatah_cuti,
                        ];
                        $this->karyawanService->updateKaryawan($karyawan_id, $kryData);
                    }
                }
            }

            $cutiData = [
                'karyawan_id' => $karyawan_id,
                'organisasi_id' => $organisasi_id,
                'jenis_cuti' => $jenis_cuti,
                'jenis_cuti_id' => $jenis_cuti_id,
                'attachment' => $attachment,
                'rencana_mulai_cuti' => $rencana_mulai_cuti,
                'rencana_selesai_cuti' => $rencana_selesai_cuti,
                'alasan_cuti' => $alasan_cuti,
                'durasi_cuti' => $durasi_cuti,
            ];

            $cuti = $this->cutiService->createCuti($cutiData);
            $structure_approval_cuti = $this->cutiService->getStructureApprovalCuti($posisi);

            if (!empty($structure_approval_cuti)) {
                $approvalCuti = [
                    'cuti_id' => $cuti->id_cuti,
                    'checked1_for' => $structure_approval_cuti['checked1_for'],
                    'checked2_for' => $structure_approval_cuti['checked2_for'],
                    'approved_for' => $structure_approval_cuti['approved_for'],
                ];
                $this->cutiService->createApprovalCuti($approvalCuti);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Approval cuti tidak ditemukan, hubungi HRD untuk informasi lebih lanjut!'], 402);
            }

            // $karyawan = Karyawan::find($karyawan_id);
            // $message = "Nama : *" . $karyawan->nama . "*\n" .
            //         "Jenis : " . $jenis_cuti . "\n" .
            //         "Segera lakukan approval sebelum tanggal " . Carbon::parse($rencana_mulai_cuti)->format('d M Y') . ",\n" .
            //         "Klik link dibawah untuk melakukan approval \n" .
            //         ($organisasi_id == 1 ? env('URL_SERVER_HRIS_TCF2') : env('URL_SERVER_HRIS_TCF3'))."cutie/member-cuti";
            // $this->send_whatsapp($karyawan_id, $approval_cuti->checked1_for, $message, $organisasi_id);
            $data = [
                'sisa_cuti_tahunan' => $kry->sisa_cuti_pribadi + $kry->sisa_cuti_bersama,
                'sisa_cuti_pribadi' => $kry->sisa_cuti_pribadi,
                'sisa_cuti_tahun_lalu' => $kry->sisa_cuti_tahun_lalu
            ];
            DB::commit();
            return response()->json(['message' => 'Pengajuan cuti berhasil dibuat, konfirmasi ke atasan untuk melakukan approval!', 'data' => $data], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function delete(string $id_cuti)
    {
        DB::beginTransaction();
        try{
            $data = $this->cutiService->deleteCuti($id_cuti);
            DB::commit();
            return response()->json(['message' => 'Pengajuan Cuti Dihapus!', 'data' => $data],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function cancel(Request $request, string $id_cuti)
    {
        DB::beginTransaction();
        try{
            $updateData = [
                'status_cuti' => 'CANCELED'
            ];

            $data = $this->cutiService->cancelCuti($id_cuti, $updateData);
            DB::commit();
            return response()->json(['message' => 'Cuti Berhasil dicancel!', 'data' => $data ], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:PRIBADI,KHUSUS',
        ]);

        if($request->jenis_cuti == 'PRIBADI'){
            $request->validate([
                'jenis_cuti' => 'required',
                'rencana_mulai_cuti' => 'date|required|after_or_equal:'.Carbon::now()->addDays(7)->format('Y-m-d'),
                'rencana_selesai_cuti' => 'date|required|after_or_equal:rencana_mulai_cuti',
                'alasan_cuti' => 'required',
                'durasi_cuti' => 'numeric|required',
            ]);
        } else {
            $request->validate([
                'jenis_cuti' => 'required',
                'jenis_cuti_khusus' => 'required|numeric',
                'rencana_mulai_cuti' => 'date|required',
                'rencana_selesai_cuti' => 'date|required|after_or_equal:rencana_mulai_cuti',
                'durasi_cuti' => 'numeric|required',
            ]);
        }

        $jenis_cuti = $request->jenis_cuti;
        $jenis_cuti_id = $request->jenis_cuti_khusus;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;
        $karyawan = auth()->user()->karyawan;

        DB::beginTransaction();
        try{
            $dataCuti = [
                'jenis_cuti' => $jenis_cuti,
                'jenis_cuti_id' => $jenis_cuti_id,
                'rencana_mulai_cuti' => $rencana_mulai_cuti,
                'rencana_selesai_cuti' => $rencana_selesai_cuti,
                'alasan_cuti' => $alasan_cuti,
                'durasi_cuti' => $durasi_cuti,
            ];

            $cuti = $this->cutiService->updateCuti($id, $dataCuti);
            $total_sisa_cuti = $karyawan->sisa_cuti_pribadi + $karyawan->sisa_cuti_bersama;
            DB::commit();
            return response()->json(['message' => 'Pengajuan cuti berhasil diubah, konfirmasi ke atasan untuk melakukan approval!', 'data' => $total_sisa_cuti], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
