<?php

namespace App\Http\Controllers\Lembure;

use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\Lembure;
use App\Models\Karyawan;
use Carbon\CarbonPeriod;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Support\Str;
use App\Models\DetailLembur;
use App\Models\LemburHarian;
use Illuminate\Http\Request;
use App\Models\SettingLembur;
use App\Models\GajiDepartemen;
use App\Models\AttachmentLembur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\SettingLemburKaryawan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LembureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Dashboard",
            'page' => 'lembure-dashboard'
        ];
        return view('pages.lembur-e.index', $dataPage);
    }

    public function detail_lembur_view()
    {
        if(auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id >= 5){
            return redirect()->route('lembure.pengajuan-lembur');
        }

        if(auth()->user()->hasRole('personalia') || (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)){
            $departemens = Departemen::all();
        } else {
            $posisis = auth()->user()->karyawan->posisi;
            $departemen_ids = [];
            $divisi_ids = [];
            foreach ($posisis as $posisi){
                if($posisi->departemen_id !== null){
                    $departemen_ids[] = $posisi->departemen_id;
                }

                if($posisi->divisi_id !== null){
                    $divisi_ids[] = $posisi->divisi_id;
                }
            }

            if(!empty($departemen_ids)){
                $departemens = Departemen::whereIn('id_departemen', $departemen_ids)->get();
            } else {
                $departemens = Departemen::whereIn('divisi_id', $divisi_ids)->get();
            }
        }

        $dataPage = [
            'pageTitle' => "Lembur-E - Leaderboard Lembur",
            'page' => 'lembure-detail-lembur',
            'departemens' => $departemens
        ];
        return view('pages.lembur-e.detail-lembur', $dataPage);
    }

    public function pengajuan_lembur_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Pengajuan Lembur",
            'page' => 'lembure-pengajuan-lembur',
        ];
        return view('pages.lembur-e.pengajuan-lembur', $dataPage);
    }

    public function approval_lembur_view()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Lembur-E - Approval Lembur",
            'page' => 'lembure-approval-lembur',
            'departemens' => $departemens
        ];
        return view('pages.lembur-e.approval-lembur', $dataPage);
    }

    public function setting_upah_lembur_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Setting Upah Lembur",
            'page' => 'lembure-setting-upah-lembur',
        ];
        return view('pages.lembur-e.setting-upah-lembur', $dataPage);
    }

    public function setting_lembur_view()
    {
        $setting_lembur = SettingLembur::where('organisasi_id', auth()->user()->organisasi_id)->get();
        $data_setting_lembur = [];
        foreach ($setting_lembur as $setting) {
            $data_setting_lembur[$setting->setting_name] = $setting->value;
        }

        $dataPage = [
            'pageTitle' => "Lembur-E - Setting Lembur",
            'page' => 'lembure-setting-lembur',
            'setting_lembur' => $data_setting_lembur
        ];
        return view('pages.lembur-e.setting-lembur', $dataPage);
    }

    public function setting_gaji_departemen_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Setting Gaji Departemen",
            'page' => 'lembure-setting-gaji-departemen',
        ];
        return view('pages.lembur-e.setting-gaji-departemen', $dataPage);
    }

    public function export_report_lembur_view()
    {
        $departments = Departemen::all();
        $dataPage = [
            'pageTitle' => "Lembur-E - Export Report Lembur",
            'page' => 'lembure-export-report-lembur',
            'departments' => $departments
        ];
        return view('pages.lembur-e.export-report-lembur', $dataPage);
    }

    public function pengajuan_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'lemburs.id_lembur',
            1 => 'lemburs.issued_date',
            3 => 'karyawans.nama',
            4 => 'lemburs.jenis_hari',
            5 => 'lemburs.total_durasi',
            6 => 'lemburs.status',
            7 => 'lemburs.plan_checked_by',
            8=> 'lemburs.plan_approved_by',
            9 => 'lemburs.plan_legalized_by',
            10 => 'lemburs.actual_checked_by',
            11 => 'lemburs.actual_approved_by',
            12 => 'lemburs.actual_legalized_by'
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

        $issued_by = auth()->user()->karyawan->id_karyawan;
        if(!empty($issued_by)){
            $dataFilter['issued_by'] = $issued_by;
        }

        $totalData = Lembure::where('issued_by', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;

        $lembure = Lembure::getData($dataFilter, $settings);
        $totalFiltered = Lembure::countData($dataFilter);
        $dataTable = [];

        if (!empty($lembure)) {
            foreach ($lembure as $data) {
                $jam = floor($data->total_durasi / 60);
                $menit = $data->total_durasi % 60;
                $tanggal_lembur = Carbon::parse(DetailLembur::where('lembur_id', $data->id_lembur)->first()->rencana_mulai_lembur)->format('Y-m-d');
                $is_member = false;
                $rejected = false;

                if($data->status == 'WAITING'){
                    $status = '<span class="badge badge-warning">WAITING</span>';
                } elseif ($data->status == 'PLANNED'){
                    $status = '<span class="badge badge-info">PLANNED</span>';
                } elseif ($data->status == 'COMPLETED'){
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                } else {
                    $rejected = true;
                    $status = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'-'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                if(auth()->user()->karyawan->posisi[0]->jabatan_id >= 4){
                    $is_member = true;
                }

                $nestedData['id_lembur'] = $data->id_lembur;
                $nestedData['issued_date'] = Carbon::parse($data->issued_date)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['rencana_mulai_lembur'] = Carbon::parse($data->detailLembur[0]->rencana_mulai_lembur)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['issued_by'] = $data->nama_karyawan;
                $nestedData['jenis_hari'] = $data->jenis_hari;
                $nestedData['total_durasi'] = $jam . ' Jam ' . $menit . ' Menit';
                $nestedData['status'] = $status;
                $nestedData['plan_checked_by'] = $data->plan_checked_by ? '✅<br><small class="text-bold">'.$data?->plan_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_checked_at)->diffForHumans().'</small>': '';
                $nestedData['plan_approved_by'] = $data->plan_approved_by ? '✅<br><small class="text-bold">'.$data?->plan_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_approved_at)->diffForHumans().'</small>': '';
                $nestedData['plan_legalized_by'] = $data->plan_legalized_by ? '✅<br><small class="text-bold">'.$data?->plan_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_legalized_at)->diffForHumans().'</small>': '';
                $nestedData['actual_checked_by'] = $data->actual_checked_by ? '✅<br><small class="text-bold">'.$data?->actual_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_checked_at)->diffForHumans().'</small>': '';
                $nestedData['actual_approved_by'] = $data->actual_approved_by ? '✅<br><small class="text-bold">'.$data?->actual_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_approved_at)->diffForHumans().'</small>': '';
                $nestedData['actual_legalized_by'] = $data->actual_legalized_by ? '✅<br><small class="text-bold">'.$data?->actual_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_legalized_at)->diffForHumans().'</small>': '';
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-info btnDetail" data-id-lembur="'.$data->id_lembur.'" data-is-member="'.($is_member ? 'true' : 'false').'"><i class="fas fa-eye"></i> Detail</button>
                    '.($data->status == 'PLANNED' && $data->issued_by == auth()->user()->karyawan->id_karyawan ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-check-circle"></i> Done</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnRejectLembur" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-times-circle"></i> Cancel</button>' : '').'
                    '.($data->plan_checked_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id-lembur="'.$data->id_lembur.'"><i class="fas fa-edit"></i> Edit</button>' : '').'
                    '.($data->plan_checked_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id-lembur="'.$data->id_lembur.'"><i class="fas fa-trash"></i> Delete</button>' : '').'
                </div>';

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

    public function approval_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'lemburs.id_lembur',
            1 => 'lemburs.issued_date',
            3 => 'karyawans.nama',
            4 => 'departemens.nama',
            5 => 'lemburs.jenis_hari',
            6 => 'lemburs.total_durasi',
            8 => 'lemburs.status',
            9 => 'lemburs.plan_checked_by',
            10=> 'lemburs.plan_approved_by',
            11 => 'lemburs.plan_legalized_by',
            12 => 'lemburs.actual_checked_by',
            13 => 'lemburs.actual_approved_by',
            14 => 'lemburs.actual_legalized_by'
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

        $organisasi_id = auth()->user()->organisasi_id;
        $posisi = auth()->user()?->karyawan?->posisi;
        $is_can_legalized = false;
        $is_can_checked = false;
        $is_can_approved = false;
        $is_has_department_head = false;

        if(auth()->user()->hasRole('personalia')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            // $dataFilter['is_legalized'] = true;
            $is_can_legalized = true;
        } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3){
            $member_posisi_ids = $this->get_member_posisi($posisi);
            $dataFilter['member_posisi_ids'] = $member_posisi_ids;
            $is_can_checked = true;
            $is_has_department_head = $this->has_department_head($posisi);
            // $dataFilter['is_checked'] = true;
        }  elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_approved = true;
            // $dataFilter['is_approved'] = true;
        }

        $filterUrutan = $request->urutan;
        if (!empty($filterUrutan)) {
            $dataFilter['urutan'] = $filterUrutan;
        }

        $filterJenisHari = $request->jenisHari;
        if (!empty($filterJenisHari)) {
            $dataFilter['jenisHari'] = $filterJenisHari;
        }

        $filterAksi = $request->aksi;
        if (!empty($filterAksi)) {
            $dataFilter['aksi'] = $filterAksi;
        }

        $filterMustChecked = $request->mustChecked;
        if ($filterMustChecked) {
            $dataFilter['mustChecked'] = $filterMustChecked;
        }

        $filterDepartemen = $request->departemen;
        if ($filterDepartemen) {
            $dataFilter['departemen'] = $filterDepartemen;
        }

        $filterStatus = $request->status;
        if (!empty($filterStatus)) {
            $dataFilter['status'] = $filterStatus;
        }

        $totalData = Lembure::all()->count();
        $totalFiltered = $totalData;

        $lembure = Lembure::getData($dataFilter, $settings);
        $totalFiltered = Lembure::countData($dataFilter);
        $dataTable = [];

        if (!empty($lembure)) {
            foreach ($lembure as $data) {
                $jam = floor($data->total_durasi / 60);
                $menit = $data->total_durasi % 60;
                $tanggal_lembur = Carbon::parse(DetailLembur::where('lembur_id', $data->id_lembur)->first()->rencana_mulai_lembur)->format('Y-m-d');
                $total_nominal = $data->detailLembur->where('is_aktual_approved', 'Y')->sum('nominal');
                $rejected = false;

                //STYLE STATUS
                if($data->status == 'WAITING'){
                    $status = '<span class="badge badge-warning">WAITING</span>';
                } elseif ($data->status == 'PLANNED'){
                    $status = '<span class="badge badge-info">PLANNED</span>';
                } elseif ($data->status == 'COMPLETED'){
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                } else {
                    $rejected = true;
                    $status = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'-'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                //BUTTON ACTION DATATABLE
                $button_checked_plan = '';
                $button_approved_plan = '';
                $button_legalized_plan = '';
                $button_checked_actual = '';
                $button_approved_actual = '';
                $button_legalized_actual = '';

                $is_planned = true;
                if($data->status == 'WAITING'){
                    $is_planned = false;
                }

                //TOMBOL CHECKED
                if($is_can_checked){
                     //BUTTON CHECKED DI SISI SECTION HEAD / DEPT HEAD
                    if($is_has_department_head){
                        //BEFORE PLANNED
                        if($data->plan_checked_by == null){
                            $button_checked_plan = 'MUST CHECKED BY DEPT.HEAD';
                        } else {
                            $button_checked_plan = '✅<br><small class="text-bold">'.$data?->plan_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_checked_at)->diffForHumans().'</small>';
                        }

                        //AFTER PLANNED
                        if($data->status == 'COMPLETED' && $data->actual_checked_by == null){
                            $button_checked_actual = 'MUST CHECKED BY DEPT.HEAD';
                        } elseif ($data->status == 'COMPLETED' && $data->actual_checked_by !== null) {
                            $button_checked_actual = '✅<br><small class="text-bold">'.$data?->actual_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_checked_at)->diffForHumans().'</small>';
                        }
                    } else {
                         //BEFORE PLANNED
                        if($data->plan_checked_by == null){
                            $button_checked_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="far fa-check-circle"></i> Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } else {
                            $button_checked_plan = '✅<br><small class="text-bold">'.$data?->plan_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_checked_at)->diffForHumans().'</small>';
                        }

                        //AFTER PLANNED
                        if($data->status == 'COMPLETED' && $data->actual_checked_by == null){
                            $button_checked_actual = '<button class="btn btn-sm btn-success btnCheckedAktual" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="far fa-check-circle"></i> Checked</button>';
                        } elseif ($data->status == 'COMPLETED' && $data->actual_checked_by !== null) {
                            $button_checked_actual = '✅<br><small class="text-bold">'.$data?->actual_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_checked_at)->diffForHumans().'</small>';
                        }
                    }

                    //BEFORE PLANNED
                    //BUTTON APPROVED DI SISI SECTION HEAD / DEPT HEAD
                    if($data->plan_approved_by !== null){
                        $button_approved_plan = '✅<br><small class="text-bold">'.$data?->plan_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_approved_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON APPROVED DI SISI SECTION HEAD / DEPT HEAD
                    if($data->actual_approved_by !== null){
                        $button_approved_actual = '✅<br><small class="text-bold">'.$data?->actual_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_approved_at)->diffForHumans().'</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON LEGALIZED DI SISI SECTION HEAD / DEPT HEAD
                    if($data->plan_legalized_by !== null){
                        $button_legalized_plan = '✅<br><small class="text-bold">'.$data?->plan_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_legalized_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON LEGALIZED DI SISI SECTION HEAD / DEPT HEAD
                    if($data->actual_legalized_by !== null){
                        $button_legalized_actual = '✅<br><small class="text-bold">'.$data?->actual_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_legalized_at)->diffForHumans().'</small>';
                    }
                }

                //TOMBOL APPROVED
                if($is_can_approved){
                    //BEFORE PLANNED
                    //BUTTON CHECKED DI SISI PLANT HEAD
                    if($data->plan_checked_by !== null){
                        $button_checked_plan = '✅<br><small class="text-bold">'.$data?->plan_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_checked_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON CHECKED DI SISI PLANT HEAD
                    if($data->actual_checked_by !== null){
                        $button_checked_actual = '✅<br><small class="text-bold">'.$data?->plan_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_checked_at)->diffForHumans().'</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON APPROVED DI SISI PLANT HEAD
                    if($data->plan_approved_by == null){
                        if($data->plan_checked_by !== null){
                            $button_approved_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 

                        //APPROVAL LANGSUNG OLEH PLANT HEAD JIKA USER YANG MEMBUAT DOKUMEN TIDAK PUNYA DEPT.HEAD
                        if(!$this->has_department_head($data->issued->posisi) && !$this->has_section_head($data->issued->posisi) && $data->plan_checked_by == null){
                            $button_approved_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    } else {
                        //BEFORE PLANNED
                        $button_approved_plan = '✅<br><small class="text-bold">'.$data?->plan_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_approved_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON APPROVED DI SISI PLANT HEAD
                    if($data->actual_approved_by == null){
                        if($data->status == 'COMPLETED' && $data->actual_checked_by !== null){
                            $button_approved_actual = '<button class="btn btn-sm btn-success btnApprovedAktual" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-thumbs-up"></i> Approved</button>';
                        } 

                        if($data->status == 'COMPLETED' && !$this->has_department_head($data->issued->posisi) && $data->actual_checked_by == null){
                            $button_approved_actual = '<button class="btn btn-sm btn-success btnApprovedAktual" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-thumbs-up"></i> Approved</button>';
                        }
                    } else {
                        //AFTER PLANNED
                        $button_approved_actual = '✅<br><small class="text-bold">'.$data?->actual_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_approved_at)->diffForHumans().'</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON LEGALIZED DI SISI PLANT HEAD
                    if($data->plan_legalized_by !== null){
                        $button_legalized_plan = '✅<br><small class="text-bold">'.$data?->plan_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_legalized_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON LEGALIZED DI SISI PLANT HEAD
                    if($data->actual_legalized_by !== null){
                        $button_legalized_actual = '✅<br><small class="text-bold">'.$data?->actual_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_legalized_at)->diffForHumans().'</small>';
                    }
                }

                //TOMBOL APPROVED
                if($is_can_legalized){
                    //BEFORE PLANNED
                    //BUTTON CHECKED DI SISI PERSONALIA
                    if($data->plan_checked_by !== null){
                        $button_checked_plan = '✅<br><small class="text-bold">'.$data?->plan_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_checked_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON CHECKED DI SISI PERSONALIA
                    if($data->actual_checked_by !== null){
                        $button_checked_actual = '✅<br><small class="text-bold">'.$data?->actual_checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_checked_at)->diffForHumans().'</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON APPROVED DI SISI PERSONALIA
                    if($data->plan_approved_by !== null){
                        $button_approved_plan = '✅<br><small class="text-bold">'.$data?->plan_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_approved_at)->diffForHumans().'</small>';
                    } 

                    //AFTER PLANNED
                    //BUTTON APPROVED DI SISI PERSONALIA
                    if($data->actual_approved_by !== null){
                        $button_approved_actual = '✅<br><small class="text-bold">'.$data?->actual_approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_approved_at)->diffForHumans().'</small>';
                    } 

                    //BEFORE PLANNED
                    //BUTTON LEGALIZED DI SISI PERSONALIA
                    if($data->plan_legalized_by == null){
                        if($data->plan_approved_by !== null){
                            $button_legalized_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-balance-scale"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } 
                    } else {
                        //BEFORE PLANNED
                        $button_legalized_plan = '✅<br><small class="text-bold">'.$data?->plan_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->plan_legalized_at)->diffForHumans().'</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON LEGALIZED DI SISI PERSONALIA
                    if($data->actual_legalized_by == null){
                        if($data->actual_approved_by !== null){
                            $button_legalized_actual = '<button class="btn btn-sm btn-success btnLegalizedAktual" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-balance-scale"></i> Legalized</button>';
                        } 
                    } else {
                        //AFTER PLANNED
                        $button_legalized_actual = '✅<br><small class="text-bold">'.$data?->actual_legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->actual_legalized_at)->diffForHumans().'</small>';
                    }
                }

                $nestedData['id_lembur'] = $data->id_lembur;
                $nestedData['issued_date'] = Carbon::parse($data->issued_date)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['rencana_mulai_lembur'] = Carbon::parse($data->detailLembur[0]->rencana_mulai_lembur)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['issued_by'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data?->nama_departemen;
                $nestedData['jenis_hari'] = $data->jenis_hari;
                $nestedData['total_durasi'] = $jam . ' Jam ' . $menit . ' Menit';
                $nestedData['total_nominal'] = 'Rp. ' . number_format($total_nominal, 0, ',', '.');
                $nestedData['status'] = $status;
                $nestedData['plan_checked_by'] = !$rejected ? $button_checked_plan : '';
                $nestedData['plan_approved_by'] = !$rejected ? $button_approved_plan : '';
                $nestedData['plan_legalized_by'] = !$rejected ? $button_legalized_plan : '';
                $nestedData['actual_checked_by'] =  !$rejected ? $button_checked_actual : '';
                $nestedData['actual_approved_by'] = !$rejected ? $button_approved_actual : '';
                $nestedData['actual_legalized_by'] = !$rejected ? $button_legalized_actual : '';
                $nestedData['action'] = '<button type="button" class="waves-effect waves-light btn btn-sm btn-info btnDetail" data-id-lembur="'.$data->id_lembur.'"><i class="fas fa-eye"></i> Detail</button>';

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

    public function detail_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'detail_lemburs.lembur_id',
            1 => 'karyawans.nama',
            2 => 'posisis.nama',
            3 => 'departemens.nama',
            4 => 'detail_lemburs.aktual_mulai_lembur',
            5 => 'detail_lemburs.aktual_selesai_lembur',
            6 => 'detail_lemburs.durasi',
            7 => 'detail_lemburs.nominal',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = (!empty($request->input('order.0.column'))) ? $columns[$request->input('order.0.column')] : $columns[0];
        $dir = (!empty($request->input('order.0.dir'))) ? $request->input('order.0.dir') : "ASC";

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $filterPeriode = $request->periode;
        if(isset($filterPeriode)){
            $dataFilter['month'] = Carbon::parse($filterPeriode)->format('m');
            $dataFilter['year'] = Carbon::parse($filterPeriode)->format('Y');
        } else {
            $dataFilter['month'] = date('m');
            $dataFilter['year'] = date('Y');
        }

        if (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3)){
            $posisi = auth()->user()->karyawan->posisi;
            $member_posisi_ids = $this->get_member_posisi($posisi);
            $dataFilter['member_posisi_ids'] = $member_posisi_ids;
        } 

        $filterDepartemen = $request->departemen;
        if(isset($filterDepartemen)){
            $dataFilter['departemen'] = $filterDepartemen;
        }

        $totalData = DetailLembur::all()->count();
        $totalFiltered = $totalData;

        $leaderboard = DetailLembur::getData($dataFilter, $settings);
        $totalFiltered = DetailLembur::countData($dataFilter);
        $dataTable = [];

        if (!empty($leaderboard)) {

            foreach ($leaderboard as $data) {
                $jam = floor($data->durasi / 60);
                $menit = $data->durasi % 60;

                $nestedData['lembur_id'] = $data->lembur_id;
                $nestedData['nama'] = $data->nama;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['departemen'] = $data->departemen ?? $data->divisi ?? '-';
                $nestedData['mulai'] = Carbon::parse($data->aktual_mulai_lembur)->format('Y-m-d H:i');
                $nestedData['selesai'] = Carbon::parse($data->aktual_selesai_lembur)->format('Y-m-d H:i');
                $nestedData['durasi'] = $jam.' jam '.$menit.' menit';
                $nestedData['nominal'] = 'Rp. ' . number_format($data->nominal, 0, ',', '.');

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

    public function setting_upah_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'karyawans.ni_karyawan',
            1 => 'divisis.nama',
            2 => 'departemens.nama',
            3 => 'karyawans.nama',
            4 => 'setting_lembur_karyawans.gaji',
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

        $totalData = SettingLemburKaryawan::all()->count();
        $totalFiltered = $totalData;

        $setting_upah_lembur = SettingLemburKaryawan::getData($dataFilter, $settings);
        $totalFiltered = SettingLemburKaryawan::countData($dataFilter);

        $dataTable = [];

        if (!empty($setting_upah_lembur)) {
            foreach ($setting_upah_lembur as $data) {
                $nestedData['ni_karyawan'] = $data->ni_karyawan;
                $nestedData['divisi'] = $data->divisi ?? '-';
                $nestedData['departemen'] = $data->departemen ?? '-';
                $nestedData['nama'] = $data->nama;
                $nestedData['gaji'] = '
                    <div class="input-group mb-3">
                        <input type="number" value="' . ($data->gaji ?? 0) . '" min="0" class="form-control inputUpahLembur"/>
                        <button class="btn btn-warning updateUpahLembur" type="button" data-id-setting-lembur-karyawan="' . $data->id_setting_lembur_karyawan . '" data-karyawan-id="'.$data->id_karyawan.'"><i class="fas fa-save"></i></button>
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

    public function setting_gaji_departemen_datatable(Request $request)
    {
        $columns = array(
            0 => 'departemens.nama',
            1 => 'gaji_departemens.periode',
            2 => 'gaji_departemens.nominal_batas_lembur',
            4 => 'gaji_departemens.total_gaji',
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

        $totalData = GajiDepartemen::all()->count();
        $totalFiltered = $totalData;

        $setting_upah_lembur = GajiDepartemen::getData($dataFilter, $settings);
        $totalFiltered = GajiDepartemen::countData($dataFilter);

        $dataTable = [];

        if (!empty($setting_upah_lembur)) {
            $count = 0;
            foreach ($setting_upah_lembur as $data) {
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['periode'] = Carbon::parse($data->periode)->format('F Y');
                $nestedData['nominal_batas_lembur'] = 'Rp. ' . number_format($data->nominal_batas_lembur, 0, ',', '.');
                $nestedData['presentase'] = '
                    <div class="input-group mb-3">
                        <input type="number" value="' . ($data->presentase ?? 0) . '" min="0" class="form-control inputGajiDepartemen" id="presentase_'.$count.'"/>
                    </div>
                ';
                $nestedData['total_gaji'] = '
                    <div class="input-group mb-3">
                        <input type="number" value="' . ($data->total_gaji ?? 0) . '" min="0" class="form-control inputGajiDepartemen" id="total_gaji_'.$count.'"/>
                        <button class="btn btn-warning updateGajiDepartemen" type="button" data-id-gaji-departemen="' . $data->id_gaji_departemen . '" data-departemen-id="'.$data->departemen_id.'" data-urutan="'.$count.'"><i class="fas fa-save"></i></button>
                    </div>
                ';

                $dataTable[] = $nestedData;
                $count++;
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
            'jenis_hari' => ['required','in:WD,WE'],
            'karyawan_id.*' => ['required', 'distinct'],
            'job_description.*' => ['required'],
            'rencana_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:rencana_selesai_lembur.*', 'after_or_equal:today'],
            'rencana_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:rencana_mulai_lembur.*'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $jenis_hari = $request->jenis_hari;
        $karyawan_ids = $request->karyawan_id;
        $job_descriptions = $request->job_description;
        $rencana_mulai_lemburs = $request->rencana_mulai_lembur;
        $rencana_selesai_lemburs = $request->rencana_selesai_lembur;
        $issued_by = auth()->user()->karyawan->id_karyawan;
        $organisasi_id = auth()->user()->organisasi_id;
        $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;
        $divisi_id = auth()->user()->karyawan->posisi[0]->divisi_id;

        DB::beginTransaction();
        try {

            $date = Carbon::parse($rencana_mulai_lemburs[0])->format('Y-m-d');
            foreach ($rencana_mulai_lemburs as $key => $start) {
                if (Carbon::parse($start)->format('Y-m-d') !== $date) {
                    DB::rollback();
                    return response()->json(['message' => 'Seluruh rencana mulai lembur harus berada pada tanggal yang sama!'], 402);
                }
            }

            $header = Lembure::create([
                'id_lembur' => 'LEMBUR-' . Str::random(4).'-'. date('YmdHis'),
                'issued_by' => $issued_by,
                'issued_date' => now(),
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'divisi_id' => $divisi_id,
                'jenis_hari' => $jenis_hari
            ]);

            if(auth()->user()->karyawan->posisi[0]->jabatan_id >= 5){
                if(!$this->has_department_head(auth()->user()->karyawan->posisi) && !$this->has_section_head(auth()->user()->karyawan->posisi)){
                    $checked_by = auth()->user()->karyawan->nama;
                    $header->update([
                        'plan_checked_by' => $checked_by,
                        'plan_checked_at' => now(),
                    ]);
                }
            }

            if(auth()->user()->karyawan->posisi[0]->jabatan_id == 4){
                if(!$this->has_department_head(auth()->user()->karyawan->posisi)){
                    $checked_by = auth()->user()->karyawan->nama;
                    $header->update([
                        'plan_checked_by' => $checked_by,
                        'plan_checked_at' => now(),
                    ]);
                }
            }

            if(auth()->user()->karyawan->posisi[0]->jabatan_id == 3){
                $checked_by = auth()->user()->karyawan->nama;
                $header->update([
                    'plan_checked_by' => $checked_by,
                    'plan_checked_at' => now(),
                    'actual_checked_by' => $checked_by,
                    'actual_checked_at' => now(),
                ]);
            }

            if(auth()->user()->karyawan->posisi[0]->jabatan_id <= 2){
                $checked_and_approved = auth()->user()->karyawan->nama;
                $header->update([
                    'status' => 'COMPLETED',
                    'plan_checked_by' => $checked_and_approved,
                    'plan_checked_at' => now(),
                    'plan_approved_by' => $checked_and_approved,
                    'plan_approved_at' => now(),
                    'plan_legalized_by' => 'HRD & GA',
                    'plan_legalized_at' => now(),
                    'actual_checked_by' => $checked_and_approved,
                    'actual_checked_at' => now(),
                    'actual_approved_by' => $checked_and_approved,
                    'actual_approved_at' => now(),
                ]);
            }

            $total_durasi = 0;
            $total_nominal = 0;
            $data_detail_lembur = [];
            foreach ($karyawan_ids as $key => $karyawan_id) {
                $karyawan = Karyawan::find($karyawan_id);
                $gaji_lembur = $karyawan->settingLembur->gaji;
                $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $karyawan->user->organisasi_id)->first()->value;
                $datetime_rencana_mulai_lembur = $this->pembulatan_menit_ke_bawah($rencana_mulai_lemburs[$key]);
                $datetime_rencana_selesai_lembur = $this->pembulatan_menit_ke_bawah($rencana_selesai_lemburs[$key]);
                $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $karyawan_id);
                $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $karyawan_id);

                if($durasi < 60){
                    DB::rollback();
                    return response()->json(['message' => 'Durasi lembur '.$karyawan->nama.' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                }

                if(!$karyawan->posisi()->exists()){
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama.' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
                }

                $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id);
                $data_detail_lembur[] = [
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $karyawan->user->organisasi_id,
                    'departemen_id' => $karyawan->posisi[0]?->departemen_id,
                    'divisi_id' => $karyawan->posisi[0]?->divisi_id,
                    'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'deskripsi_pekerjaan' => $job_descriptions[$key],
                    'durasi_istirahat' => $durasi_istirahat,
                    'durasi_konversi_lembur' => $durasi_konversi_lembur,
                    'gaji_lembur' => $gaji_lembur,
                    'pembagi_upah_lembur' => $pembagi_upah_lembur,
                    'uang_makan' => $uang_makan,
                    'durasi' => $durasi,
                    'nominal' => $nominal
                ];

                $total_durasi += $durasi;
                $total_nominal += $nominal;
            }
            
            $header->detailLembur()->createMany($data_detail_lembur);
            
            //Update Total Durasi Lagi
            $header->update(['total_durasi' => $total_durasi]);

            if(auth()->user()->karyawan->posisi[0]->jabatan_id <= 2){
                $header->update(['total_nominal' => $total_nominal]);
            }

            DB::commit();
            return response()->json(['message' => 'Lembur Berhasil Dibuat'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //FUNGSI MENGHITUNG DURASI LEMBUR YANG SUDAH DIKURANGI DENGAN ISTIRAHAT
    public function calculate_overtime_per_minutes($datetime_start, $datetime_end, $organisasi_id)
    {
        //Kondisi Istirahat ketika lembur
        $start = Carbon::parse($datetime_start);
        $end = Carbon::parse($datetime_end);
        $duration = $start->diffInMinutes($end);

        $setting_lembur = SettingLembur::where('organisasi_id', $organisasi_id)->get();
        $jam_istirahat_mulai_1 = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_1')->first()->value;
        $jam_istirahat_selesai_1 = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_1')->first()->value;
        $jam_istirahat_mulai_2 = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_2')->first()->value;
        $jam_istirahat_selesai_2 = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_2')->first()->value;
        $jam_istirahat_mulai_3 = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_3')->first()->value;
        $jam_istirahat_selesai_3 = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_3')->first()->value;
        $jam_istirahat_mulai_jumat = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_jumat')->first()->value;
        $jam_istirahat_selesai_jumat = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_jumat')->first()->value;
        $durasi_istirahat_1 = $setting_lembur->where('setting_name', 'durasi_istirahat_1')->first()->value;
        $durasi_istirahat_2 = $setting_lembur->where('setting_name', 'durasi_istirahat_2')->first()->value;
        $durasi_istirahat_3 = $setting_lembur->where('setting_name', 'durasi_istirahat_3')->first()->value;
        $durasi_istirahat_jumat = $setting_lembur->where('setting_name', 'durasi_istirahat_jumat')->first()->value;

        // Setting Istirahat ketika lembur (Hari jumat memiliki perbedaan)
        if ($start->isFriday()) {
            $breaks = [
                ['start' => $jam_istirahat_mulai_jumat, 'end' => $jam_istirahat_selesai_jumat, 'duration' => $durasi_istirahat_jumat],
                ['start' => $jam_istirahat_mulai_2, 'end' => $jam_istirahat_selesai_2, 'duration' => $durasi_istirahat_2],
                ['start' => $jam_istirahat_mulai_3, 'end' => $jam_istirahat_selesai_3, 'duration' => $durasi_istirahat_3],
            ];
        } else {
            $breaks = [
                ['start' => $jam_istirahat_mulai_1, 'end' => $jam_istirahat_selesai_1, 'duration' => $durasi_istirahat_1],
                ['start' => $jam_istirahat_mulai_2, 'end' => $jam_istirahat_selesai_2, 'duration' => $durasi_istirahat_2],
                ['start' => $jam_istirahat_mulai_3, 'end' => $jam_istirahat_selesai_3, 'duration' => $durasi_istirahat_3],
            ];
        }

        // Adjust duration for each break period
        foreach ($breaks as $break) {

            // Kondisi jika lintas hari
            if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
                if($start->format('H:i') > $break['start']){
                    $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start'])->addDay();
                    $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end'])->addDay();
                } else {
                    $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
                    $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);
                }
            } else {
                $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
                $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);
            }

            // If the break period spans over midnight, adjust the dates
            // if ($breakStart->greaterThan($breakEnd)) {
            //     $breakEnd->addDay();
            // }
            
            if ($start->lessThanOrEqualTo($breakEnd) && $end->greaterThanOrEqualTo($breakStart)) {
                if ($start->lessThanOrEqualTo($breakStart) && $end->greaterThanOrEqualTo($breakEnd)) {
                    $duration -= $break['duration'];
                } elseif ($start->lessThan($breakStart) && $end->lessThan($breakEnd)) {
                    $duration -= abs($end->diffInMinutes($breakStart));
                } elseif ($start->greaterThan($breakStart) && $end->greaterThan($breakEnd)) {
                    $duration -= abs($breakEnd->diffInMinutes($start));
                } else {
                    $duration -= abs($end->diffInMinutes($start));
                }
            }
        }

        //OLD
        // foreach ($breaks as $break) {
        //     $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
        //     $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);

        //     // If the break period spans over midnight, adjust the dates
        //     if ($breakStart->greaterThan($breakEnd)) {
        //         $breakEnd->addDay();
        //     }

        //     //Hasil Revisi
        //     if ($start->lessThanOrEqualTo($breakEnd) && $end->greaterThanOrEqualTo($breakStart)) {
        //         if ($start->lessThanOrEqualTo($breakStart) && $end->greaterThanOrEqualTo($breakEnd)) {
        //             $duration -= $break['duration'];
        //         } elseif ($start->lessThan($breakStart) && $end->lessThan($breakEnd)) {
        //             $duration -= abs($end->diffInMinutes($breakStart));
        //         } elseif ($start->greaterThan($breakStart) && $end->greaterThan($breakEnd)) {
        //             $duration -= abs($breakEnd->diffInMinutes($start));
        //         } else {
        //             $duration -= abs($end->diffInMinutes($start));
        //         }
        //     }
        // }        

        // memastikan bukan negatif
        $duration = intval($duration);
        return $duration;
    }

    public function overtime_resttime_per_minutes($datetime_start, $datetime_end, $organisasi_id)
    {
        $start = Carbon::parse($datetime_start);
        $end = Carbon::parse($datetime_end);
        $duration = $start->diffInMinutes($end);
        $rest_time = 0;

        $setting_lembur = SettingLembur::where('organisasi_id', $organisasi_id)->get();
        $jam_istirahat_mulai_1 = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_1')->first()->value;
        $jam_istirahat_selesai_1 = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_1')->first()->value;
        $jam_istirahat_mulai_2 = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_2')->first()->value;
        $jam_istirahat_selesai_2 = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_2')->first()->value;
        $jam_istirahat_mulai_3 = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_3')->first()->value;
        $jam_istirahat_selesai_3 = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_3')->first()->value;
        $jam_istirahat_mulai_jumat = $setting_lembur->where('setting_name', 'jam_istirahat_mulai_jumat')->first()->value;
        $jam_istirahat_selesai_jumat = $setting_lembur->where('setting_name', 'jam_istirahat_selesai_jumat')->first()->value;
        $durasi_istirahat_1 = $setting_lembur->where('setting_name', 'durasi_istirahat_1')->first()->value;
        $durasi_istirahat_2 = $setting_lembur->where('setting_name', 'durasi_istirahat_2')->first()->value;
        $durasi_istirahat_3 = $setting_lembur->where('setting_name', 'durasi_istirahat_3')->first()->value;
        $durasi_istirahat_jumat = $setting_lembur->where('setting_name', 'durasi_istirahat_jumat')->first()->value;

        // Setting Istirahat ketika lembur (Hari jumat memiliki perbedaan)
        if ($start->isFriday()) {
            $breaks = [
                ['start' => $jam_istirahat_mulai_jumat, 'end' => $jam_istirahat_selesai_jumat, 'duration' => $durasi_istirahat_jumat],
                ['start' => $jam_istirahat_mulai_2, 'end' => $jam_istirahat_selesai_2, 'duration' => $durasi_istirahat_2],
                ['start' => $jam_istirahat_mulai_3, 'end' => $jam_istirahat_selesai_3, 'duration' => $durasi_istirahat_3],
            ];
        } else {
            $breaks = [
                ['start' => $jam_istirahat_mulai_1, 'end' => $jam_istirahat_selesai_1, 'duration' => $durasi_istirahat_1],
                ['start' => $jam_istirahat_mulai_2, 'end' => $jam_istirahat_selesai_2, 'duration' => $durasi_istirahat_2],
                ['start' => $jam_istirahat_mulai_3, 'end' => $jam_istirahat_selesai_3, 'duration' => $durasi_istirahat_3],
            ];
        }

        foreach ($breaks as $break) {
            // $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
            // $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);
            if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
                if($start->format('H:i') > $break['start']){
                    $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start'])->addDay();
                    $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end'])->addDay();
                } else {
                    $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
                    $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);
                }
            } else {
                $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
                $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);
            }

            //Revisi
            if ($start->lessThanOrEqualTo($breakEnd) && $end->greaterThanOrEqualTo($breakStart)) {
                if ($start->lessThanOrEqualTo($breakStart) && $end->greaterThanOrEqualTo($breakEnd)) {
                    $rest_time += $break['duration'];
                } elseif ($start->lessThan($breakStart) && $end->lessThan($breakEnd)) {
                    $rest_time += abs($end->diffInMinutes($breakStart));
                } elseif ($start->greaterThan($breakStart) && $end->greaterThan($breakEnd)) {
                    $rest_time += abs($breakEnd->diffInMinutes($start));
                } else {
                    $rest_time += abs($end->diffInMinutes($start));
                }
            }
        }

        return intval($rest_time);
    }

    public function calculate_overtime_uang_makan($jenis_hari, $durasi, $karyawan_id)
    {
        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $karyawan_id)->first();
        $karyawan = Karyawan::find($karyawan_id); 
        $organisasi_id = $karyawan->user->organisasi_id;
        $jabatan_id = $karyawan->posisi[0]->jabatan_id;
        $convert_duration = number_format($durasi / 60, 2);
        $uang_makan = SettingLembur::where('organisasi_id', $organisasi_id)->where('setting_name', 'uang_makan')->first()->value;

        if($jenis_hari == 'WD'){
            if($jabatan_id >= 5){
                if($convert_duration >= 4){
                    return $uang_makan;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            if($jabatan_id >= 5){
                if($convert_duration >= 7){
                    return $uang_makan;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    }

    //FUNGSI MENGHITUNG NOMINAL LEMBUR
    public function calculate_durasi_konversi_lembur($jenis_hari, $durasi, $karyawan_id)
    {
        $karyawan = Karyawan::find($karyawan_id); 
        $organisasi_id = $karyawan->user->organisasi_id;
        $convert_duration = number_format($durasi / 60, 2);
        $jabatan_id = $karyawan->posisi[0]->jabatan_id;

        if($jenis_hari == 'WD'){
            $jam_pertama = $convert_duration < 2 ? ($convert_duration * 1.5) : (1 * 1.5); 
            $jam_kedua = $convert_duration >= 2 ? ($convert_duration - 1) * 2 : 0;
            $durasi_konversi_lembur = $jam_pertama + $jam_kedua;
        } else {
            $delapan_jam_pertama = $convert_duration < 9 ? ($convert_duration * 2) : (8 * 2);
            $jam_ke_sembilan = $convert_duration >= 9 && $convert_duration < 10 ? (($convert_duration - 8) * 3) : ($convert_duration >= 10 ? 1 * 3 : 0);
            $jam_ke_sepuluh = $convert_duration >= 10 ? ($convert_duration - 9) * 4 : 0;
            $durasi_konversi_lembur = $delapan_jam_pertama + $jam_ke_sembilan + $jam_ke_sepuluh;
        }
        // return $durasi_konversi_lembur * 60;
        return floor($durasi_konversi_lembur * 60);
    }

    public function calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id)
    {
        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $karyawan_id)->first();
        $karyawan = Karyawan::find($karyawan_id); 
        $organisasi_id = $karyawan->user->organisasi_id;
        $convert_duration = number_format($durasi / 60, 2);
        $jabatan_id = $karyawan->posisi[0]->jabatan_id;
        $gaji_lembur_karyawan = $setting_lembur_karyawan->gaji;

        $setting_lembur = SettingLembur::where('organisasi_id', $organisasi_id)->get();
        $upah_sejam = $gaji_lembur_karyawan / $setting_lembur->where('setting_name', 'pembagi_upah_lembur_harian')->first()->value;
        $uang_makan = $setting_lembur->where('setting_name', 'uang_makan')->first()->value;
        $insentif_section_head_1 = $setting_lembur->where('setting_name', 'insentif_section_head_1')->first()->value;
        $insentif_section_head_2 = $setting_lembur->where('setting_name', 'insentif_section_head_2')->first()->value;
        $insentif_section_head_3 = $setting_lembur->where('setting_name', 'insentif_section_head_3')->first()->value;
        $insentif_section_head_4 = $setting_lembur->where('setting_name', 'insentif_section_head_4')->first()->value;
        $insentif_department_head_4 = $setting_lembur->where('setting_name', 'insentif_department_head_4')->first()->value;

        //PERHITUNGAN SESUAI JENIS HARI
        if($jenis_hari == 'WD'){

            //PERHITUNGAN UNTUK LEADER DAN STAFF
            if($jabatan_id >= 5){
                $jam_pertama = $convert_duration < 2 ? ($convert_duration * $upah_sejam * 1.5) : (1 * $upah_sejam * 1.5); 
                $jam_kedua = $convert_duration >= 2 ? ($convert_duration - 1) * $upah_sejam * 2 : 0;
                $nominal_lembur = $jam_pertama + $jam_kedua;

                if($convert_duration >= 4){
                    $nominal_lembur += $uang_makan;
                }

            //PERHITUNGAN UNTUK JABATAN LAINNYA
            } elseif ($jabatan_id == 4){
                if ($convert_duration >= 3){
                    $nominal_lembur = $insentif_section_head_3;
                } elseif ($convert_duration >= 2){
                    $nominal_lembur = $insentif_section_head_2;
                } elseif ($convert_duration >= 1){
                    $nominal_lembur = $insentif_section_head_1;
                } else {
                    $nominal_lembur = 0;
                }
            } else {
                $nominal_lembur = 0;
            }

            //WEEKDAY SECTION HEAD HANYA JAM KE 1,2,3 SEDANGKAN DEPT HEAD TIDAK ADA / 0 rupiah

        } else {
            //PERHITUNGAN UNTUK LEADER DAN STAFF
            if($jabatan_id >= 5){

                //lOGIC AFTER REVISI
                $delapan_jam_pertama = $convert_duration < 9 ? ($convert_duration * $upah_sejam * 2) : (8 * $upah_sejam * 2);
                $jam_ke_sembilan = $convert_duration >= 9 && $convert_duration < 10 ? (($convert_duration - 8) * $upah_sejam * 3) : ($convert_duration >= 10 ? $upah_sejam * 3 : 0);
                $jam_ke_sepuluh = $convert_duration >= 10 ? ($convert_duration - 9) * $upah_sejam * 4 : 0;
                $nominal_lembur = $delapan_jam_pertama + $jam_ke_sembilan + $jam_ke_sepuluh;

                if($convert_duration >= 7){
                    $nominal_lembur += $uang_makan;
                }

            //PERHITUNGAN UNTUK SECTION HEAD
            } elseif ($jabatan_id == 4){
                if ($convert_duration >= 4){
                    $nominal_lembur = $insentif_section_head_4;
                } elseif ($convert_duration >= 3){
                    $nominal_lembur = $insentif_section_head_3;
                } elseif ($convert_duration >= 2){
                    $nominal_lembur = $insentif_section_head_2;
                } elseif ($convert_duration >= 1){
                    $nominal_lembur = $insentif_section_head_1;
                } else {
                    $nominal_lembur = 0;
                }

            //PERHITUNGAN UNTUK DEPARTEMEN HEAD
            } elseif ($jabatan_id == 3) {
                if ($convert_duration >= 4){
                    $nominal_lembur = $insentif_department_head_4;
                } else {
                    $nominal_lembur = 0;
                }
            
            //PERHITUNGAN UNTUK PLANT HEAD
            } else {
                $nominal_lembur = 0;
            }
        }

        return intval($nominal_lembur);

    }

    public function pembulatan_menit_ke_bawah($datetime)
    {
        //OLD VERSION DATE
        $datetime = Carbon::createFromFormat('Y-m-d\TH:i', $datetime);
        // $datetime = Carbon::parse($datetime);
        $minute = $datetime->minute;
        $minute = $minute - ($minute % 15);
        $datetime->minute($minute)->second(0);
        return $datetime->toDateTimeString();
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
    public function update(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'jenis_hariEdit' => ['required','in:WD,WE'],
            'karyawan_idEdit.*' => ['required', 'distinct'],
            'job_descriptionEdit.*' => ['required'],
            'rencana_mulai_lemburEdit.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:rencana_selesai_lemburEdit.*'],
            'rencana_selesai_lemburEdit.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:rencana_mulai_lemburEdit.*'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
        
        $id_detail_lemburs = $request->id_detail_lemburEdit;
        $jenis_hari = $request->jenis_hariEdit;
        $karyawan_ids = $request->karyawan_idEdit;
        $job_descriptions = $request->job_descriptionEdit;
        $rencana_mulai_lemburs = $request->rencana_mulai_lemburEdit;
        $rencana_selesai_lemburs = $request->rencana_selesai_lemburEdit;
        $issued_by = auth()->user()->karyawan->id_karyawan;
        $organisasi_id = auth()->user()->organisasi_id;
        $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;

        $karyawan_ids_new = $request->karyawan_idEditNew;
        $job_descriptions_new = $request->job_descriptionEditNew;
        $rencana_mulai_lemburs_new = $request->rencana_mulai_lemburEditNew;
        $rencana_selesai_lemburs_new = $request->rencana_selesai_lemburEditNew;

        DB::beginTransaction();
        try {

            $lembur = Lembure::find($id_lembur);
            $total_durasi = 0;

            if($lembur){
                $lembur->jenis_hari = $jenis_hari;
                $lembur->save();
            } else {
                DB::rollback();
                return response()->json(['message' => 'ID Lembur tidak ditemukan, hubungi ICT'], 402);
            }

            if(isset($karyawan_ids_new) || isset($job_descriptions_new) || isset($rencana_mulai_lemburs_new) || isset($rencana_selesai_lemburs_new)){
                $dataValidate = [
                    'karyawan_idEditNew.*' => ['required', 'distinct'],
                    'job_descriptionEditNew.*' => ['required'],
                    'rencana_mulai_lemburEditNew.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:rencana_selesai_lemburEditNew.*'],
                    'rencana_selesai_lemburEditNew.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:rencana_mulai_lemburEditNew.*'],
                ];
        
                $validator = Validator::make(request()->all(), $dataValidate);
            
                if ($validator->fails()) {
                    $errors = $validator->errors()->all();
                    return response()->json(['message' => $errors], 402);
                }
    
                $data_detail_lembur_new = [];
                foreach ($karyawan_ids_new as $key => $karyawan_id_new) {
                    $karyawan_new = Karyawan::find($karyawan_id_new);
                    $gaji_lembur_new = $karyawan_new->settingLembur->gaji;
                    $pembagi_upah_lembur_new = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $karyawan_new->user->organisasi_id)->first()->value;
                    $datetime_rencana_mulai_lembur_new = $this->pembulatan_menit_ke_bawah($rencana_mulai_lemburs_new[$key]);
                    $datetime_rencana_selesai_lembur_new = $this->pembulatan_menit_ke_bawah($rencana_selesai_lemburs_new[$key]);
                    $durasi_istirahat_new = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur_new, $datetime_rencana_selesai_lembur_new, $karyawan_new->user->organisasi_id);
                    $durasi_new = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur_new, $datetime_rencana_selesai_lembur_new, $karyawan_new->user->organisasi_id);
                    $durasi_konversi_lembur_new = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi_new, $karyawan_id_new);
                    $uang_makan_new = $this->calculate_overtime_uang_makan($jenis_hari, $durasi_new, $karyawan_id_new);
    
                    if($durasi_new < 60){
                        DB::rollback();
                        return response()->json(['message' => 'Durasi lembur '.$karyawan_new->nama.' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                    }
    
                    if(!$karyawan_new->posisi()->exists()){
                        DB::rollback();
                        return response()->json(['message' => $karyawan_new->nama.' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
                    }
    
                    $nominal_new = $this->calculate_overtime_nominal($jenis_hari, $durasi_new, $karyawan_id_new);
                    $data_detail_lembur_new[] = [
                        'karyawan_id' => $karyawan_id_new,
                        'organisasi_id' => $karyawan_new->user->organisasi_id,
                        'departemen_id' => $karyawan_new->posisi[0]?->departemen_id,
                        'divisi_id' => $karyawan_new->posisi[0]?->divisi_id,
                        'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur_new,
                        'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur_new,
                        'deskripsi_pekerjaan' => $job_descriptions_new[$key],
                        'durasi_istirahat' => $durasi_istirahat_new,
                        'durasi_konversi_lembur' => $durasi_konversi_lembur_new,
                        'gaji_lembur' => $gaji_lembur_new,
                        'pembagi_upah_lembur' => $pembagi_upah_lembur_new,
                        'uang_makan' => $uang_makan_new,
                        'durasi' => $durasi_new,
                        'nominal' => $nominal_new
                    ];
    
                    $total_durasi+= $durasi_new;
                }
                
                $lembur->detailLembur()->createMany($data_detail_lembur_new);
            }

            foreach ($karyawan_ids as $key => $id_kry){
                $karyawan = Karyawan::find($id_kry);
                $gaji_lembur = $karyawan->settingLembur->gaji;
                $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $karyawan->user->organisasi_id)->first()->value;
                $datetime_rencana_mulai_lembur = $this->pembulatan_menit_ke_bawah($rencana_mulai_lemburs[$key]);
                $datetime_rencana_selesai_lembur = $this->pembulatan_menit_ke_bawah($rencana_selesai_lemburs[$key]);
                $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $id_kry);
                $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $id_kry);

                if($durasi < 60){
                    DB::rollback();
                    return response()->json(['message' => 'Durasi lembur '.$karyawan->nama.' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                }

                if(!$karyawan->posisi()->exists()){
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama.' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
                }

                $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $id_kry);
                $detailLembur = DetailLembur::find($id_detail_lemburs[$key]);
                $detailLembur->update([
                    'karyawan_id' => $id_kry,
                    'organisasi_id' => $karyawan->user->organisasi_id,
                    'departemen_id' => $karyawan->posisi[0]?->departemen_id,
                    'divisi_id' => $karyawan->posisi[0]?->divisi_id,
                    'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'deskripsi_pekerjaan' => $job_descriptions[$key],
                    'durasi_istirahat' => $durasi_istirahat,
                    'durasi_konversi_lembur' => $durasi_konversi_lembur,
                    'gaji_lembur' => $gaji_lembur,
                    'pembagi_upah_lembur' => $pembagi_upah_lembur,
                    'uang_makan' => $uang_makan,
                    'durasi' => $durasi,
                    'nominal' => $nominal
                ]);

                $total_durasi += $durasi;
            }

            //Update Total Durasi Lagi
            $lembur->update(['total_durasi' => $total_durasi]);

            DB::commit();
            return response()->json(['message' => 'Lembur Berhasil Diupdate!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update_setting_upah_lembur(Request $request)
    {
        $dataValidate = [
            'gaji' => ['required', 'numeric', 'min:174'],
            'karyawan_id' => ['required', 'exists:karyawans,id_karyawan'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try{
            $setting_lembur_karyawan = SettingLemburKaryawan::find($request->id_setting_lembur_karyawan);
            if($setting_lembur_karyawan){
                $setting_lembur_karyawan->gaji = $request->gaji;
                $setting_lembur_karyawan->save();
            } else {
                $karyawan = Karyawan::find($request->karyawan_id); 
                $posisi = $karyawan->posisi[0];

                if(!$karyawan){
                    DB::rollback();
                    return response()->json(['message' => 'Karyawan tidak ditemukan!'], 402);
                }

                if(!$posisi){
                    DB::rollback();
                    return response()->json(['message' => 'Karyawan belum memiliki posisi, hubungi HRD untuk setting posisi karyawan!'], 402);
                }

                SettingLemburKaryawan::create([
                    'karyawan_id' => $request->karyawan_id,
                    'organisasi_id' => $karyawan->user->organisasi_id,
                    'departemen_id' => $posisi?->departemen_id,
                    'jabatan_id' => $posisi->jabatan_id,
                    'gaji' => $request->gaji
                ]);
            }
            
            DB::commit();
            return response()->json(['message' => 'Upah Lembur Karyawan Berhasil di Update!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update_setting_lembur(Request $request)
    {
        $dataValidate = [
            'pembagi_upah_lembur_harian' => ['required', 'numeric', 'min:1'],
            'uang_makan' => ['required', 'numeric', 'min:0'],
            'insentif_section_head_1' => ['required', 'numeric', 'min:0'],
            'insentif_section_head_2' => ['required', 'numeric', 'min:0'],
            'insentif_section_head_3' => ['required', 'numeric', 'min:0'],
            'insentif_section_head_4' => ['required', 'numeric', 'min:0'],
            'insentif_department_head_4' => ['required', 'numeric', 'min:0'],
            'jam_istirahat_mulai_1' => ['required', 'date_format:H:i'],
            'jam_istirahat_selesai_1' => ['required', 'date_format:H:i', 'after:jam_istirahat_mulai_1'],
            'jam_istirahat_mulai_2' => ['required', 'date_format:H:i'],
            'jam_istirahat_selesai_2' => ['required', 'date_format:H:i', 'after:jam_istirahat_mulai_2'],
            'jam_istirahat_mulai_3' => ['required', 'date_format:H:i'],
            'jam_istirahat_selesai_3' => ['required', 'date_format:H:i', 'after:jam_istirahat_mulai_3'],
            'jam_istirahat_mulai_jumat' => ['required', 'date_format:H:i'],
            'jam_istirahat_selesai_jumat' => ['required', 'date_format:H:i', 'after:jam_istirahat_mulai_jumat'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $organisasi_id = auth()->user()->organisasi_id;
        $pembagi_upah_lembur_harian = $request->pembagi_upah_lembur_harian;
        $uang_makan = $request->uang_makan;
        $insentif_section_head_1 = $request->insentif_section_head_1;
        $insentif_section_head_2 = $request->insentif_section_head_2;
        $insentif_section_head_3 = $request->insentif_section_head_3;
        $insentif_section_head_4 = $request->insentif_section_head_4;
        $insentif_department_head_4 = $request->insentif_department_head_4;
        $jam_istirahat_mulai_1 = $request->jam_istirahat_mulai_1;
        $jam_istirahat_selesai_1 = $request->jam_istirahat_selesai_1;
        $jam_istirahat_mulai_2 = $request->jam_istirahat_mulai_2;
        $jam_istirahat_selesai_2 = $request->jam_istirahat_selesai_2;
        $jam_istirahat_mulai_3 = $request->jam_istirahat_mulai_3;
        $jam_istirahat_selesai_3 = $request->jam_istirahat_selesai_3;
        $jam_istirahat_mulai_jumat = $request->jam_istirahat_mulai_jumat;
        $jam_istirahat_selesai_jumat = $request->jam_istirahat_selesai_jumat;

        //Durasi Istirahat
        $durasi_istirahat_1 = intval(Carbon::parse($jam_istirahat_mulai_1)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_1)));
        $durasi_istirahat_2 = intval(Carbon::parse($jam_istirahat_mulai_2)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_2)));
        $durasi_istirahat_3 = intval(Carbon::parse($jam_istirahat_mulai_3)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_3)));
        $durasi_istirahat_jumat = intval(Carbon::parse($jam_istirahat_mulai_jumat)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_jumat)));

        DB::beginTransaction();
        try{
            $setting_lembur = SettingLembur::where('organisasi_id', $organisasi_id)->get();
            if($setting_lembur){
                $settings = [
                    'pembagi_upah_lembur_harian' => $pembagi_upah_lembur_harian,
                    'uang_makan' => $uang_makan,
                    'insentif_section_head_1' => $insentif_section_head_1,
                    'insentif_section_head_2' => $insentif_section_head_2,
                    'insentif_section_head_3' => $insentif_section_head_3,
                    'insentif_section_head_4' => $insentif_section_head_4,
                    'insentif_department_head_4' => $insentif_department_head_4,
                    'jam_istirahat_mulai_1' => $jam_istirahat_mulai_1,
                    'jam_istirahat_selesai_1' => $jam_istirahat_selesai_1,
                    'jam_istirahat_mulai_2' => $jam_istirahat_mulai_2,
                    'jam_istirahat_selesai_2' => $jam_istirahat_selesai_2,
                    'jam_istirahat_mulai_3' => $jam_istirahat_mulai_3,
                    'jam_istirahat_selesai_3' => $jam_istirahat_selesai_3,
                    'jam_istirahat_mulai_jumat' => $jam_istirahat_mulai_jumat,
                    'jam_istirahat_selesai_jumat' => $jam_istirahat_selesai_jumat,
                    'durasi_istirahat_1' => $durasi_istirahat_1,
                    'durasi_istirahat_2' => $durasi_istirahat_2,
                    'durasi_istirahat_3' => $durasi_istirahat_3,
                    'durasi_istirahat_jumat' => $durasi_istirahat_jumat,
                ];

                foreach ($settings as $key => $value) {
                    $setting_lembur->where('setting_name', $key)->first()->update(['value' => $value]);
                }
            } else {
                return response()->json(['message' => 'Setting Lembur tidak ditemukan!'], 402);
            }

            DB::commit();
            return response()->json(['message' => 'Setting Lembur Berhasil di Update!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store_setting_gaji_departemen(Request $request)
    {
        $dataValidate = [
            'periode' => ['required', 'date_format:Y-m']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $periode = $request->periode.'-01';

        DB::beginTransaction();
        try{
            $gaji_departemen_exist = GajiDepartemen::whereDate('periode', $periode)->exists();

            if($gaji_departemen_exist){
                DB::rollback();
                return response()->json(['message' => 'Gaji Departemen sudah ada, silahkan update nominalnya pada tabel!'], 402);
            }

            $departemens = Departemen::all();
            if($departemens){
                foreach($departemens as $dept){
                    GajiDepartemen::create([
                        'departemen_id' => $dept->id_departemen,
                        'organisasi_id' => auth()->user()->organisasi_id,
                        'periode' => $periode,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Gaji Departemen periode'.Carbon::parse($periode)->format('F Y').' Berhasil di Tambahkan!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update_setting_gaji_departemen(Request $request)
    {
        $dataValidate = [
            'total_gaji' => ['required', 'numeric', 'min:0'],
            'presentase' => ['required', 'numeric', 'min:0'],
            'id_gaji_departemen' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try{
            $gaji_departemen = GajiDepartemen::find($request->id_gaji_departemen);
            $presentase = $request->presentase;
            $nominal_batas_lembur = intval($request->total_gaji * ($presentase / 100));
            if($gaji_departemen){
                $gaji_departemen->total_gaji = $request->total_gaji;
                $gaji_departemen->presentase = $presentase;
                $gaji_departemen->nominal_batas_lembur = $nominal_batas_lembur;
                $gaji_departemen->save();
            } 
            
            DB::commit();
            return response()->json(['message' => 'Gaji Departemen Berhasil di Update!'], 200);
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


    //GET DATA KARYAWAN MEMBER DARI LEADER
    public function get_data_karyawan_lembur(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi',
        );

        $organisasi_id = auth()->user()->organisasi_id;
        $posisi = auth()->user()->karyawan->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps){
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }
        array_push($id_posisi_members, auth()->user()->karyawan->posisi[0]->id_posisi);


        if (!empty($search)) {
            if(auth()->user()->karyawan->posisi[0]->jabatan_id == 5){

                //Bug
                // $query->where('users.organisasi_id', $organisasi_id);
                // $query->whereIn('posisis.id_posisi', $id_posisi_members);
                // $query->where(function ($dat) use ($search) {
                //     $dat->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                //         ->whereIn('posisis.id_posisi', $id_posisi_members)
                //         ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
                // });
                // $query->orWhere(function ($dat) use ($search) {
                //     $dat->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                //         ->whereIn('posisis.id_posisi', $id_posisi_members)
                //         ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                //         ->orWhere('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
                // });

                //Sementara
                $query->where('users.organisasi_id', $organisasi_id)
                ->whereIn('posisis.id_posisi', $id_posisi_members)
                ->where(function ($dat) use ($search) {
                    $dat->where(function ($subQuery) use ($search) {
                        $subQuery->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                                 ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
                    });
                });

            } else {
                $query->where('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
            }
        } else {
            if(auth()->user()->karyawan->posisi[0]->jabatan_id == 5){
                $query->where('users.organisasi_id', $organisasi_id);
                $query->whereIn('posisis.id_posisi', $id_posisi_members);
                $query->orWhere('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
            } else {
                $query->where('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
            }
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('users', 'karyawans.user_id', 'users.id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        $query->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama');

        $data = $query->simplePaginate(30);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        $dataUser = [];
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

    public function get_karyawan_lembur()
    {
        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi',
        );

        $posisi = auth()->user()->karyawan->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps){
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('users', 'karyawans.user_id', 'users.id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        $organisasi_id = auth()->user()->organisasi_id;
        $query->where('users.organisasi_id', $organisasi_id);
        $query->whereIn('posisis.id_posisi', $id_posisi_members);
        $query->orWhere('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);

        $query->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama',);
        $data = $query->get();

        $karyawanLembur = [];
        if($data){
            foreach ($data as $karyawan) {
                $karyawanLembur[] = [
                    'id' => $karyawan->id_karyawan,
                    'text' => $karyawan->nama
                ];
            }
        };

        return response()->json(['message' => 'Data Karyawan Berhasil Ditemukan', 'data' => $karyawanLembur], 200);

        
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

    function get_parent_posisi($posisi)
    {
        $data = [];
        if ($posisi->parent_id !== 0) {
            $parent = Posisi::find($posisi->parent_id);
            $data = array_merge($data, $this->get_parent_posisi($parent));
        }
        $data[] = $posisi->parent_id;
        return $data;
    }

    function has_department_head($posisi)
    {
        $has_dept_head = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3){
                                $has_dept_head = true;
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json(['message' => 'Anda tidak memiliki posisi, silahkan hubungi HRD'], 200);
        }

        return $has_dept_head;
    } 

    function has_section_head($posisi)
    {
        $has_sec_head = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4){
                                $has_sec_head = true;
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json(['message' => 'Anda tidak memiliki posisi, silahkan hubungi HRD'], 200);
        }

        return $has_sec_head;
    } 

    public function get_data_lembur(string $id_lembur)
    {
        try {
            $lembur = Lembure::findOrFail($id_lembur);
            $data_detail_lembur = [];
            foreach ($lembur->detailLembur as $data){
                //rencana
                $duration_rencana = $this->calculate_overtime_per_minutes($data->rencana_mulai_lembur, $data->rencana_selesai_lembur, $data->organisasi_id);
                $hour_rencana = floor($duration_rencana / 60);
                $minutes_rencana = $duration_rencana % 60;

                //aktual
                $duration_aktual = $this->calculate_overtime_per_minutes($data->aktual_mulai_lembur, $data->aktual_selesai_lembur, $data->organisasi_id);
                $hour_aktual = floor($duration_aktual / 60);
                $minutes_aktual = $duration_aktual % 60;

                $data_detail_lembur[] = [
                    'id_detail_lembur' => $data->id_detail_lembur,
                    'lembur_id' => $data->lembur_id,
                    'nama' => $data->karyawan->nama,
                    'karyawan_id' => $data->karyawan_id,
                    'organisasi_id' => $data->organisasi_id,
                    'departemen_id' => $data->departemen_id,
                    'divisi_id' => $data->divisi_id,
                    'rencana_mulai_lembur' => $data->rencana_mulai_lembur ? Carbon::parse($data->rencana_mulai_lembur)->format('Y-m-d\TH:i') : null,
                    'rencana_selesai_lembur' => $data->rencana_selesai_lembur ? Carbon::parse($data->rencana_selesai_lembur)->format('Y-m-d\TH:i') : null,
                    'aktual_mulai_lembur' => $data->aktual_mulai_lembur ? Carbon::parse($data->aktual_mulai_lembur)->format('Y-m-d\TH:i') : null,
                    'aktual_selesai_lembur' => $data->aktual_selesai_lembur ? Carbon::parse($data->aktual_selesai_lembur)->format('Y-m-d\TH:i') : null,
                    'is_rencana_approved' => $data->is_rencana_approved,
                    'is_aktual_approved' => $data->is_aktual_approved,
                    'deskripsi_pekerjaan' => $data->deskripsi_pekerjaan,
                    'durasi_rencana' => $hour_rencana . ' jam  ' . $minutes_rencana . ' menit',
                    'durasi_aktual' => $hour_aktual . ' jam  ' . $minutes_aktual . ' menit',
                    'keterangan' => $data->keterangan,
                    'nominal' => 'Rp. ' . number_format($data->nominal, 0, ',', '.'),
                ];
            }

            $data = [
                'header' => $lembur,
                'attachment' => $lembur->attachmentLembur,
                'detail_lembur' => $data_detail_lembur,
                'text_tanggal' => Carbon::parse($data->rencana_mulai_lembur)->locale('id')->translatedFormat('l, d F Y'),
            ];
            return response()->json(['message' => 'Berhasil mendapatkan data lembur', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Data lembur tidak tersedia, hubungi ICT!', 'data' => []], 500);
        }
    }

    public function delete(string $id_lembur)
    {
        DB::beginTransaction();
        try{
            $lembure = Lembure::find($id_lembur);
            $lembure->detailLembur()->delete();
            $lembure->delete();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur Dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    //OLD
    // public function checked(Request $request, string $id_lembur)
    // {
    //     $dataValidate = [
    //         'is_planned' => ['required', 'in:Y,N'],
    //     ];

    //     $validator = Validator::make(request()->all(), $dataValidate);
    
    //     if ($validator->fails()) {
    //         $errors = $validator->errors()->all();
    //         return response()->json(['message' => $errors], 402);
    //     }

    //     $is_planned = $request->is_planned;
    //     $approved_detail = $request->approved_detail;

    //     DB::beginTransaction();
    //     try{
    //         $lembur = Lembure::find($id_lembur);
    //         $detail_lembur = $lembur->detailLembur;
    //         $karyawan = auth()->user()->karyawan->nama;
    //         $total_durasi = $lembur->total_durasi;

    //         if($is_planned == 'N'){
    //             if(!$approved_detail){
    //                 DB::commit();
    //                 return response()->json(['message' => 'Minimal ada 1 orang yang di Checked !'], 403);
    //             } else {
    //                 $approved_detail = explode(',', $approved_detail);
    //             }
                
    //             if($lembur->plan_checked_by !== null){
    //                 return response()->json(['message' => 'Pengajuan Lembur sudah di checked !'], 403);
    //             }
    
    //             // Delete detail lembur yang tidak ada di approved_detail
    //             foreach ($detail_lembur as $detail) {
    //                 if (!in_array($detail->id_detail_lembur, $approved_detail)) {
    //                     $detail->is_rencana_approved = 'N';
    //                     $detail->is_aktual_approved = 'N';
    //                     $detail->save();

    //                     $lembur->total_durasi -= $detail->durasi;
    //                 }
    //             }

    //             $lembur->plan_checked_by = $karyawan;
    //             $lembur->plan_checked_at = now();

    //         } else {
    //             if($lembur->actual_checked_by !== null){
    //                 return response()->json(['message' => 'Aktual Lembur sudah di checked!'], 403);
    //             }

    //             $lembur->actual_checked_by = $karyawan;
    //             $lembur->actual_checked_at = now();
    //         }

    //         $lembur->save();
    //         DB::commit();
    //         return response()->json(['message' => 'Pengajuan Lembur berhasil di Checked!'],200);
    //     } catch (Throwable $e){
    //         DB::rollBack();
    //         return response()->json(['message' => $error->getMessage()], 500);
    //     }
    // }
    public function checked(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'is_planned' => ['required', 'in:Y,N'],
            'mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:selesai_lembur.*'],
            'selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:mulai_lembur.*'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $is_planned = $request->is_planned;
        $checked_detail = $request->approved_detail;
        $mulai_lemburs = $request->mulai_lembur;
        $selesai_lemburs = $request->selesai_lembur;
        $id_detail_lemburs = $request->id_detail_lembur;
        $keterangan = $request->keterangan;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            $detail_lembur = $lembur->detailLembur;

            $date = Carbon::parse($mulai_lemburs[0])->format('Y-m-d');
            foreach ($mulai_lemburs as $key => $start) {
                if (Carbon::parse($start)->format('Y-m-d') !== $date) {
                    DB::rollback();
                    return response()->json(['message' => 'Seluruh tanggal mulai lembur harus berada pada tanggal yang sama!'], 402);
                }
            }

            if($is_planned == 'N'){
                if(!$checked_detail){
                    DB::commit();
                    return response()->json(['message' => 'Minimal ada 1 orang yang di Checked!'], 403);
                } else {
                    $checked_detail = explode(',', $checked_detail);
                }
    
                if($lembur->plan_checked_by !== null){
                    return response()->json(['message' => 'Pengajuan Lembur sudah di Checked !'], 403);
                }

                $total_durasi = 0;
                $total_nominal = 0;
                foreach ($id_detail_lemburs as $key => $id_detail_lembur) {
                    $detail = DetailLembur::find($id_detail_lembur);
                    if (!in_array($detail->id_detail_lembur, $checked_detail)) {
                        $detail->is_rencana_approved = 'N';
                        $detail->is_aktual_approved = 'N';
                        $detail->save();
                        
                        $lembur->total_durasi -= $detail->durasi;
                    } else {
                        if($detail && $detail->is_rencana_approved == 'Y'){
                            $karyawan = $detail->karyawan;
                            $gaji_lembur = $karyawan->settingLembur->gaji;
                            $jenis_hari = $detail->lembur->jenis_hari == 'WEEKDAY' ? 'WD' : 'WE';
                            $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $detail->organisasi_id)->first()->value;
                            $datetime_rencana_mulai_lembur = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$key]);
                            $datetime_rencana_selesai_lembur = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$key]);
                            $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $detail->organisasi_id);
                            $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $detail->organisasi_id);
                            $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $detail->karyawan_id);
                            $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $detail->karyawan_id);
            
                            if($durasi < 60){
                                DB::rollback();
                                return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                            }
        
                            $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $detail->karyawan_id);
                            $detail->rencana_mulai_lembur = $datetime_rencana_mulai_lembur;
                            $detail->rencana_selesai_lembur = $datetime_rencana_selesai_lembur;
                            $detail->durasi_istirahat = $durasi_istirahat;
                            $detail->durasi_konversi_lembur = $durasi_konversi_lembur;
                            $detail->uang_makan = $uang_makan;
                            $detail->gaji_lembur = $gaji_lembur;
                            $detail->pembagi_upah_lembur = $pembagi_upah_lembur;
                            $detail->durasi = $durasi;
                            $detail->nominal = $nominal;
                            $detail->save();

                            // Hitung durasi dan nominal Aktual
                            $total_durasi += $durasi;
                        }
                    }
                }

                $lembur->update([
                    'plan_checked_by' => auth()->user()->karyawan->nama,
                    'plan_checked_at' => now(),
                    'total_durasi' => $total_durasi,
                    'total_nominal' => $total_nominal
                ]);
                $lembur->save();
            } else {
                if($lembur->actual_checked_by !== null){
                    DB::rollback();
                    return response()->json(['message' => 'Aktual Lembur sudah di Checked !'], 403);
                }

                $total_durasi = 0;
                $total_nominal = 0;
                foreach ($id_detail_lemburs as $key => $id_detail_lembur) {
                    $detail = DetailLembur::find($id_detail_lembur);
                    if($detail && $detail->is_aktual_approved == 'Y'){
                        $karyawan = $detail->karyawan;
                        $gaji_lembur = $karyawan->settingLembur->gaji;
                        $jenis_hari = $detail->lembur->jenis_hari == 'WEEKDAY' ? 'WD' : 'WE';
                        $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $detail->organisasi_id)->first()->value;
                        $datetime_aktual_mulai_lembur = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$key]);
                        $datetime_aktual_selesai_lembur = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$key]);
                        $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur, $detail->organisasi_id);
                        $durasi = $this->calculate_overtime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur, $detail->organisasi_id);
                        $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $detail->karyawan_id);
                        $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $detail->karyawan_id);
        
                        if($durasi < 60){
                            DB::rollback();
                            return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                        }
    
                        $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $detail->karyawan_id);
                        $detail->aktual_mulai_lembur = $datetime_aktual_mulai_lembur;
                        $detail->aktual_selesai_lembur = $datetime_aktual_selesai_lembur;
                        $detail->durasi_istirahat = $durasi_istirahat;
                        $detail->durasi_konversi_lembur = $durasi_konversi_lembur;
                        $detail->uang_makan = $uang_makan;
                        $detail->gaji_lembur = $gaji_lembur;
                        $detail->pembagi_upah_lembur = $pembagi_upah_lembur;
                        $detail->durasi = $durasi;
                        $detail->nominal = $nominal;

                        // Hitung durasi dan nominal Aktual
                        $total_durasi += $durasi;
                    }
                    $detail->keterangan = isset($keterangan[$key]) ? $keterangan[$key] : null;
                    $detail->save();
                }

                $lembur->update([
                    'actual_checked_by' => auth()->user()->karyawan->nama,
                    'actual_checked_at' => now(),
                    'total_durasi' => $total_durasi,
                    'total_nominal' => $total_nominal
                ]);
                $lembur->save();
            }

            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Checked!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approved(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'is_planned' => ['required', 'in:Y,N'],
            'mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:selesai_lembur.*'],
            'selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:mulai_lembur.*'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $is_planned = $request->is_planned;
        $approved_detail = $request->approved_detail;
        $mulai_lemburs = $request->mulai_lembur;
        $selesai_lemburs = $request->selesai_lembur;
        $id_detail_lemburs = $request->id_detail_lembur;
        $keterangan = $request->keterangan;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            $detail_lembur = $lembur->detailLembur;

            $date = Carbon::parse($mulai_lemburs[0])->format('Y-m-d');
            foreach ($mulai_lemburs as $key => $start) {
                if (Carbon::parse($start)->format('Y-m-d') !== $date) {
                    DB::rollback();
                    return response()->json(['message' => 'Seluruh tanggal mulai lembur harus berada pada tanggal yang sama!'], 402);
                }
            }

            if($is_planned == 'N'){
                if(!$approved_detail){
                    DB::commit();
                    return response()->json(['message' => 'Minimal ada 1 orang yang di Approved!'], 403);
                } else {
                    $approved_detail = explode(',', $approved_detail);
                }
    
                if($lembur->plan_approved_by !== null){
                    return response()->json(['message' => 'Pengajuan Lembur sudah di Approved !'], 403);
                }

                $total_durasi = 0;
                $total_nominal = 0;
                foreach ($id_detail_lemburs as $key => $id_detail_lembur) {
                    $detail = DetailLembur::find($id_detail_lembur);
                    if (!in_array($detail->id_detail_lembur, $approved_detail)) {
                        $detail->is_rencana_approved = 'N';
                        $detail->is_aktual_approved = 'N';
                        $detail->save();
                        
                        $lembur->total_durasi -= $detail->durasi;
                    } else {
                        if($detail && $detail->is_rencana_approved == 'Y'){
                            $karyawan = $detail->karyawan;
                            $gaji_lembur = $karyawan->settingLembur->gaji;
                            $jenis_hari = $detail->lembur->jenis_hari == 'WEEKDAY' ? 'WD' : 'WE';
                            $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $detail->organisasi_id)->first()->value;
                            $datetime_rencana_mulai_lembur = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$key]);
                            $datetime_rencana_selesai_lembur = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$key]);
                            $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $detail->organisasi_id);
                            $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $detail->organisasi_id);
                            $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $detail->karyawan_id);
                            $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $detail->karyawan_id);
            
                            if($durasi < 60){
                                DB::rollback();
                                return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                            }
        
                            $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $detail->karyawan_id);
                            $detail->rencana_mulai_lembur = $datetime_rencana_mulai_lembur;
                            $detail->rencana_selesai_lembur = $datetime_rencana_selesai_lembur;
                            $detail->durasi_istirahat = $durasi_istirahat;
                            $detail->durasi_konversi_lembur = $durasi_konversi_lembur;
                            $detail->uang_makan = $uang_makan;
                            $detail->gaji_lembur = $gaji_lembur;
                            $detail->pembagi_upah_lembur = $pembagi_upah_lembur;
                            $detail->durasi = $durasi;
                            $detail->nominal = $nominal;
                            $detail->save();

                            // Hitung durasi dan nominal Aktual
                            $total_durasi += $durasi;
                        }
                    }
                }

                
                //Jika yang membuat tidak memiliki dept head maka checked by = approved by
                $has_dept_head = $this->has_department_head($lembur->issued->posisi);
                if(!$has_dept_head && $lembur->plan_checked_by == null){
                    $lembur->update([
                        'plan_checked_by' => auth()->user()->karyawan->nama,
                        'plan_checked_at' => now(),
                    ]);
                } else {
                    if($lembur->plan_checked_by == null){
                        DB::rollback();
                        return response()->json(['message' => 'Pengajuan Lembur belum di check oleh Sect.Head/Dept.Head !'], 403);
                    }
                }

                $lembur->update([
                    'plan_approved_by' => auth()->user()->karyawan->nama,
                    'plan_approved_at' => now(),
                    'total_durasi' => $total_durasi,
                    'total_nominal' => $total_nominal
                ]);
                $lembur->save();
            } else {
                if($lembur->actual_approved_by !== null){
                    DB::rollback();
                    return response()->json(['message' => 'Aktual Lembur sudah di Approved !'], 403);
                }

                $total_durasi = 0;
                $total_nominal = 0;
                foreach ($id_detail_lemburs as $key => $id_detail_lembur) {
                    $detail = DetailLembur::find($id_detail_lembur);
                    if($detail && $detail->is_aktual_approved == 'Y'){
                        $karyawan = $detail->karyawan;
                        $gaji_lembur = $karyawan->settingLembur->gaji;
                        $jenis_hari = $detail->lembur->jenis_hari == 'WEEKDAY' ? 'WD' : 'WE';
                        $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $detail->organisasi_id)->first()->value;
                        $datetime_aktual_mulai_lembur = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$key]);
                        $datetime_aktual_selesai_lembur = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$key]);
                        $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur, $detail->organisasi_id);
                        $durasi = $this->calculate_overtime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur, $detail->organisasi_id);
                        $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $detail->karyawan_id);
                        $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $detail->karyawan_id);
        
                        if($durasi < 60){
                            DB::rollback();
                            return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                        }
    
                        $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $detail->karyawan_id);
                        $detail->aktual_mulai_lembur = $datetime_aktual_mulai_lembur;
                        $detail->aktual_selesai_lembur = $datetime_aktual_selesai_lembur;
                        $detail->durasi_istirahat = $durasi_istirahat;
                        $detail->durasi_konversi_lembur = $durasi_konversi_lembur;
                        $detail->uang_makan = $uang_makan;
                        $detail->gaji_lembur = $gaji_lembur;
                        $detail->pembagi_upah_lembur = $pembagi_upah_lembur;
                        $detail->durasi = $durasi;
                        $detail->nominal = $nominal;

                        // Hitung durasi dan nominal Aktual
                        $total_durasi += $durasi;
                    }
                    $detail->keterangan = isset($keterangan[$key]) ? $keterangan[$key] : null;
                    $detail->save();
                }

                //Jika yang membuat tidak memiliki dept head maka checked by = approved by
                $has_dept_head = $this->has_department_head($lembur->issued->posisi);
                if(!$has_dept_head && $lembur->plan_checked_by == null){
                    $lembur->update([
                        'actual_checked_by' => auth()->user()->karyawan->nama,
                        'actual_checked_at' => now(),
                    ]);
                } else {
                    if($lembur->actual_checked_by == null){
                        DB::rollback();
                        return response()->json(['message' => 'Aktual Lembur belum di check oleh Sect.Head/Dept.Head !'], 403);
                    }
                }

                $lembur->update([
                    'actual_approved_by' => auth()->user()->karyawan->nama,
                    'actual_approved_at' => now(),
                    'total_durasi' => $total_durasi,
                    'total_nominal' => $total_nominal
                ]);
                $lembur->save();
            }

            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Approved!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function legalized(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'is_planned' => ['required', 'in:Y,N'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $is_planned = $request->is_planned;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            $karyawan = 'HRD & GA';

            if($is_planned == 'N'){
                if($lembur->plan_legalized_by !== null){
                    return response()->json(['message' => 'Pengajuan Lembur sudah di Legalized pada Planning !'], 403);
                }
                $lembur->status = 'PLANNED';
                $lembur->plan_legalized_by = $karyawan;
                $lembur->plan_legalized_at = now();
            } else {
                if($lembur->actual_legalized_by !== null){
                    return response()->json(['message' => 'Pengajuan Lembur sudah di Legalized pada Aktual!'], 403);
                }
                $lembur->status = 'COMPLETED';
                $lembur->actual_legalized_by = $karyawan;
                $lembur->actual_legalized_at = now();

                //NEW 
                $detail_lembur = $lembur->detailLembur->where('is_aktual_approved', 'Y');
                $departemen_id = $lembur?->departemen_id;
                $divisi_id = $lembur?->divisi_id;
                $organisasi_id = auth()->user()->organisasi_id;

                foreach ($detail_lembur as $item){
                    $total_nominal = $item->nominal;
                    $total_durasi = $item->durasi;
                    $aktual_mulai_lembur = $item->aktual_mulai_lembur;
    
                    $tanggal_lembur = Carbon::parse($aktual_mulai_lembur)->format('Y-m-d');
                    $lembur_harian = LemburHarian::whereDate('tanggal_lembur', $tanggal_lembur)->where('organisasi_id', $organisasi_id)->where('departemen_id', $departemen_id)->where('divisi_id', $divisi_id)->first();
                    if ($lembur_harian){
                        $lembur_harian->total_durasi_lembur = $lembur_harian->total_durasi_lembur + $total_durasi;
                        $lembur_harian->total_nominal_lembur = $lembur_harian->total_nominal_lembur + $total_nominal;
                        $lembur_harian->save();
                    } else {
                        $lembur_harian = LemburHarian::create([
                            'tanggal_lembur' => $tanggal_lembur,
                            'total_durasi_lembur' => $total_durasi,
                            'total_nominal_lembur' => $total_nominal,
                            'organisasi_id' => $organisasi_id,
                            'departemen_id' => $departemen_id,
                            'divisi_id' => $divisi_id,
                        ]);
                    }
                }
            }

            $lembur->save();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Legalized!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function rejected(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'rejected_note' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $rejected_note = $request->rejected_note;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            if(auth()->user()->hasRole('personalia')){
                $rejected_by = 'HRD & GA';
            } else {
                $rejected_by = auth()->user()->karyawan->nama;
            }

            $detail_lembur = $lembur->detailLembur;
            foreach ($detail_lembur as $detail){
                $detail->is_rencana_approved = 'N';
                $detail->is_aktual_approved = 'N';
                $detail->save();
            }

            $lembur->status = 'REJECTED';
            $lembur->rejected_by = $rejected_by;
            $lembur->rejected_note = $rejected_note;
            $lembur->rejected_at = now();
            $lembur->save();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Rejected!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function done(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'aktual_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:aktual_selesai_lembur.*'],
            'aktual_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:aktual_mulai_lembur.*'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $is_aktual_approved = $request->is_aktual_approved;
        $aktual_mulai_lemburs = $request->aktual_mulai_lembur;
        $aktual_selesai_lemburs = $request->aktual_selesai_lembur;
        $id_detail_lemburs = $request->id_detail_lembur;
        $keterangan = $request->keterangan;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            $detail_lembur = $lembur->detailLembur;

            if(!$is_aktual_approved){
                DB::commit();
                return response()->json(['message' => 'Minimal ada 1 orang yang di Approved!'], 403);
            } else {
                $is_aktual_approved = explode(',', $is_aktual_approved);
            }

            if($lembur->status !== 'PLANNED'){
                return response()->json(['message' => 'Status lembur harus planned untuk melakukan Done!'], 403);
            }

            if($lembur->plan_legalized_by == null){
                return response()->json(['message' => 'Pengajuan Lembur belum di Legalized oleh HRD !'], 403);
            }


            $total_durasi_aktual = 0;
            $total_nominal_aktual = 0;
            // Delete detail lembur yang tidak ada di is_aktual_approved
            foreach ($id_detail_lemburs as $key => $id_detail_lembur) {
                $detail = DetailLembur::find($id_detail_lembur);
                if (!in_array($detail->id_detail_lembur, $is_aktual_approved)) {
                    $detail->is_aktual_approved = 'N';
                } else {
                    if($detail && $detail->is_aktual_approved == 'Y'){
                        $karyawan = $detail->karyawan;
                        $gaji_lembur = $karyawan->settingLembur->gaji;
                        $jenis_hari = $detail->lembur->jenis_hari == 'WEEKDAY' ? 'WD' : 'WE';
                        $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $detail->organisasi_id)->first()->value;
                        $datetime_aktual_mulai_lembur = $this->pembulatan_menit_ke_bawah($aktual_mulai_lemburs[$key]);
                        $datetime_aktual_selesai_lembur = $this->pembulatan_menit_ke_bawah($aktual_selesai_lemburs[$key]);
                        $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur, $detail->organisasi_id);
                        $durasi = $this->calculate_overtime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur, $detail->organisasi_id);
                        $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $detail->karyawan_id);
                        $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $detail->karyawan_id);
        
                        if($durasi < 60){
                            DB::rollback();
                            return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                        }
    
                        $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $detail->karyawan_id);
                        $detail->aktual_mulai_lembur = $datetime_aktual_mulai_lembur;
                        $detail->aktual_selesai_lembur = $datetime_aktual_selesai_lembur;
                        $detail->durasi_istirahat = $durasi_istirahat;
                        $detail->durasi_konversi_lembur = $durasi_konversi_lembur;
                        $detail->uang_makan = $uang_makan;
                        $detail->gaji_lembur = $gaji_lembur;
                        $detail->pembagi_upah_lembur = $pembagi_upah_lembur;
                        $detail->durasi = $durasi;
                        $detail->nominal = $nominal;
                        
                        // Hitung durasi dan nominal Aktual
                        $total_durasi_aktual += $durasi;
                    }
                }
                $detail->keterangan = isset($keterangan[$key]) ? $keterangan[$key] : null;
                $detail->save();
            }

            if(auth()->user()->karyawan->posisi[0]->jabatan_id >= 5){
                if(!$this->has_department_head(auth()->user()->karyawan->posisi) && !$this->has_section_head(auth()->user()->karyawan->posisi)){
                    $checked_by = auth()->user()->karyawan->nama;
                    $lembur->update([
                        'actual_checked_by' => $checked_by,
                        'actual_checked_at' => now(),
                    ]);
                }
            }

            if(auth()->user()->karyawan->posisi[0]->jabatan_id == 4){
                if(!$this->has_department_head(auth()->user()->karyawan->posisi)){
                    $checked_by = auth()->user()->karyawan->nama;
                    $lembur->update([
                        'actual_checked_by' => $checked_by,
                        'actual_checked_at' => now(),
                    ]);
                }
            }

            $lembur->update([
                'total_durasi' => $total_durasi_aktual,
                'status' => 'COMPLETED'
            ]);
            $lembur->save();

            DB::commit();
            return response()->json(['message' => 'Aktual Lembur berhasil di Konfirmasi!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //CHART
    public function get_monthly_lembur_per_departemen(Request $request)
    {
        try{
            $data = LemburHarian::getMonthlyLemburPerDepartemen()->toArray();
            $batas = GajiDepartemen::getMonthlyNominalBatasAllDepartemen()->toArray();

            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data, 'batas' => $batas], 200);
        } catch (Throwable $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_weekly_lembur_per_departemen(Request $request)
    {
        try{
            $data = LemburHarian::getWeeklyLemburPerDepartemen()->toArray();
            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data], 200);
        } catch (Throwable $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_current_month_lembur_per_departemen(Request $request)
    {
        try{
            $data = LemburHarian::getCurrentMonthLemburPerDepartemen()->toArray();
            $batas = GajiDepartemen::getCurrentMonthNominalBatasPerDepartemen()->toArray();

            $existing_batas = [];
            foreach ($batas as $key => $value) {
                foreach ($data as $key2 => $value2) {
                    if($value['id_departemen'] == $value2['id_departemen']){
                        $existing_batas[] = $value;
                    }
                }
            }

            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data, 'batas' => $existing_batas], 200);
        } catch (Throwable $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_leaderboard_user_monthly(Request $request)
    {
        try{
            $dataFilter = [];

            if (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3)){
                $posisi = auth()->user()->karyawan->posisi;
                $member_posisi_ids = $this->get_member_posisi($posisi);
                $dataFilter['member_posisi_ids'] = $member_posisi_ids;
            }

            $filterPeriode = $request->periode;
            if(isset($filterPeriode)){
                $dataFilter['month'] = Carbon::parse($filterPeriode)->format('m');
                $dataFilter['year'] = Carbon::parse($filterPeriode)->format('Y');
            } else {
                $dataFilter['month'] = date('m');
                $dataFilter['year'] = date('Y');
            }

            $filterLimit = $request->limit;
            if(isset($filterLimit)){
                $dataFilter['limit'] = $filterLimit;
            }

            $filterDepartemen = $request->departemen;
            if(isset($filterDepartemen)){
                $dataFilter['departemen'] = $filterDepartemen;
            }
            $data = DetailLembur::getLeaderboardUserMonthly($dataFilter)->toArray();
            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data], 200);
        } catch (Throwable $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //EXPORT REPORT LEMBUR
    public function export_rekap_lembur_perbulan(Request $request){
        
        $organisasi_id = auth()->user()->organisasi_id;
        $periode = $request->periode_rekap;
        $year = Carbon::parse($periode)->format('Y');
        $month = Carbon::parse($periode)->format('m');

        //CREATE EXCEL FILE
        $spreadsheet = new Spreadsheet();

        $fillStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ];
        
        $rekapLembur = DetailLembur::getReportMonthlyPerDepartemen($month, $year);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REKAP LEMBUR');
        $row = 1;
        $col = 'A';
        $headers = [
            'NO',
            'FULL NAME',
            'DEPARTMENT',
            'JABATAN',
            'PERIODE PERHITUNGAN',
            'GAJI POKOK '.$year,
            'UPAH LEMBUR PER JAM',
            'TOTAL JAM LEMBUR',
            'KONVERSI JAM LEMBUR',
            'GAJI LEMBUR',
            'UANG MAKAN',
            'TOTAL GAJI LEMBUR'
        ];

        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->mergeCells($col . '1:' . $col . '2');
            $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
            $col++;
        }

        $row = 3;

        $columns = range('A', 'N');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->setAutoFilter('A1:L1');

        $no = 1;
        $is_first = true;
        $is_last = false;
        $departemen_first_data_row = 0;
        if($rekapLembur){
            foreach ($rekapLembur as $index => $data) {
                if($is_first){
                    $sheet->setCellValue('B'.$row, 'DEPARTEMEN '.$data->departemen);
                    $sheet->mergeCells('B'.$row.':E'.$row);
                    $sheet->getStyle('B'.$row.':E'.$row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFFFFF00',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                    ]);
                    $departemen_first_data_row = $row+1;
                    $is_first = false;
                    $row++;
                } 
    
                $sheet->setCellValue('A'.$row, $no);
                $sheet->setCellValue('B'.$row, $data->nama);
                $sheet->setCellValue('C'.$row, $data->departemen);
                $sheet->setCellValue('D'.$row, $data->posisi);
                $sheet->setCellValue('E'.$row, $data->periode_perhitungan);
                $sheet->setCellValue('E'.$row, '1 ' . Carbon::createFromFormat('m', $month)->format('F Y') . ' - ' . Carbon::createFromFormat('Y-m', $year . '-' . $month)->endOfMonth()->format('d F Y'));
                $sheet->setCellValue('F'.$row, $data->gaji);
                $sheet->setCellValue('G'.$row, $data->jabatan_id >= 5 ? $data->upah_lembur_per_jam : '-');
                $sheet->setCellValue('H'.$row, $data->total_jam_lembur);
                $sheet->setCellValue('I'.$row, $data->konversi_jam_lembur);
                $sheet->setCellValue('J'.$row, $data->jabatan_id >= 5 ? $data->gaji_lembur : '-');
                $sheet->setCellValue('K'.$row, $data->jabatan_id >= 5 ? $data->uang_makan : '-');
                $sheet->setCellValue('L'.$row, $data->total_gaji_lembur);
    
                if($data->jabatan_id >= 5){
                    $sheet->getStyle('G'.$row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('J'.$row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('K'.$row)->getNumberFormat()->setFormatCode('#,##0');
                }
    
                $sheet->getStyle('F'.$row)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('L'.$row)->getNumberFormat()->setFormatCode('#,##0');
    
                //ALIGN CENTER
                $sheet->getStyle('A'.$row.':L'.$row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
    
                $no++;
                $row++;
    
                if(isset($rekapLembur[$index+1]) && $rekapLembur[$index+1]->departemen !== $data->departemen){
                    $is_first = true;
                    $is_last = true;
                }
    
                if(!isset($rekapLembur[$index+1])){
                    $is_last = true;
                }
    
                if($is_last){
                    $sheet->setCellValue('A'.$row, '###');
                    $sheet->setCellValue('B'.$row, 'TOTAL GAJI DEPT. '.$data->departemen);
                    $sheet->setCellValue('C'.$row, $data->departemen);
                    $sheet->setCellValue('D'.$row, '-');
                    $sheet->setCellValue('E'.$row, '-');
                    $sheet->setCellValue('F'.$row, '-');
                    $sheet->setCellValue('G'.$row, '-');
                    $sheet->getStyle('A'.$row.':N'.$row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFFFFF00',
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                    ]);
                    $sheet->setCellValue('H'.$row, '=SUM(H'.$departemen_first_data_row.':H'.($row-1).')');
                    $sheet->setCellValue('I'.$row, '=SUM(I'.$departemen_first_data_row.':I'.($row-1).')');
                    $sheet->setCellValue('J'.$row, '=SUM(J'.$departemen_first_data_row.':J'.($row-1).')');
                    $sheet->setCellValue('K'.$row, '=SUM(K'.$departemen_first_data_row.':K'.($row-1).')');
                    $sheet->setCellValue('L'.$row, '=SUM(L'.$departemen_first_data_row.':L'.($row-1).')');
    
                    $sheet->getStyle('J'.$row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('K'.$row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('L'.$row)->getNumberFormat()->setFormatCode('#,##0');
                    $is_last = false;
    
                    if(isset($rekapLembur[$index+1])){
                        $no = 1;
                        $row++;
                    }
                }
            }
        }

        //STYLE ALL CELLS
        $sheet->getStyle('A1:L'.$row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekapitulasi Pembayaran Lembur - '.Carbon::createFromFormat('m', $month)->format('F Y').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    public function export_slip_lembur_perbulan(Request $request){
        
        $organisasi_id = auth()->user()->organisasi_id;
        $periode = $request->periode_slip;
        $departemen_id = $request->departemen_slip;
        $departemen = Departemen::find($departemen_id)->nama;

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
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ];
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SLIP LEMBUR');
        $row = 1;
        $headers = [
            'NO',
            'HARI',
            'TANGGAL',
            'JAM MASUK',
            'JAM KELUAR',
            'JAM ISTIRAHAT',
            'JAM KELUAR SETELAH ISTIRAHAT',
            'TOTAL JAM',
            'KONVERSI JAM',
            'UANG MAKAN',
            'UPAH LEMBUR PERJAM',
            'JUMLAH'
        ];
        $start = Carbon::createFromFormat('Y-m', $periode)->startOfMonth()->toDateString();
        $end = Carbon::createFromFormat('Y-m', $periode)->endOfMonth()->toDateString();
        $members = Karyawan::getDepartemenMember($departemen_id);

        $columns = range('A', 'L');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
   
        foreach ($members as $kry){
            $lembur_karyawan = DetailLembur::leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')->where('detail_lemburs.karyawan_id', $kry->id_karyawan)->whereBetween('detail_lemburs.aktual_mulai_lembur', [$start, $end])->whereNotNull('lemburs.actual_legalized_by')
            ->where('lemburs.status', 'COMPLETED')->first();
            $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $kry->id_karyawan)->first();
            $pembagi_upah_lembur_harian = SettingLembur::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'pembagi_upah_lembur_harian')->first()->value;
            $upah_lembur_per_jam_setting = $lembur_karyawan ? $lembur_karyawan->gaji_lembur / $lembur_karyawan->pembagi_upah_lembur : ($setting_lembur_karyawan ? $setting_lembur_karyawan->gaji / $pembagi_upah_lembur_harian : 0);
            // TEXT "SLIP LEMBUR BULAN INI"
            $sheet->mergeCells('A'.$row.':F'.$row+1);
            $sheet->setCellValue('A'.$row, 'SLIP LEMBUR BULAN '.Carbon::createFromFormat('Y-m', $periode)->format('F Y'));
            $sheet->getStyle('A'.$row.':F'.$row+1)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF808080',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            $row += 2;
            $sheet->setCellValue('B'.$row, 'NAMA');
            $sheet->setCellValue('C'.$row, ':');
            $sheet->setCellValue('D'.$row, $kry->nama);
            $sheet->setCellValue('B'.$row+1, 'NIK');
            $sheet->setCellValue('C'.$row+1, ':');
            $sheet->setCellValue('D'.$row+1, $kry->ni_karyawan);
            $sheet->getStyle('B'.$row.':B'.$row+1)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            $sheet->getStyle('C'.$row.':C'.$row+1)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            $sheet->getStyle('D'.$row.':D'.$row+1)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ]);

            $row += 2;
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->mergeCells($col . $row.':' . $col . ($row+1));
                $sheet->getStyle($col . $row.':' . $col . ($row+1))->applyFromArray($fillStyle);
                $col++;
            }
            
            $row += 2;
            //LOOPING AWAL SAMPAI AKHIR BULAN
            $total_jam = 0;
            $total_konversi_jam = 0;
            $total_uang_makan = 0;
            $total_spl = 0;
            for($i = 0; $i <= Carbon::parse($start)->diffInDays(Carbon::parse($end)); $i++){
                $date = Carbon::parse($start)->addDays($i)->toDateString();
                $slipLembur = DetailLembur::getSlipLemburPerDepartemen($kry->id_karyawan, $date);
                $upah_lembur_per_jam = $slipLembur ? $slipLembur->gaji_lembur / $slipLembur->pembagi_upah_lembur : $upah_lembur_per_jam_setting;

                if($slipLembur){
                    $total_jam += $slipLembur->durasi;
                    $total_konversi_jam += $slipLembur->durasi_konversi_lembur;
                    $total_uang_makan += $slipLembur->uang_makan;
                    $total_spl += $slipLembur->nominal;
                    $sheet->setCellValue('A'.$row, $i+1);
                    $sheet->setCellValue('B'.$row, Carbon::parse($date)->locale('id')->translatedFormat('l'));

                    //JIKA WEEKEND UBAH STYLE CELL
                    if(Carbon::parse($date)->isWeekend()){
                        $sheet->getStyle('B'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'argb' => 'FFFF0000',
                                ],
                            ],
                            'font' => [
                                'color' => [
                                    'argb' => 'FFFFFFFF',
                                ],
                            ],
                        ]);
                    }

                    $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
                    $sheet->setCellValue('D'.$row, Carbon::parse($slipLembur->aktual_mulai_lembur)->format('H:i'));
                    $sheet->setCellValue('E'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->format('H:i'));
                    $sheet->setCellValue('F'.$row, number_format($slipLembur->durasi_istirahat / 100 , 2));
                    $sheet->setCellValue('G'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->subMinutes($slipLembur->durasi_istirahat)->format('H:i'));
                    $sheet->setCellValue('H'.$row, number_format($slipLembur->durasi / 60, 2));
                    $sheet->setCellValue('I'.$row, number_format($slipLembur->durasi_konversi_lembur / 60, 2));
                    $sheet->setCellValue('J'.$row, $slipLembur->uang_makan);
                    $sheet->setCellValue('K'.$row, 'Rp ' . number_format($upah_lembur_per_jam, 0, ',', '.'));
                    $sheet->setCellValue('L'.$row, 'Rp '. number_format($slipLembur->nominal, 0, ',', '.'));

                    //STYLE CELL
                $sheet->getStyle('C'.$row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle('J'.$row.':K'.$row)->applyFromArray([
                    'font' => [
                        'color' => [
                            'argb' => 'FFFF0000',
                        ],
                    ],
                ]);
                $sheet->getStyle('A'.$row.':L'.$row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                } else {
                    $sheet->setCellValue('A'.$row, $i+1);
                    $sheet->setCellValue('B'.$row, Carbon::parse($date)->locale('id')->translatedFormat('l'));

                    //JIKA WEEKEND UBAH STYLE CELL
                    if(Carbon::parse($date)->isWeekend()){
                        $sheet->getStyle('B'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'argb' => 'FFFF0000',
                                ],
                            ],
                            'font' => [
                                'color' => [
                                    'argb' => 'FFFFFFFF',
                                ],
                            ],
                        ]);
                    }

                    $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
                    $sheet->setCellValue('D'.$row, '-');
                    $sheet->setCellValue('E'.$row, '-');
                    $sheet->setCellValue('F'.$row, '-');
                    $sheet->setCellValue('G'.$row, '-');
                    $sheet->setCellValue('H'.$row, '-');
                    $sheet->setCellValue('I'.$row, '-');
                    $sheet->setCellValue('J'.$row, 0);
                    $sheet->setCellValue('K'.$row, 'Rp ' . number_format($upah_lembur_per_jam, 0, ',', '.'));
                    $sheet->setCellValue('L'.$row, 'Rp');
                }

                //STYLE CELL
                $sheet->getStyle('C'.$row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle('J'.$row.':K'.$row)->applyFromArray([
                    'font' => [
                        'color' => [
                            'argb' => 'FFFF0000',
                        ],
                    ],
                ]);
                $sheet->getStyle('A'.$row.':L'.$row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                $row++;
            }
            $sheet->setCellValue('H'.$row, number_format($total_jam / 60 , 2));    
            $sheet->setCellValue('I'.$row, number_format($total_konversi_jam / 60 , 2));    
            $sheet->setCellValue('J'.$row, 'Rp ' . number_format($total_uang_makan, 0, ',', '.'));    
            $sheet->setCellValue('K'.$row, '-');    
            $sheet->setCellValue('L'.$row, 'Rp ' . number_format($total_spl, 0, ',', '.'));
            $sheet->setCellValue('K'.$row+1, 'SESUAI SPL');
            $sheet->setCellValue('L'.$row+1, 'Rp ' . number_format($total_spl, 0, ',', '.'));
            $sheet->getStyle('H'.$row.':L'.$row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ]
            ]);
            $sheet->getStyle('K'.($row+1).':L'.($row+1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            
            $row += 6;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Slip Pembayaran Lembur - '.$departemen.' - '.Carbon::createFromFormat('Y-m', $periode)->format('F Y').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    public function upload_upah_lembur_karyawan(Request $request)
    {
        $file = $request->file('upah_lembur_karyawan_file');
        $organisasi_id = auth()->user()->organisasi_id;
        
        $validator = Validator::make($request->all(), [
            'upah_lembur_karyawan_file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'File Harus bertipe Excel!'], 400);
        }

        DB::beginTransaction();
        try {

            if($request->hasFile('upah_lembur_karyawan_file')){
                $upah_lembur_karyawan_records = 'ULK_' . time() . '.' . $file->getClientOriginalExtension();
                $upah_lembur_karyawan_file = $file->storeAs("attachment/upload-upah-lembur-karyawan", $upah_lembur_karyawan_records);
            } 

            if (file_exists(storage_path("app/public/".$upah_lembur_karyawan_file))) {
                $spreadsheet = IOFactory::load(storage_path("app/public/".$upah_lembur_karyawan_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                unset($data[0]);

                foreach ($data as $key => $row) {
                    Log::info($row[0]);
                    $karyawan = Karyawan::where('ni_karyawan', $row[0])->first();
                    $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $karyawan->id_karyawan)->first();

                    if($karyawan){
                        if($karyawan->user->organisasi_id !== auth()->user()->organisasi_id){
                            DB::rollback();
                            return response()->json(['message' => 'NIK Karyawan '.$karyawan->nama.' tidak terdaftar di Plant Anda!'], 404);
                        }
    
                        if(!$karyawan->posisi){
                            DB::rollback();
                            return response()->json(['message' => 'Karyawan '.$karyawan->nama.' belum memiliki posisi, setting di master data!'], 404);
                        }
    
                        if(!is_numeric($row[2]) || $row[2] < 0 ){
                            DB::rollback();
                            return response()->json(['message' => 'Gaji '. $karyawan->nama.' harus berupa angka dan tidak boleh kurang dari 0!'], 402);
                        }
    
                        if($setting_lembur_karyawan){
                            $setting_lembur_karyawan->gaji = $row[2];
                            $setting_lembur_karyawan->jabatan_id = $karyawan->posisi[0]->jabatan_id;
                            $setting_lembur_karyawan->departemen_id = $karyawan->posisi[0]->departemen_id;
                            $setting_lembur_karyawan->save();
                        } else {
                            SettingLemburKaryawan::create([
                                'karyawan_id' => $karyawan->id_karyawan,
                                'gaji' => $row[2],
                                'organisasi_id' => $organisasi_id,
                                'jabatan_id' => $karyawan->posisi[0]->jabatan_id,
                                'departemen_id' => $karyawan->posisi[0]->departemen_id
                            ]);
                        }
                    } else {
                        DB::rollback();
                        return response()->json(['message' => 'Data Karyawan dengan NI Karyawan '.$row[0]].' tidak ditemukan, periksa kembali di data master');
                    }
                }
                DB::commit();
                return response()->json(['message' => 'Upah Lembur Karyawan Berhasil di Update'], 200);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Terjadi kesalahan, silahkan upload ulang file!'], 404);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing the file: ' . $e->getMessage()], 500);
        }
    }

    public function store_lkh(Request $request)
    {
        $dataValidate = [
            'attachment_lembur' => ['mimes:jpeg,jpg,png,pdf', 'max:2048', 'required'],
            'lembur_id' => ['required', 'exists:lemburs,id_lembur'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $lembur = Lembure::find($request->lembur_id);
        $file = $request->file('attachment_lembur');

        DB::beginTransaction();
        try{

            $fileName = $lembur->id_lembur.'-'.Str::random(5).'.'.$file->getClientOriginalExtension();
            $file_path = $file->storeAs("attachment/lembur", $fileName);

            $lembur->attachmentLembur()->create([
                'path' => $file_path
            ]);

            DB::commit();
            return response()->json(['message' => 'LKH Berhasil di Upload!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_attachment_lembur(Request $request, string $id_lembur)
    {
        $lembur = Lembure::find($id_lembur);
        $attachment = $lembur->attachmentLembur;
        return response()->json(['message' => 'Data LKH Berhasil Ditemukan', 'data' => $attachment], 200);
    }

    public function get_calculation_durasi_and_nominal_lembur(Request $request, int $id_detail_lembur)
    {
        $dataValidate = [
            'mulai_lembur' => ['required', 'date_format:Y-m-d\TH:i', 'before:selesai_lembur'],
            'selesai_lembur' => ['required', 'date_format:Y-m-d\TH:i', 'after:mulai_lembur'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $detail_lembur = DetailLembur::find($id_detail_lembur);
        $status = $detail_lembur->lembur->status;
        $jenis_hari = $detail_lembur->lembur->jenis_hari == 'WEEKDAY' ? 'WD' : 'WE';
        $karyawan_id = $detail_lembur->karyawan_id;
        $mulai_lembur = $request->mulai_lembur;
        $selesai_lembur = $request->selesai_lembur;

        try{
            $datetime_mulai_lembur = $this->pembulatan_menit_ke_bawah($mulai_lembur);
            $datetime_selesai_lembur = $this->pembulatan_menit_ke_bawah($selesai_lembur);
            $durasi = $this->calculate_overtime_per_minutes($datetime_mulai_lembur, $datetime_selesai_lembur, $detail_lembur->karyawan->user->organisasi_id, $jenis_hari, $karyawan_id);
            $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id);

            $hours = floor($durasi / 60);
            $minutes = $durasi % 60;

            $durasi_text = $hours . ' jam ' . $minutes . ' menit';
            $nominal_text = 'Rp ' . number_format($nominal, 0, ',', '.');

            $data = [
                'durasi' => $durasi_text,
                'nominal' => $nominal_text
            ];
            return response()->json(['message' => 'Data Durasi dan Nominal Lembur Berhasil Ditemukan', 'data' => $data], 200);
        } catch (Throwable $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

        // return response()->json(['message' => 'Data Durasi dan Nominal Lembur Berhasil Ditemukan', 'durasi' => $durasi, 'nominal' => $nominal], 200);
    }

    public function generate_lembur_harian()
    {
        DB::beginTransaction();
        try {
            $data = DetailLembur::generateLemburHarian();
            if($data){
                foreach ($data as $key => $value) {
                    $lembur_harian = LemburHarian::where('organisasi_id', $value->organisasi_id)->where('divisi_id', $value->divisi_id)->where('departemen_id', $value->departemen_id)->whereDate('tanggal_lembur', $value->tanggal_lembur)->first();
                    if($lembur_harian){
                        $lembur_harian->update([
                            'total_nominal_lembur' => $value->total_nominal_lembur,
                            'total_durasi_lembur' => $value->total_durasi_lembur
                        ]);
                    } else {
                        LemburHarian::create([
                            'organisasi_id' => $value->organisasi_id,
                            'divisi_id' => $value->divisi_id,
                            'departemen_id' => $value->departemen_id,
                            'tanggal_lembur' => $value->tanggal_lembur,
                            'total_nominal_lembur' => $value->total_nominal_lembur,
                            'total_durasi_lembur' => $value->total_durasi_lembur
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'Data Lembur Harian Berhasil di Generate'], 200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
