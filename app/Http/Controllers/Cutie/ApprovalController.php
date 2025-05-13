<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Services\CutiService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApprovalController extends Controller
{
    private $cutiService;
    public function __construct(CutiService $cutiService)
    {
        $this->cutiService = $cutiService;
    }

    public function index()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Cuti-E - Approval Cuti",
            'page' => 'cutie-approval-cuti',
            'departemens' => $departemens,
        ];

        return view('pages.cuti-e.approval.index', $dataPage);
    }

    public function must_approved_datatable(Request $request)
    {
        $columns = array(
            0 => 'nama',
            1 => 'nama_departemen',
            2 => 'rencana_mulai_cuti',
            3 => 'rencana_selesai_cuti',
            4 => 'durasi_cuti',
            5 => 'jenis_cuti',
            6 => 'checked1_at',
            7 => 'checked2_at',
            8 => 'approved_at',
            9 => 'legalize_at',
            10 => 'status_dokumen',
            11 => 'status_cuti',
            12 => 'alasan_cuti',
            13 => 'nama_pengganti',
            14 => 'created_at',
        );

        $totalData = $this->cutiService->countApprovalCuti();
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
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        //FILTER DATA
        $departemenFilter = $request->input('departemen');
        if (!empty($departemenFilter)) {
            $dataFilter['departemen'] = $departemenFilter;
        }

        $jenisCutiFilter = $request->input('jenisCuti');
        if (!empty($jenisCutiFilter)) {
            $dataFilter['jenisCuti'] = $jenisCutiFilter;
        }

        $statusCutiFilter = $request->input('statusCuti');
        if (!empty($statusCutiFilter)) {
            $dataFilter['statusCuti'] = $statusCutiFilter;
        }

        $statusDokumenFilter = $request->input('statusDokumen');
        if (!empty($statusDokumenFilter)) {
            $dataFilter['statusDokumen'] = $statusDokumenFilter;
        }

        $namaFilter = $request->input('nama');
        if (!empty($namaFilter)) {
            $dataFilter['nama'] = $namaFilter;
        }

        $durasiFilter = $request->input('durasi');
        if (!empty($durasiFilter)) {
            $dataFilter['durasi'] = $durasiFilter;
        }

        $rencanamulaiFilter = $request->input('rencanaMulai');
        if (!empty($rencanamulaiFilter)) {
            $dataFilter['rencanaMulai'] = $rencanamulaiFilter;
        }

        //MENCARI MEMBER
        $is_can_legalized = false;
        $is_can_checked = false;
        $is_can_approved = false;

        if (auth()->user()->hasRole('personalia')) {
            $my_posisi = null;
            $is_can_legalized = true;
            $organisasi_id = auth()->user()->organisasi_id;
            if ($organisasi_id) {
                $dataFilter['organisasi_id'] = $organisasi_id;
            }
        } elseif (auth()->user()->hasRole('atasan')){
            $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;
            if (auth()->user()->karyawan->posisi[0]->jabatan_id >= 4){
                $is_can_checked = true;
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 4){
                $is_can_approved = true;
            }

            $dataFilter['member_posisi_id'] = true;
        }

        $cutie = $this->cutiService->getMustApprovedDatatable($dataFilter, $settings);
        $totalFiltered = $this->cutiService->countMustApprovedDatatable($dataFilter);

        $dataTable = [];

        if (!empty($cutie)) {
            $cuti_id = null;
            $count_duplicate_cuti = 0;
            foreach ($cutie as $data) {

                if(!$cuti_id){
                    $cuti_id = $data->id_cuti;
                } else {
                    if($cuti_id == $data->id_cuti){
                        $count_duplicate_cuti++;
                        continue;
                    } else {
                        $cuti_id = $data->id_cuti;
                    }
                }

                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $created_at = Carbon::parse($data->created_at)->format('d M Y, H:i:s');
                $list_atasan = Approval::ListAtasan($posisi);
                $has_leader = $list_atasan['leader'];
                $has_section_head = $list_atasan['section_head'];
                $has_department_head = $list_atasan['department_head'];
                $checked1_by = '🕛 Need Checked';
                $checked2_by = '🕛 Need Checked';
                $approved_by = '🕛 Need Approved';
                $legalized_by = '🕛 Need Legalized';
                $aksi = '-';

                //KONDISI CHECKED
                if ($is_can_checked){

                    //CHECKED 1
                    if($has_leader && $my_posisi == 5){
                        if(!$data->checked1_by){
                            $checked1_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="checked_1"><i class="far fa-check-circle"></i> Checked</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    //CHECKED 2
                    if($has_section_head && $has_department_head && $my_posisi == 4){
                        if(!$data->checked2_by){
                            $checked2_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="checked_2"><i class="far fa-check-circle"></i> Checked</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }
                }

                //KONDISI APPROVED
                if ($is_can_approved){

                    //SECTION HEAD
                    if($has_leader && $has_section_head && !$has_department_head && $my_posisi == 4){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    if(!$has_leader && $has_section_head && !$has_department_head && $my_posisi == 4){
                        $checked1_by = 'Directly Approved';
                        $checked2_by = 'Directly Approved';
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    //DEPARTMENT HEAD
                    if(!$has_leader && $has_section_head && $has_department_head && $my_posisi == 3){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    if($has_leader && $has_section_head && $has_department_head && $my_posisi == 3){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    if(!$has_leader && !$has_section_head && $has_department_head && $my_posisi == 3){
                        $checked1_by = 'Directly Approved';
                        $checked2_by = 'Directly Approved';
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    // OTHER
                    if(!$has_section_head && !$has_department_head){
                        $checked1_by = 'Directly Approved';
                        $checked2_by = 'Directly Approved';
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    if($has_leader && !$has_section_head && $has_department_head && $my_posisi == 3)
                    {
                        $checked2_by = 'Directly Approved';
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }
                }


                if ($is_can_legalized) {
                    if(!$data->legalized_by){
                        if($data->approved_by){
                            $legalized = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'" data-issued-name="HRD & GA" data-type="legalized"><i class="fas fa-balance-scale"></i> Legalized</button>';
                            $reject = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="HRD & GA"><i class="far fa-times-circle"></i> Reject</button>';
                            $legalized_by = '<div class="btn-group btn-sm">'.$legalized.$reject.'</div>';
                        } else {
                            $legalized_by = 'Waiting Checked/Approved';
                        }
                    }

                    $aksi = '<div class="btn-group btn-group-sm">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>
                    '.(date('Y-m-d') <= Carbon::parse($data->rencana_mulai_cuti)->addDays(7)->format('Y-m-d') ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnCancel" data-id="'.$data->id_cuti.'"><i class="fas fa-history"></i> Cancel </button>' : '').'
                    </div>';
                }

                //KONDISI JIKA SUDAH ADA DATA
                if($data->checked1_by){
                    $checked1_by = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $checked1_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                if($data->checked2_by){
                    $checked2_by = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $checked2_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $approved_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $legalized_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                //KARYAWAN PENGGANTI
                if ($data->nama_pengganti && !$data->legalized_by && !$data->rejected_by){
                    $karyawan_pengganti = '<small class="text-bold">'.$data->nama_pengganti.'</small><br>'.'<button type="button" class="waves-effect waves-light btn btn-sm btn-secondary btnKaryawanPengganti" data-id="'.$data->id_cuti.'" data-karyawan-id="'.$data->karyawan_id.'" data-karyawan-pengganti-id="'.$data->karyawan_pengganti_id.'"><i class="fas fa-user-friends"></i> Pilih</button>';
                } elseif($data->nama_pengganti && $data->legalized_by && !$data->rejected_by){
                    $karyawan_pengganti = '<small class="text-bold">'.$data->nama_pengganti.'</small>';
                } elseif (!$data->nama_pengganti && !$data->legalized_by && !$data->rejected_by){
                    $karyawan_pengganti = '<button type="button" class="waves-effect waves-light btn btn-sm btn-secondary btnKaryawanPengganti" data-id="'.$data->id_cuti.'" data-karyawan-id="'.$data->karyawan_id.'" data-karyawan-pengganti-id="'.$data->karyawan_pengganti_id.'"><i class="fas fa-user-friends"></i> Pilih</button>';
                } else {
                    $karyawan_pengganti = '-';
                }

                //STATUS CUTI
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

                //JIKA CANCEL
                if($data->status_cuti == 'CANCELED'){
                    $checked1_by = $checked2_by = $approved_by = $legalized_by = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    $karyawan_pengganti = '-';
                }

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['checked_1'] = $checked1_by;
                $nestedData['checked_2'] = $checked2_by;
                $nestedData['approved'] = $approved_by;
                $nestedData['legalized'] = $legalized_by;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger btnAlasan" data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $karyawan_pengganti;
                $nestedData['created_at'] = $created_at;
                $nestedData['aksi'] = $aksi;

                $dataTable[] = $nestedData;
            }
        }


        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered) - $count_duplicate_cuti,
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function alldata_datatable(Request $request)
    {
        $columns = array(
            0 => 'nama',
            1 => 'nama_departemen',
            2 => 'rencana_mulai_cuti',
            3 => 'rencana_selesai_cuti',
            4 => 'durasi_cuti',
            5 => 'jenis_cuti',
            6 => 'checked1_at',
            7 => 'checked2_at',
            8 => 'approved_at',
            9 => 'legalize_at',
            10 => 'status_dokumen',
            11 => 'status_cuti',
            12 => 'alasan_cuti',
            13 => 'nama_pengganti',
            14 => 'created_at',
        );

        $totalData = $this->cutiService->countApprovalCuti();
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
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        //FILTER DATA
        $departemenFilter = $request->input('departemen');
        if (!empty($departemenFilter)) {
            $dataFilter['departemen'] = $departemenFilter;
        }

        $jenisCutiFilter = $request->input('jenisCuti');
        if (!empty($jenisCutiFilter)) {
            $dataFilter['jenisCuti'] = $jenisCutiFilter;
        }

        $statusCutiFilter = $request->input('statusCuti');
        if (!empty($statusCutiFilter)) {
            $dataFilter['statusCuti'] = $statusCutiFilter;
        }

        $statusDokumenFilter = $request->input('statusDokumen');
        if (!empty($statusDokumenFilter)) {
            $dataFilter['statusDokumen'] = $statusDokumenFilter;
        }

        $namaFilter = $request->input('nama');
        if (!empty($namaFilter)) {
            $dataFilter['nama'] = $namaFilter;
        }

        $durasiFilter = $request->input('durasi');
        if (!empty($durasiFilter)) {
            $dataFilter['durasi'] = $durasiFilter;
        }

        $rencanamulaiFilter = $request->input('rencanaMulai');
        if (!empty($rencanamulaiFilter)) {
            $dataFilter['rencanaMulai'] = $rencanamulaiFilter;
        }

        $is_can_checked = false;
        $is_can_approved = false;
        $is_can_legalized = false;

        if (auth()->user()->hasRole('personalia')) {
            $my_posisi = null;
            $is_can_legalized = true;
            $organisasi_id = auth()->user()->organisasi_id;
            if ($organisasi_id) {
                $dataFilter['organisasi_id'] = $organisasi_id;
            }
        } elseif (auth()->user()->hasRole('atasan')){
            $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;
            if (auth()->user()->karyawan->posisi[0]->jabatan_id >= 4){
                $is_can_checked = true;
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 4){
                $is_can_approved = true;
            }

            $dataFilter['member_posisi_id'] = true;
        }

        $cutie = $this->cutiService->getAllDataDatatable($dataFilter, $settings);
        $totalFiltered = $this->cutiService->countAllDataDatatable($dataFilter);

        $dataTable = [];

        if (!empty($cutie)) {
            $cuti_id = null;
            $count_duplicate_cuti = 0;
            foreach ($cutie as $data) {

                if(!$cuti_id){
                    $cuti_id = $data->id_cuti;
                } else {
                    if($cuti_id == $data->id_cuti){
                        $count_duplicate_cuti++;
                        continue;
                    } else {
                        $cuti_id = $data->id_cuti;
                    }
                }

                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $created_at = Carbon::parse($data->created_at)->format('d M Y, H:i:s');
                $list_atasan = Approval::ListAtasan($posisi);
                $has_leader = $list_atasan['leader'];
                $has_section_head = $list_atasan['section_head'];
                $has_department_head = $list_atasan['department_head'];
                $checked1_by = '🕛 Need Checked';
                $checked2_by = '🕛 Need Checked';
                $approved_by = '🕛 Need Approved';
                $legalized_by = '🕛 Need Legalized';
                $aksi = '-';

                //KONDISI CHECKED
                if ($is_can_checked){

                    //CHECKED 1
                    if($has_leader && $my_posisi == 5){
                        if(!$data->checked1_by){
                            $checked1_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="checked_1"><i class="far fa-check-circle"></i> Checked</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }

                    //CHECKED 2
                    if($has_section_head && $has_department_head && $my_posisi == 4){
                        if(!$data->checked2_by){
                            $checked2_by = '<div class="btn-group btn-sm">
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-issued-id="'.auth()->user()->karyawan->id_karyawan.'" data-type="checked_2"><i class="far fa-check-circle"></i> Checked</button>
                                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>
                            </div>';
                        }
                    }
                }

                //KONDISI JIKA SUDAH ADA DATA
                if($data->checked1_by){
                    $checked1_by = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $checked1_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                if($data->checked2_by){
                    $checked2_by = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $checked2_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $approved_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $legalized_by = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                //KARYAWAN PENGGANTI
                if ($data->nama_pengganti){
                    $karyawan_pengganti = '<small class="text-bold">'.$data->nama_pengganti.'</small>';
                } else {
                    $karyawan_pengganti = '-';
                }

                //STATUS CUTI
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

                //JIKA CANCEL
                if($data->status_cuti == 'CANCELED'){
                    $checked1_by = $checked2_by = $approved_by = $legalized_by = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    $karyawan_pengganti = '-';
                }

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['checked_1'] = $checked1_by;
                $nestedData['checked_2'] = $checked2_by;
                $nestedData['approved'] = $approved_by;
                $nestedData['legalized'] = $legalized_by;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger btnAlasan" data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $karyawan_pengganti;
                $nestedData['created_at'] = $created_at;
                $nestedData['aksi'] = $aksi;

                $dataTable[] = $nestedData;
            }
        }


        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered) - $count_duplicate_cuti,
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function reject(Request $request, string $id_cuti)
    {
        $request->validate([
            'nama_atasan' => 'required',
            'alasan_reject' => 'required'
        ]);

        $nama_atasan = $request->nama_atasan;
        $alasan_reject = $request->alasan_reject;
        DB::beginTransaction();
        try{

            $data = [
                'rejected_by' => $nama_atasan,
                'rejected_at' => now(),
                'rejected_note' => $alasan_reject,
                'status_dokumen' => 'REJECTED'
            ];
            $this->cutiService->rejectCuti($id_cuti, $data);
            DB::commit();
            return response()->json(['message' => 'Reject Cuti Berhasil dilakukan!'], 200);
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

     public function update_dokumen_cuti(Request $request, string $id_cuti)
    {
        $type = $request->type;

        if($type !== 'legalized'){
            $request->validate([
                'issued_name' => 'required',
                'type' => 'required',
                'issued_id' => 'required|exists:karyawans,id_karyawan'
            ]);
        } else {
            $request->validate([
                'issued_name' => 'required',
                'type' => 'required',
            ]);
        }

        $issued_name = $request->issued_name;
        $issued_id = $request->issued_id;
        $posisi = Karyawan::find($issued_id)->posisi[0]->id_posisi;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try{
            $cuti = $this->cutiService->getById($id_cuti);
            $id_approval = $cuti->approval->id_approval_cuti;
            if($type == 'checked_1'){
                $data = [
                    'checked1_by' => $issued_name,
                    'checked1_at' => now()
                ];

                $approvalData = [
                    'checked1_by' => $posisi,
                    'checked1_karyawan_id' => $issued_id
                ];

                $this->cutiService->updateCuti($id_cuti, $data);
                $this->cutiService->updateApprovalCuti($id_approval, $approvalData);

                // $message = "Nama : *" . $cuti->karyawan->nama . "*\n" .
                //         "Jenis Cuti : " . $cuti->jenis_cuti . "\n" .
                //         "Segera lakukan approval sebelum tanggal " . Carbon::parse($cuti->rencana_mulai_cuti)->format('d M Y') . ",\n" .
                //         "Klik link dibawah untuk melakukan approval \n" .
                //         ($data['organisasi_id'] == 1 ? env('URL_SERVER_HRIS_TCF2') : env('URL_SERVER_HRIS_TCF3'))."cutie/approval-cuti";
                // $this->send_whatsapp($cuti->karyawan->id_karyawan, $cuti->approval->checked2_for, $message, $data['organisasi_id']);
            } elseif ($type == 'checked_2'){
                if(!$cuti->checked1_by){
                    $data = [
                        'checked1_by' => $issued_name,
                        'checked1_at' => now()
                    ];

                    $approvalData = [
                        'checked1_by' => $posisi,
                        'checked1_karyawan_id' => $issued_id
                    ];

                    $this->cutiService->updateCuti($id_cuti, $data);
                    $this->cutiService->updateApprovalCuti($id_approval, $approvalData);
                }

                $data = [
                    'checked2_by' => $issued_name,
                    'checked2_at' => now()
                ];

                $approvalData = [
                    'checked2_by' => $posisi,
                    'checked2_karyawan_id' => $issued_id
                ];

                $this->cutiService->updateCuti($id_cuti, $data);
                $this->cutiService->updateApprovalCuti($id_approval, $approvalData);

                // $message = "Nama : *" . $cuti->karyawan->nama . "*\n" .
                //         "Jenis Cuti : " . $cuti->jenis_cuti . "\n" .
                //         "Segera lakukan approval sebelum tanggal " . Carbon::parse($cuti->rencana_mulai_cuti)->format('d M Y') . ",\n" .
                //         "Klik link dibawah untuk melakukan approval \n" .
                //         ($data['organisasi_id'] == 1 ? env('URL_SERVER_HRIS_TCF2') : env('URL_SERVER_HRIS_TCF3'))."cutie/approval-cuti";
                // $this->send_whatsapp($cuti->karyawan->id_karyawan, $cuti->approval->approved_for, $message, $data['organisasi_id']);
            } elseif ($type == 'approved'){
                if(!$cuti->checked1_by){
                    $data = [
                        'checked1_by' => $issued_name,
                        'checked1_at' => now()
                    ];

                    $approvalData = [
                        'checked1_by' => $posisi,
                        'checked1_karyawan_id' => $issued_id
                    ];

                    $this->cutiService->updateCuti($id_cuti, $data);
                    $this->cutiService->updateApprovalCuti($id_approval, $approvalData);
                }

                if(!$cuti->checked2_by){
                    $data = [
                        'checked2_by' => $issued_name,
                        'checked2_at' => now()
                    ];

                    $approvalData = [
                        'checked2_by' => $posisi,
                        'checked2_karyawan_id' => $issued_id
                    ];
                    $this->cutiService->updateCuti($id_cuti, $data);
                    $this->cutiService->updateApprovalCuti($id_approval, $approvalData);
                }

                $data = [
                    'approved_by' => $issued_name,
                    'approved_at' => now()
                ];

                $approvalData = [
                    'approved_by' => $posisi,
                    'approved_karyawan_id' => $issued_id
                ];

                $this->cutiService->updateCuti($id_cuti, $data);
                $this->cutiService->updateApprovalCuti($id_approval, $approvalData);
            } else {
                //LOGIKA UNTUK BYPASS CUTI
                if($cuti->rencana_mulai_cuti < date('Y-m-d', strtotime('+7 days')) && $cuti->jenis_cuti == 'PRIBADI'){
                    if($cuti->rencana_mulai_cuti > date('Y-m-d')){
                        $data = [
                            'legalized_by' => $issued_name,
                            'status_dokumen' => 'APPROVED',
                            'status_cuti' => 'SCHEDULED',
                            'legalized_at' => now()
                        ];
                        $this->cutiService->updateCuti($id_cuti, $data);
                    } elseif ($cuti->rencana_mulai_cuti == date('Y-m-d')){
                        $data = [
                            'legalized_by' => $issued_name,
                            'status_dokumen' => 'APPROVED',
                            'status_cuti' => 'ON LEAVE',
                            'legalized_at' => now()
                        ];
                        $this->cutiService->updateCuti($id_cuti, $data);
                    } else {
                        $data = [
                            'legalized_by' => $issued_name,
                            'status_dokumen' => 'APPROVED',
                            'status_cuti' => 'COMPLETED',
                            'legalized_at' => now()
                        ];
                        $this->cutiService->updateCuti($id_cuti, $data);
                    }
                //LOGIKAN UNTUK CUTI BIASA
                } else {
                    $data = [
                        'legalized_by' => $issued_name,
                        'status_dokumen' => 'APPROVED',
                        'status_cuti' => 'SCHEDULED',
                        'legalized_at' => now()
                    ];
                    $this->cutiService->updateCuti($id_cuti, $data);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Update Dokumen Cuti Berhasil dilakukan!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function update_karyawan_pengganti(Request $request, string $id_cuti)
    {
        $request->validate([
            'karyawan_pengganti_id' => 'required|exists:karyawans,id_karyawan'
        ]);

        $karyawan_pengganti_id = $request->karyawan_pengganti_id;

        DB::beginTransaction();
        try{
            $data = [
                'karyawan_pengganti_id' => $karyawan_pengganti_id
            ];
            $cuti = $this->cutiService->updateCuti($id_cuti, $data);
            DB::commit();
            return response()->json(['message' => 'Update Karyawan Pengganti Berhasil dilakukan!'], 200);
        } catch(Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
