<?php

namespace App\Http\Controllers\Lembure;

use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\Lembure;
use App\Models\Karyawan;
use App\Models\Organisasi;
use Illuminate\Support\Str;
use App\Models\DetailLembur;
use App\Models\LemburHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SettingLemburKaryawan;
use Illuminate\Support\Facades\Validator;

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

    public function pengajuan_lembur_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Pengajuan Lembur",
            'page' => 'lembure-pengajuan-lembur',
        ];
        return view('pages.lembur-e.pengajuan-lembur', $dataPage);
    }

    public function pengajuan_lembur_individual_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Pengajuan Lembur Individual (Non-Leader)",
            'page' => 'lembure-pengajuan-lembur-individual',
        ];
        return view('pages.lembur-e.pengajuan-lembur-individual', $dataPage);
    }

    public function approval_lembur_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Approval Lembur",
            'page' => 'lembure-approval-lembur',
        ];
        return view('pages.lembur-e.approval-lembur', $dataPage);
    }

    public function pengajuan_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'lemburs.id_lembur',
            1 => 'lemburs.issued_date',
            2 => 'karyawans.nama',
            3 => 'lemburs.jenis_hari',
            4 => 'lemburs.total_durasi',
            5 => 'lemburs.status',
            6 => 'lemburs.plan_checked_by',
            7=> 'lemburs.plan_approved_by',
            8 => 'lemburs.plan_legalized_by',
            9 => 'lemburs.actual_checked_by',
            10 => 'lemburs.actual_approved_by',
            11 => 'lemburs.actual_legalized_by'
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
        $totalFiltered = $lembure->count();
        $dataTable = [];

        if (!empty($lembure)) {
            foreach ($lembure as $data) {
                $jam = floor($data->total_durasi / 60);
                $menit = $data->total_durasi % 60;
                $tanggal_lembur = Carbon::parse(DetailLembur::where('lembur_id', $data->id_lembur)->first()->rencana_mulai_lembur)->format('Y-m-d');
                if($data->status == 'WAITING'){
                    $status = '<span class="badge badge-warning">WAITING</span>';
                } elseif ($data->status == 'PLANNED'){
                    $status = '<span class="badge badge-info">PLANNED</span>';
                } elseif ($data->status == 'COMPLETED'){
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                } else {
                    $status = '<span class="badge badge-rejected">REJECTED</span>';
                }

                $nestedData['id_lembur'] = $data->id_lembur;
                $nestedData['issued_date'] = Carbon::parse($data->issued_date)->locale('id')->translatedFormat('l, d F Y');
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
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-info btnDetail" data-id-lembur="'.$data->id_lembur.'"><i class="fas fa-eye"></i> Detail</button>
                    '.($tanggal_lembur <= date('Y-m-d') && $data->status == 'PLANNED' && $data->issued_by == auth()->user()->karyawan->id_karyawan ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id-lembur="'.$data->id_lembur.'"><i class="far fa-check-circle"></i> Done</button>' : '').'
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
            2 => 'karyawans.nama',
            3 => 'lemburs.jenis_hari',
            4 => 'lemburs.total_durasi',
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

        $totalData = Lembure::all()->count();
        $totalFiltered = $totalData;

        $lembure = Lembure::getData($dataFilter, $settings);
        $totalFiltered = $lembure->count();
        $dataTable = [];

        if (!empty($lembure)) {
            foreach ($lembure as $data) {
                $jam = floor($data->total_durasi / 60);
                $menit = $data->total_durasi % 60;
                $tanggal_lembur = Carbon::parse(DetailLembur::where('lembur_id', $data->id_lembur)->first()->rencana_mulai_lembur)->format('Y-m-d');
                $total_nominal = $data->detailLembur->where('is_aktual_approved', 'Y')->sum('nominal');

                //STYLE STATUS
                if($data->status == 'WAITING'){
                    $status = '<span class="badge badge-warning">WAITING</span>';
                } elseif ($data->status == 'PLANNED'){
                    $status = '<span class="badge badge-info">PLANNED</span>';
                } elseif ($data->status == 'COMPLETED'){
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                } else {
                    $status = '<span class="badge badge-rejected">REJECTED</span>';
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
                            $button_checked_plan = '<button class="btn btn-sm btn-success btnChecked" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="far fa-check-circle"></i> Checked</button>';
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
                            $button_approved_plan = '<button class="btn btn-sm btn-success btnApproved" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-thumbs-up"></i> Approved</button>';
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
                            $button_legalized_plan = '<button class="btn btn-sm btn-success btnLegalized" data-id-lembur="'.$data->id_lembur.'" data-can-approved="'.($is_can_approved ? 'true' : 'false').'" data-can-checked="'.($is_can_checked ? 'true' : 'false').'" data-is-planned="'.($is_planned ? 'true' : 'false').'"><i class="fas fa-balance-scale"></i> Legalized</button>';
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
                $nestedData['issued_by'] = $data->nama_karyawan;
                $nestedData['jenis_hari'] = $data->jenis_hari;
                $nestedData['total_durasi'] = $jam . ' Jam ' . $menit . ' Menit';
                $nestedData['total_nominal'] = 'Rp. ' . number_format($total_nominal, 0, ',', '.');
                $nestedData['status'] = $status;
                $nestedData['plan_checked_by'] = $button_checked_plan;
                $nestedData['plan_approved_by'] = $button_approved_plan;
                $nestedData['plan_legalized_by'] = $button_legalized_plan;
                $nestedData['actual_checked_by'] =  $button_checked_actual;
                $nestedData['actual_approved_by'] = $button_approved_actual;
                $nestedData['actual_legalized_by'] = $button_legalized_actual;
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
            'rencana_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i'],
            'rencana_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i'],
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

        DB::beginTransaction();
        try {
            $header = Lembure::create([
                'id_lembur' => 'LEMBUR-' . Str::random(4).'-'. date('YmdHis'),
                'issued_by' => $issued_by,
                'issued_date' => now(),
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'jenis_hari' => $jenis_hari
            ]);

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


            //belum selesai
            $total_durasi = 0;
            $total_nominal = 0;
            $data_detail_lembur = [];
            foreach ($karyawan_ids as $key => $karyawan_id) {
                $karyawan = Karyawan::find($karyawan_id);
                $datetime_rencana_mulai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_mulai_lemburs[$key])->toDateTimeString();
                $datetime_rencana_selesai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_selesai_lemburs[$key])->toDateTimeString();
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur);

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
                    'departemen_id' => $karyawan->posisi[0]->departemen_id,
                    'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'deskripsi_pekerjaan' => $job_descriptions[$key],
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
    public function calculate_overtime_per_minutes($datetime_start, $datetime_end)
    {
        //Kondisi Istirahat ketika lembur
        $start = Carbon::parse($datetime_start);
        $end = Carbon::parse($datetime_end);
        $duration = $start->diffInMinutes($end);

        // Setting Istirahat ketika lembur (Hari jumat memiliki perbedaan)
        if ($start->isFriday()) {
            $breaks = [
                ['start' => '11:30', 'end' => '13:00', 'duration' => 90],
                ['start' => '18:00', 'end' => '18:45', 'duration' => 45],
                ['start' => '02:30', 'end' => '03:15', 'duration' => 45],
            ];
        } else {
            $breaks = [
                ['start' => '12:00', 'end' => '12:45', 'duration' => 45],
                ['start' => '18:00', 'end' => '18:45', 'duration' => 45],
                ['start' => '02:30', 'end' => '03:15', 'duration' => 45],
            ];
        }

        // Adjust duration for each break period
        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($start->format('Y-m-d') . ' ' . $break['start']);
            $breakEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $break['end']);

            // If the break period spans over midnight, adjust the dates
            if ($breakStart->greaterThan($breakEnd)) {
                $breakEnd->addDay();
            }

            // if ($start->lessThanOrEqualTo($breakEnd) && $end->greaterThanOrEqualTo($breakStart)) {
            //     $duration -= $break['duration'];
            // }
                
            //Revisi
            // Check if the overtime period overlaps with the break period
            if ($start->lessThan($breakEnd) && $end->greaterThan($breakStart)) {
                $duration -= $break['duration'];
            }
        }

        // memastikan bukan negatif
        $duration = max($duration, 0);

        // pembulatan durasi ke bawah ke 0, 15, 30, 45
        // $remainder = $duration % 15;
        // if ($remainder != 0) {
        //     $duration -= $remainder;
        // }

        $duration = intval($duration);
        return $duration;
    }

    //FUNGSI MENGHITUNG NOMINAL LEMBUR
    public function calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id)
    {
        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $karyawan_id)->first();
        $karyawan = Karyawan::find($karyawan_id);
        $convert_duration = number_format($durasi / 60, 2);
        $gaji_lembur_karyawan = $setting_lembur_karyawan->gaji;
        $jabatan_id = $karyawan->posisi[0]->jabatan_id;
        $upah_sejam = $gaji_lembur_karyawan / 173;
        $uang_makan = 15000;

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
                    $nominal_lembur = 107500;
                } elseif ($convert_duration >= 2){
                    $nominal_lembur = 67500;
                } elseif ($convert_duration >= 1){
                    $nominal_lembur = 32500;
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
                    $nominal_lembur = 250000;
                } elseif ($convert_duration >= 3){
                    $nominal_lembur = 107500;
                } elseif ($convert_duration >= 2){
                    $nominal_lembur = 67500;
                } elseif ($convert_duration >= 1){
                    $nominal_lembur = 32500;
                } else {
                    $nominal_lembur = 0;
                }

            //PERHITUNGAN UNTUK DEPARTEMEN HEAD
            } elseif ($jabatan_id == 3) {
                if ($convert_duration >= 4){
                    $nominal_lembur = 400000;
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
            'rencana_mulai_lemburEdit.*' => ['required', 'date_format:Y-m-d\TH:i'],
            'rencana_selesai_lemburEdit.*' => ['required', 'date_format:Y-m-d\TH:i'],
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
                    'rencana_mulai_lemburEditNew.*' => ['required', 'date_format:Y-m-d\TH:i'],
                    'rencana_selesai_lemburEditNew.*' => ['required', 'date_format:Y-m-d\TH:i'],
                ];
        
                $validator = Validator::make(request()->all(), $dataValidate);
            
                if ($validator->fails()) {
                    $errors = $validator->errors()->all();
                    return response()->json(['message' => $errors], 402);
                }
    
                $data_detail_lembur_new = [];
                foreach ($karyawan_ids_new as $key => $karyawan_id_new) {
                    $karyawan_new = Karyawan::find($karyawan_id_new);
                    $datetime_rencana_mulai_lembur_new = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_mulai_lemburs_new[$key])->toDateTimeString();
                    $datetime_rencana_selesai_lembur_new = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_selesai_lemburs_new[$key])->toDateTimeString();
                    $durasi_new = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur_new, $datetime_rencana_selesai_lembur_new);
    
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
                        'departemen_id' => $karyawan_new->posisi[0]->departemen_id,
                        'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur_new,
                        'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur_new,
                        'deskripsi_pekerjaan' => $job_descriptions_new[$key],
                        'durasi' => $durasi_new,
                        'nominal' => $nominal_new
                    ];
    
                    $total_durasi+= $durasi_new;
                }
                
                $lembur->detailLembur()->createMany($data_detail_lembur_new);
            }

            foreach ($karyawan_ids as $key => $id_kry){
                $karyawan = Karyawan::find($id_kry);
                $datetime_rencana_mulai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_mulai_lemburs[$key])->toDateTimeString();
                $datetime_rencana_selesai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $rencana_selesai_lemburs[$key])->toDateTimeString();
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur);

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
                    'departemen_id' => $karyawan->posisi[0]->departemen_id,
                    'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'deskripsi_pekerjaan' => $job_descriptions[$key],
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

        $posisi = auth()->user()->karyawan->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps){
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('nama', 'ILIKE', "%{$search}%");
            });
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        if(auth()->user()->karyawan->posisi[0]->jabatan_id == 5){
            $query->whereIn('posisis.id_posisi', $id_posisi_members);
            $query->orWhere('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
        } else {
            $query->where('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
        }

        $query->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama',);

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

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
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

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

    public function get_data_lembur(string $id_lembur)
    {
        try {
            $lembur = Lembure::findOrFail($id_lembur);
            $data_detail_lembur = [];
            foreach ($lembur->detailLembur as $data){
                //rencana
                $duration_rencana = $this->calculate_overtime_per_minutes($data->rencana_mulai_lembur, $data->rencana_selesai_lembur);
                $hour_rencana = floor($duration_rencana / 60);
                $minutes_rencana = $duration_rencana % 60;
                
                //aktual
                $duration_aktual = $this->calculate_overtime_per_minutes($data->aktual_mulai_lembur, $data->aktual_selesai_lembur);
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

    public function checked(Request $request, string $id_lembur)
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
        $approved_detail = $request->approved_detail;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            $detail_lembur = $lembur->detailLembur;
            $karyawan = auth()->user()->karyawan->nama;
            $total_durasi = $lembur->total_durasi;

            if($is_planned == 'N'){
                if(!$approved_detail){
                    DB::commit();
                    return response()->json(['message' => 'Minimal ada 1 orang yang di Checked !'], 403);
                } else {
                    $approved_detail = explode(',', $approved_detail);
                }
                
                if($lembur->plan_checked_by !== null){
                    return response()->json(['message' => 'Pengajuan Lembur sudah di checked !'], 403);
                }
    
                // Delete detail lembur yang tidak ada di approved_detail
                foreach ($detail_lembur as $detail) {
                    if (!in_array($detail->id_detail_lembur, $approved_detail)) {
                        $detail->is_rencana_approved = 'N';
                        $detail->is_aktual_approved = 'N';
                        $detail->save();

                        $lembur->total_durasi -= $detail->durasi;
                    }
                }

                $lembur->plan_checked_by = $karyawan;
                $lembur->plan_checked_at = now();

            } else {
                if($lembur->actual_checked_by !== null){
                    return response()->json(['message' => 'Aktual Lembur sudah di checked!'], 403);
                }

                $lembur->actual_checked_by = $karyawan;
                $lembur->actual_checked_at = now();
            }

            $lembur->save();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Checked!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function approved(Request $request, string $id_lembur)
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
        $approved_detail = $request->approved_detail;

        DB::beginTransaction();
        try{
            $lembur = Lembure::find($id_lembur);
            $detail_lembur = $lembur->detailLembur;
            $karyawan = auth()->user()->karyawan->nama;
            $total_durasi = $lembur->total_durasi;

            if($is_planned == 'N'){
                if(!$approved_detail){
                    DB::commit();
                    return response()->json(['message' => 'Minimal ada 1 orang yang di Approved!'], 403);
                } else {
                    $approved_detail = explode(',', $approved_detail);
                }
                
                if($lembur->plan_checked_by == null){
                    return response()->json(['message' => 'Pengajuan Lembur belum di check oleh Sect.Head/Dept.Head !'], 403);
                }
    
                if($lembur->plan_approved_by !== null){
                    return response()->json(['message' => 'Pengajuan Lembur sudah di Approved !'], 403);
                }

                // Delete detail lembur yang tidak ada di approved_detail
                foreach ($detail_lembur as $detail) {
                    if($detail->is_rencana_approved == 'Y'){
                        if (!in_array($detail->id_detail_lembur, $approved_detail)) {
                            $detail->is_rencana_approved = 'N';
                            $detail->is_aktual_approved = 'N';
                            $detail->save();
                            
                            $lembur->total_durasi -= $detail->durasi;
                        }
                    }   
                }

                $lembur->plan_approved_by = $karyawan;
                $lembur->plan_approved_at = now();

            } else {
                if($lembur->actual_checked_by == null){
                    return response()->json(['message' => 'Aktual Lembur belum di check oleh Sect.Head/Dept.Head !'], 403);
                }
    
                if($lembur->actual_approved_by !== null){
                    return response()->json(['message' => 'Aktual Lembur sudah di Approved !'], 403);
                }

                $lembur->actual_approved_by = $karyawan;
                $lembur->actual_approved_at = now();
            }

            $lembur->save();
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

                // CREATE LEMBUR HARIAN DATA
                $total_nominal = $lembur->detailLembur->where('is_aktual_approved', 'Y')->sum('nominal');
                $total_durasi = $lembur->detailLembur->where('is_aktual_approved', 'Y')->sum('durasi');
                $organisasi_id = auth()->user()->organisasi_id;
                $departemen_id = $lembur->issued->posisi[0]?->departemen_id;
                $divisi_id = $lembur->issued->posisi[0]?->divisi_id;
                $tanggal_lembur = Carbon::parse($lembur->aktual_mulai_lembur)->format('Y-m-d');
                
                $lembur_harian = LemburHarian::where('tanggal_lembur', $tanggal_lembur)->where('organisasi_id', $organisasi_id)->where('departemen_id', $departemen_id)->where('divisi_id', $divisi_id)->first();
                if ($lembur_harian){
                    $lembur_harian->total_durasi_lembur += $total_durasi;
                    $lembur_harian->total_nominal_lembur += $total_nominal;
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

            $lembur->save();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Legalized!'],200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function done(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'aktual_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i'],
            'aktual_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i'],
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
                        $datetime_aktual_mulai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $aktual_mulai_lemburs[$key])->toDateTimeString();
                        $datetime_aktual_selesai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $aktual_selesai_lemburs[$key])->toDateTimeString();
                        $durasi = $this->calculate_overtime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur);
        
                        if($durasi < 60){
                            DB::rollback();
                            return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                        }
    
                        $nominal = $this->calculate_overtime_nominal($detail->jenis_hari, $durasi, $detail->karyawan_id);
                        $detail->aktual_mulai_lembur = $datetime_aktual_mulai_lembur;
                        $detail->aktual_selesai_lembur = $datetime_aktual_selesai_lembur;
                        $detail->durasi = $durasi;
                        $detail->nominal = $nominal;
                        
                        // Hitung durasi dan nominal Aktual
                        $total_durasi_aktual += $durasi;
                    }
                }
                $detail->keterangan = isset($keterangan[$key]) ? $keterangan[$key] : null;
                $detail->save();
            }

            
            // foreach ($is_aktual_approved as $key => $id_detail_lembur){
            //     dd($is_aktual_approved);
            //     $detail = DetailLembur::find($id_detail_lembur);
            //     if($detail && $detail->is_aktual_approved == 'Y'){
            //         $datetime_aktual_mulai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $aktual_mulai_lemburs[$key])->toDateTimeString();
            //         $datetime_aktual_selesai_lembur = Carbon::createFromFormat('Y-m-d\TH:i', $aktual_selesai_lemburs[$key])->toDateTimeString();
            //         $durasi = $this->calculate_overtime_per_minutes($datetime_aktual_mulai_lembur, $datetime_aktual_selesai_lembur);
            //         dd($durasi);
    
            //         if($durasi < 60){
            //             DB::rollback();
            //             return response()->json(['message' => 'Durasi lembur '.$detail->karyawan->nama.' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
            //         }

            //         $nominal = $this->calculate_overtime_nominal($detail->jenis_hari, $durasi, $detail->karyawan_id);
            //         $detail->durasi = $durasi;
            //         $detail->nominal = $nominal;
                    
            //         // Hitung durasi dan nominal Aktual
            //         $total_durasi_aktual += $durasi;
            //     }
            //     $detail->save();
            // }

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
}
