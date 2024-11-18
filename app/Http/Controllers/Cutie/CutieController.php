<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Event;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
            'pageTitle' => "Cutie - Dashboard",
            'page' => 'cutie-dashboard',
            'jenis_cuti_title' => $jenis_cuti_title,
            'detail_cuti_title' => $detail_cuti_title,
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
        $departemens = Departemen::all();

        $dataPage = [
            'pageTitle' => "Cutie - Member Cuti",
            'page' => 'cutie-member-cuti',
            'departemen' => $departemens
        ];
        return view('pages.cuti-e.member-cuti', $dataPage);
    }

    public function personalia_cuti_view()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Cutie - Personalia",
            'page' => 'cutie-personalia-cuti',
            'departemen' => $departemens,
        ];
        return view('pages.cuti-e.personalia-cuti', $dataPage);
    }

    public function export_cuti_view()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Cutie - Export Data",
            'page' => 'cutie-export',
            'departemen' => $departemens,
        ];
        return view('pages.cuti-e.export-cuti', $dataPage);
    }

    public function bypass_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Bypass Cuti",
            'page' => 'cutie-bypass-cuti',
        ];
        return view('pages.cuti-e.bypass-cuti', $dataPage);
    }

    public function setting_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Setting Cuti Khusus",
            'page' => 'cutie-setting',
        ];
        return view('pages.cuti-e.setting-cuti', $dataPage);
    }

    public function pengajuan_cuti_datatable(Request $request)
    {

        $columns = array(
            // 0 => 'id_cuti',
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'aktual_mulai_cuti',
            4 => 'aktual_selesai_cuti',
            5 => 'durasi_cuti',
            6 => 'jenis_cuti',
            7 => 'checked1_at',
            8 => 'checked2_at',
            9 => 'approved_at',
            10 => 'legalize_at',
            11 => 'status_dokumen',
            12 => 'status_cuti',
            13 => 'alasan_cuti',
            14 => 'kp.nama_pengganti',
            15 => 'created_at',
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

        $totalData = Cutie::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $cutie = Cutie::getData($dataFilter, $settings);
        $totalFiltered = $cutie->count();
        $dataTable = [];
        

        if (!empty($cutie)) {
            foreach ($cutie as $data) {
                if($data->rejected_by == null){
                    $rejected = null;
                    if($data->checked1_by == null){
                        $btn_group_1 = '-';
                    } else {
                        $btn_group_1 = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                    } 

                    if($data->checked2_by == null){
                        $btn_group_2 = '-';
                    } else {
                        $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                    }

                    if($data->approved_by == null){
                        $btn_group_3 = '-';
                    } else {
                        $btn_group_3 = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                    }
                    
                    if($data->legalized_by == null){
                        if($data->approved_by !== null){
                            $legalized = 'Waiting For Legalized';
                        } else {
                            $legalized = 'Waiting Approved';
                        }
                    } else {
                        $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                    }
                } else {
                    $rejected = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                //Status Cuti
                if($data->rejected_by == null){
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
                    if($data->checked1_by == null){
                        $btn_group_1 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    } 
                    
                    if($data->checked2_by == null){
                        $btn_group_2 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    } 
                    
                    if($data->approved_by == null){
                        $btn_group_3 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }
                    
                    if ($data->legalized_by == null){
                        $legalized = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }
                    
                    $btn_karyawan_pengganti = '-';
                }
                    

                $nestedData['no'] = $data->id_cuti;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger btnAlasan" data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? '<small class="text-bold">'.$data->nama_pengganti.'</small>' : '-';
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    // ($data->checked1_by == null && $data->checked2_by == null && $data->approved_by == null && $data->legalized_by == null && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_cuti.'"><i class="fas fa-edit"></i> Edit</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                    ($data->status_cuti !== 'CANCELED' ? (date('Y-m-d') <= $data->rencana_mulai_cuti && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnCancel" data-id="'.$data->id_cuti.'"><i class="fas fa-history"></i> Cancel </button>' : '') : '').
                    ($data->checked1_by == null && $data->checked2_by == null && $data->approved_by == null && $data->legalized_by == null && $data->rejected_by == null && $data->status_cuti !== 'CANCELED' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                    // ($data->status_cuti == 'SCHEDULED' && $data->status_dokumen == 'APPROVED' && $data->approved_by !== null && $data->aktual_mulai_cuti == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-info btnMulai" data-id="'.$data->id_cuti.'"><i class="fas fa-play-circle"></i> Mulai </button>' : '').
                    // ($data->status_cuti == 'ON LEAVE' && $data->status_dokumen == 'APPROVED' && $data->approved_by !== null && $data->aktual_mulai_cuti !== null && $data->aktual_selesai_cuti == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-primary btnSelesai" data-id="'.$data->id_cuti.'"><i class="fas fa-calendar-check"></i> Selesai </button>' : '').
                    '
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
            1 => 'departemens.nama',
            2 => 'rencana_mulai_cuti',
            3 => 'rencana_selesai_cuti',
            4 => 'aktual_mulai_cuti',
            5 => 'aktual_selesai_cuti',
            6 => 'durasi_cuti',
            7 => 'jenis_cuti',
            8 => 'checked1_at',
            9 => 'checked2_at',
            10 => 'approved_at',
            11 => 'legalize_at',
            12 => 'status_dokumen',
            13 => 'status_cuti',
            14 => 'alasan_cuti',
            15 => 'kp.nama_pengganti',
            16 => 'created_at',
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
        if(auth()->user()->hasRole('atasan')){
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
                $my_jabatan = auth()->user()->karyawan->posisi[0]->jabatan_id;
                $posisi_karyawan = Karyawan::find($data->karyawan_id)->posisi;

                foreach($posisi_karyawan as $ps){
                    $id_posisi_parents = $this->get_parent_posisi($ps);
                }

                //KONDISI CHECKED
                if($data->rejected_by == null){
                    $rejected = null;
                    $jumlah_parent = count($id_posisi_parents) - 1;

                    //JUMLAH PARENT 5
                    if ($jumlah_parent >= 5) {
                        if($data->checked1_by == null){
                            if($my_jabatan == 5 || $my_jabatan == 4){
                                $checked1 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="checked_1"><i class="far fa-check-circle"></i> Checked</button>';
                                $reject1 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                $btn_group_1 = '<div class="btn-group btn-sm">'.$checked1.$reject1.'</div>';
                            } else {
                                $btn_group_1 = 'Need Checked';
                            }
                        } else {
                            $btn_group_1 = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                        } 

                        if($data->checked2_by == null){
                            if($my_jabatan == 5 || $my_jabatan == 4){
                                if($data->checked1_by !== null){
                                    if($data->checked1_by !== auth()->user()->karyawan->nama){
                                        $checked2 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="checked_2"><i class="far fa-check-circle"></i> Checked</button>';
                                        $reject2 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                        $btn_group_2 = '<div class="btn-group btn-sm">'.$checked2.$reject2.'</div>';
                                    } else {
                                        $btn_group_2 = 'Need Checked';
                                    }
                                } else {
                                    $btn_group_2 = 'Need Checked';
                                }
                            } else {
                                $btn_group_2 = 'Need Checked';
                            }
                        } else {
                            $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                        }
    
                        if($data->approved_by == null){
                            if($my_jabatan == 4 || $my_jabatan == 3){
                                if($data->checked2_by !== null){
                                    if($data->checked1_by !== auth()->user()->karyawan->nama){
                                        $approved = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>';
                                        $reject3 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                        $btn_group_3 = '<div class="btn-group btn-sm">'.$approved.$reject3.'</div>';
                                    } else {
                                        $btn_group_3 = 'Waiting For Approved';
                                    }
                                } else {
                                    $btn_group_3 = 'Waiting Checked 1 & 2';
                                }
                            } else {
                                $btn_group_3 = 'Waiting Approved';
                            }
                        } else {
                            $btn_group_3 = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                        }
                        
                        if($data->legalized_by == null){
                            if($data->approved_by !== null){
                                $legalized = 'Waiting For Legalized';
                            } else {
                                $legalized = 'Waiting Approved';
                            }
                        } else {
                            $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                        }

                    // JUMLAH PARENT 4
                    } elseif ($jumlah_parent >= 4){
                        if($data->checked1_by == null){
                            if($my_jabatan == 4 || $my_jabatan == 3 || $my_jabatan == 5){
                                $checked1 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="checked_1"><i class="far fa-check-circle"></i> Checked</button>';
                                $reject1 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                $btn_group_1 = '<div class="btn-group btn-sm">'.$checked1.$reject1.'</div>';
                            } else {
                                $btn_group_1 = 'Need Checked';
                            }
                        } else {
                            $btn_group_1 = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                        } 

                        if($data->checked2_by == null){
                            if($my_jabatan == 4 || $my_jabatan == 3 || $my_jabatan == 5){
                                if($data->checked1_by !== null){
                                    if($data->checked1_by !== auth()->user()->karyawan->nama){
                                        $checked2 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="checked_2"><i class="far fa-check-circle"></i> Checked</button>';
                                        $reject2 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                        $btn_group_2 = '<div class="btn-group btn-sm">'.$checked2.$reject2.'</div>';
                                    } else {
                                        $btn_group_2 = 'Need Checked';
                                    }
                                } else {
                                    $btn_group_2 = 'Need Checked';
                                }
                            } else {
                                $btn_group_2 = 'Need Checked';
                            }
                        } else {
                            $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                        }
    
                        if($data->approved_by == null){
                            if($my_jabatan == 3 || $my_jabatan == 2){
                                if($data->checked2_by !== null){
                                    $approved = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'" data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>';
                                    $reject3 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                    $btn_group_3 = '<div class="btn-group btn-sm">'.$approved.$reject3.'</div>';
                                } else {
                                    $btn_group_3 = 'Need Checked 1 & 2';
                                }
                            } else {
                                $btn_group_3 = 'Waiting Approved';
                            }
                        } else {
                            $btn_group_3 = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                        }
                        
                        if($data->legalized_by == null){
                            if($data->approved_by !== null){
                                $legalized = 'Waiting For Legalized';
                            } else {
                                $legalized = 'Waiting Approved';
                            }
                        } else {
                            $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                        }
                    // JUMLAH PARENT 3
                    } elseif ($jumlah_parent >= 1) {
                        if($data->checked1_by == null){
                            $btn_group_1 = 'Directly Approve';
                        } else {
                            $btn_group_1 = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                        } 

                        if($data->checked2_by == null){
                            $btn_group_2 = 'Directly Approve';
                        } else {
                            $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                        }

                        if($data->approved_by == null){
                            $btn_group_3 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'" data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="approved"><i class="fas fa-thumbs-up"></i> Approved</button>';
                            $reject3 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                            $btn_group_3 = '<div class="btn-group btn-sm">'.$btn_group_3.$reject3.'</div>';
                        } else {
                            $btn_group_3 = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                        }
                        
                        if($data->legalized_by == null){
                            if($data->approved_by !== null){
                                $legalized = 'Waiting For Legalized';
                            } else {
                                $legalized = 'Waiting Approved';
                            }
                        } else {
                            $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                        }
                    } 
                } else {
                    $rejected = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }
                
                //Karyawan Pengganti 
                if ($data->nama_pengganti && $data->legalized_by == null && $data->rejected_by == null){
                    $btn_karyawan_pengganti = '<small class="text-bold">'.$data->nama_pengganti.'</small><br>'.'<button type="button" class="waves-effect waves-light btn btn-sm btn-secondary btnKaryawanPengganti" data-id="'.$data->id_cuti.'" data-karyawan-id="'.$data->karyawan_id.'" data-karyawan-pengganti-id="'.$data->karyawan_pengganti_id.'"><i class="fas fa-user-friends"></i> Pilih</button>';
                } elseif($data->nama_pengganti && $data->legalized_by !== null && $data->rejected_by == null){
                    $btn_karyawan_pengganti = '<small class="text-bold">'.$data->nama_pengganti.'</small>';
                } elseif ($data->nama_pengganti == null && $data->legalized_by == null && $data->rejected_by == null){ 
                    $btn_karyawan_pengganti = '<button type="button" class="waves-effect waves-light btn btn-sm btn-secondary btnKaryawanPengganti" data-id="'.$data->id_cuti.'" data-karyawan-id="'.$data->karyawan_id.'" data-karyawan-pengganti-id="'.$data->karyawan_pengganti_id.'"><i class="fas fa-user-friends"></i> Pilih</button>';
                } else {
                    $btn_karyawan_pengganti = '-';
                }

                //Status Cuti
                if($data->rejected_by == null){
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
                    if($data->checked1_by == null){
                        $btn_group_1 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    } 
                    
                    if($data->checked2_by == null){
                        $btn_group_2 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    } 
                    
                    if($data->approved_by == null){
                        $btn_group_3 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }
                    
                    if ($data->legalized_by == null){
                        $legalized = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }

                    $btn_karyawan_pengganti = '-';
                }

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger btnAlasan" data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $btn_karyawan_pengganti;
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

    public function personalia_cuti_datatable(Request $request)
    {

        $columns = array(
            0 => 'karyawans.nama',
            1 => 'departemens.nama',
            2 => 'rencana_mulai_cuti',
            3 => 'rencana_selesai_cuti',
            4 => 'aktual_mulai_cuti',
            5 => 'aktual_selesai_cuti',
            6 => 'durasi_cuti',
            7 => 'jenis_cuti',
            8 => 'checked1_at',
            9 => 'checked2_at',
            10 => 'approved_at',
            11 => 'legalized_at',
            12 => 'status_dokumen',
            13 => 'status_cuti',
            14 => 'alasan_cuti',
            15 => 'kp.nama_pengganti',
            16 => 'created_at',
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

        $organisasi_id = auth()->user()->organisasi_id;
        if ($organisasi_id) {
            $dataFilter['organisasi_id'] = $organisasi_id;
        }
        
        $cutie = Cutie::getData($dataFilter, $settings);
        $totalFiltered = $cutie->count();
        // $totalFiltered = Cutie::countData($dataFilter);

        $dataTable = [];
        

        if (!empty($cutie)) {
            foreach ($cutie as $data) {
                if($data->rejected_by == null){
                    $rejected = null;
                    if($data->checked1_by == null){
                        $btn_group_1 = '-';
                    } else {
                        $btn_group_1 = '✅<br><small class="text-bold">'.$data->checked1_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked1_at)->diffForHumans().'</small>';
                    } 

                    if($data->checked2_by == null){
                        $btn_group_2 = '-';
                    } else {
                        $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                    }

                    if($data->approved_by == null){
                        $btn_group_3 = '-';
                    } else {
                        $btn_group_3 = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                    }
                    
                    if($data->legalized_by == null){
                        if($data->approved_by !== null){
                            $legalized = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'" data-issued-name="HRD & GA" data-type="legalized"><i class="fas fa-balance-scale"></i> Legalized</button>';
                            $reject = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="HRD & GA"><i class="far fa-times-circle"></i> Reject</button>';
                            $legalized = '<div class="btn-group btn-sm">'.$legalized.$reject.'</div>';
                        } else {
                            $legalized = 'Waiting Checked/Approved';
                        }
                    } else {
                        $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                    }
                } else {
                    $rejected = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
                }

                //Status Cuti
                if($data->rejected_by == null){
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
                    if($data->checked1_by == null){
                        $btn_group_1 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    } 
                    
                    if($data->checked2_by == null){
                        $btn_group_2 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    } 
                    
                    if($data->approved_by == null){
                        $btn_group_3 = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }
                    
                    if ($data->legalized_by == null){
                        $legalized = '<span class="badge badge-pill badge-danger">CANCELED</span>';
                    }
                    
                    $btn_karyawan_pengganti = '-';
                }
                    

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger btnAlasan"  data-alasan="'.$data->rejected_note.'" style="cursor:pointer;">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? '<small class="text-bold">'.$data->nama_pengganti.'</small>' : '-';
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = $data->status_cuti !== 'CANCELED' && $data->status_dokumen !== 'REJECTED' ? '<div class="btn-group btn-group-sm">
                <button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>
                '.(date('Y-m-d') <= Carbon::parse($data->rencana_mulai_cuti)->addDays(7)->format('Y-m-d') ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnCancel" data-id="'.$data->id_cuti.'"><i class="fas fa-history"></i> Cancel </button>' : '').'
                </div>' : '-';

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

    public function setting_cuti_datatable(Request $request)
    {
        $columns = array(
            0 => 'jenis',
            1 => 'durasi',
            2 => 'isUrgent'
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
        $totalFiltered = $jenis_cuti->count();

        $dataTable = [];
        

        if (!empty($jenis_cuti)) {
            foreach ($jenis_cuti as $data) {
                $nestedData['jenis'] = $data->jenis;
                $nestedData['durasi'] = $data->durasi.' Hari';
                $nestedData['isUrgent'] = $data->isUrgent == 'N' ? '❌' : '✅';
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

    public function get_data_jenis_cuti_khusus(){
        $data = JenisCuti::all();
        foreach ($data as $jc) {
            $dataJenisCutiKhusus[] = [
                'id' => $jc->id_jenis_cuti,
                'text' => $jc->jenis,
                'durasi' => $jc->durasi,
                'isurgent' => $jc->isUrgent
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

    public function get_data_detail_jenis_cuti(string $id_jenis_cuti)
    {
        $jc = JenisCuti::find($id_jenis_cuti);
        $data = [
            'id_jenis_cuti' => $jc->id_jenis_cuti,
            'jenis' => $jc->jenis,
            'durasi' => $jc->durasi,
            'isUrgent' => $jc->isUrgent
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
        $kry = Karyawan::find($karyawan_id);
        $sisa_cuti_pribadi = $kry->sisa_cuti_pribadi;
        $sisa_cuti_tahun_lalu = $kry->sisa_cuti_tahun_lalu;
        $expired_date_cuti_tahun_lalu = $kry->expired_date_cuti_tahun_lalu;

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

        $organisasi_id = auth()->user()->organisasi_id;

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

            } elseif ($jenis_cuti == 'PRIBADI') {

                // if($expired_date_cuti_tahun_lalu){
                //     if($sisa_cuti_tahun_lalu <= 0 || ($sisa_cuti_tahun_lalu > 0 && $expired_date_cuti_tahun_lalu < date('Y-m-d'))){
                //         $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                //         if($sisa_cuti_pribadi < 0){
                //             return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                //         } else {
                //             if($jatah_cuti < 0){
                //                 return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                //             } else {
                //                 $karyawan = Karyawan::find($karyawan_id);
                //                 $karyawan->sisa_cuti_pribadi = $jatah_cuti;
                //                 $karyawan->save();
                //             }
                //         }
    
                //     } else {
                //         $jatah_cuti = $sisa_cuti_tahun_lalu - $durasi_cuti;
                //         $jatah_cuti_pengurangan_final =  $jatah_cuti + $sisa_cuti_pribadi;
    
                //         if($jatah_cuti_pengurangan_final < 0 ){
                //             return response()->json(['message' => 'Sisa cuti pribadi + Sisa cuti tahun lalu masih belum memenuhi jumlah durasi cuti yang diajukan!'], 402);
                //         } else {
                //             if($jatah_cuti < 0){
                //                 $karyawan = Karyawan::find($karyawan_id);
                //                 $karyawan->sisa_cuti_tahun_lalu = 0;
                //                 $karyawan->sisa_cuti_pribadi = $jatah_cuti_pengurangan_final;
                //                 $karyawan->save();
                //             } else {
                //                 $karyawan = Karyawan::find($karyawan_id);
                //                 $karyawan->sisa_cuti_tahun_lalu = $jatah_cuti;
                //                 $karyawan->save();
                //             }
                //         }
                //     }
                // } else {
                //     $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                //     if($sisa_cuti_pribadi < 0){
                //         return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                //     } else {
                //         if($jatah_cuti < 0){
                //             return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                //         } else {
                //             $karyawan = Karyawan::find($karyawan_id);
                //             $karyawan->sisa_cuti_pribadi = $jatah_cuti;
                //             $karyawan->save();
                //         }
                //     }
                // }

                // if($penggunaan_sisa_cuti == 'TB'){
                //     $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                //     if($sisa_cuti_pribadi < 0){
                //         return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                //     } else {
                //         if($jatah_cuti < 0){
                //             return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                //         } else {
                //             $karyawan = Karyawan::find($karyawan_id);
                //             $karyawan->sisa_cuti_pribadi = $jatah_cuti;
                //             $karyawan->save();
                //         }
                //     }
                // } else {
                //     $jatah_cuti = $sisa_cuti_tahun_lalu - $durasi_cuti;
                //     if($sisa_cuti_tahun_lalu < 0){
                //         return response()->json(['message' => 'Ada tidak memiliki sisa cuti tahun lalu!, Silahkan ajukan menggunakan sisa cuti tahun berjalan anda!'], 402);
                //     } else {
                //         if($jatah_cuti < 0 || $rencana_mulai_cuti > $expired_date_cuti_tahun_lalu){
                //             return response()->json(['message' => 'Sisa cuti tahun lalu anda tidak mencukupi atau sudah melebihi Expired Date, Silahkan ajukan menggunakan sisa cuti tahun berjalan anda!!'], 402);
                //         } else {
                //             $karyawan = Karyawan::find($karyawan_id);
                //             $karyawan->sisa_cuti_tahun_lalu = $jatah_cuti;
                //             $karyawan->save();
                //         }
                //     }
                // }

                $jatah_cuti = $sisa_cuti_pribadi - $durasi_cuti;
                if($sisa_cuti_pribadi < 0){
                    return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Hubungi HRD untuk informasi lebih lanjut!'], 402);
                } else {
                    if($jatah_cuti < 0){
                        return response()->json(['message' => 'Sisa cuti pribadi anda tidak mencukupi, Silahkan baca ketentuan pembagian cuti pribadi lagi!'], 402);
                    } else {
                        $kry->sisa_cuti_pribadi = $jatah_cuti;
                        $kry->save();
                    }
                }
                $attachment = null;
            } else {
                $attachment = null;
            }

            $cuti = Cutie::create([
                'karyawan_id' => $karyawan_id,
                'organisasi_id' => $organisasi_id,
                'jenis_cuti' => $jenis_cuti,
                'jenis_cuti_id' => $jenis_cuti_id,
                'attachment' => $attachment,
                'rencana_mulai_cuti' => $rencana_mulai_cuti,
                'rencana_selesai_cuti' => $rencana_selesai_cuti,
                'alasan_cuti' => $alasan_cuti,
                'durasi_cuti' => $durasi_cuti,
            ]);

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
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
    }

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
        $sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi;
        $sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu;
        $expired_date_cuti_tahun_lalu = $karyawan->expired_date_cuti_tahun_lalu;

        $penggunaan_sisa_cuti = $request->penggunaan_sisa_cuti;
        $rencana_mulai_cuti = $request->rencana_mulai_cuti;
        $rencana_selesai_cuti = $request->rencana_selesai_cuti;
        $alasan_cuti = $request->alasan_cuti;
        $durasi_cuti = $request->durasi_cuti;

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

            // $cuti = Cutie::create([
            //     'karyawan_id' => $id_karyawan,
            //     'organisasi_id' => $karyawan->user->organisasi_id,
            //     'jenis_cuti' => 'PRIBADI',
            //     'attachment' => null,
            //     'rencana_mulai_cuti' => $rencana_mulai_cuti,
            //     'rencana_selesai_cuti' => $rencana_selesai_cuti,
            //     'aktual_mulai_cuti' => $aktual_mulai_cuti,
            //     'aktual_selesai_cuti' => $aktual_selesai_cuti,
            //     'alasan_cuti' => $alasan_cuti,
            //     'durasi_cuti' => $durasi_cuti,
            //     'penggunaan_sisa_cuti' => $penggunaan_sisa_cuti,
            //     'status_cuti' => $status_cuti,
            //     'status_dokumen' => 'APPROVED',
            //     'checked1_by' => 'HRD & GA (BYPASS SYSTEM)',
            //     'checked1_at' => Carbon::now(),
            //     'checked2_by' => 'HRD & GA (BYPASS SYSTEM)',
            //     'checked2_at' => Carbon::now(),
            //     'approved_by' => 'HRD & GA (BYPASS SYSTEM)',
            //     'approved_at' => Carbon::now(),
            //     'legalized_by' => 'HRD & GA (BYPASS SYSTEM)',
            //     'legalized_at' => Carbon::now(),
            // ]);

            DB::commit();
            return response()->json(['message' => 'Bypass Cuti Berhasil Dilakukan!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
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
        $karyawan = auth()->user()->karyawan;

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
            return response()->json(['message' => 'Pengajuan cuti berhasil diubah, konfirmasi ke atasan untuk melakukan approval!', 'data' => $karyawan->sisa_cuti_pribadi + $karyawan->sisa_cuti_bersama], 200);
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

    public function reject(Request $request, string $id_cuti)
    {
        $nama_atasan = $request->nama_atasan;
        $alasan_reject = $request->alasan_reject;

        $dataValidate = [
            'nama_atasan' => ['required'],
            'alasan_reject' => ['required'],
        ];
        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => 'Alasan reject tidak boleh kosong!'], 402);
        }

        DB::beginTransaction();
        try{
            $cuti = Cutie::find($id_cuti);
            $cuti->rejected_by = $nama_atasan;
            $cuti->rejected_at = now();
            $cuti->rejected_note = $alasan_reject;
            $cuti->status_dokumen = 'REJECTED';
            $cuti->save();

            $karyawan = Karyawan::find($cuti->karyawan_id);

            if($cuti->jenis_cuti == 'PRIBADI'){
                if($cuti->penggunaan_sisa_cuti == 'TB'){
                    $karyawan->sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $cuti->durasi_cuti;
                } else {
                    $karyawan->sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $cuti->durasi_cuti;
                }
                $karyawan->save();
            }

            DB::commit();
            return response()->json(['message' => 'Reject Cuti Berhasil dilakukan!'], 200);
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

    public function store_jenis_cuti(Request $request)
    {
        $jenis = $request->jenis;
        $durasi = $request->durasi;
        $isUrgent = $request->isUrgent;

        $dataValidate = [
            'jenis' => ['required'],
            'durasi' => ['required', 'numeric', 'min:1'],
            'isUrgent' => ['required', 'string', 'in:Y,N'],
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

        $dataValidate = [
            'jenis' => ['required'],
            'durasi' => ['required', 'numeric', 'min:1'],
            'isUrgent' => ['required', 'string', 'in:Y,N'],
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

    public function update_dokumen_cuti(Request $request, string $id_cuti)
    {
        $type = $request->type;
        $issued_name = $request->issued_name;

        $dataValidate = [
            'issued_name' => ['required'],
            'type' => ['required'],
        ];
        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => 'Terjadi kesalahan, muat ulang halaman anda!'], 402);
        }

        DB::beginTransaction();
        try{
            $cuti = Cutie::find($id_cuti);
            if($type == 'checked_1'){
                $cuti->checked1_by = $issued_name;
                $cuti->checked1_at = now();
            } elseif ($type == 'checked_2'){
                $cuti->checked2_by = $issued_name;
                $cuti->checked2_at = now();
            } elseif ($type == 'approved'){
                $cuti->approved_by = $issued_name;
                $cuti->approved_at = now();
            } else {

                //LOGIKA UNTUK BYPASS CUTI
                if($cuti->rencana_mulai_cuti < date('Y-m-d', strtotime('+7 days')) && $cuti->jenis_cuti == 'PRIBADI'){
                    $cuti->legalized_by = $issued_name.' (BYPASS SYSTEM)';
                    $cuti->status_dokumen = 'APPROVED';

                    if($cuti->rencana_mulai_cuti > date('Y-m-d')){
                        $cuti->status_cuti = 'SCHEDULED';
                    } elseif ($cuti->rencana_mulai_cuti == date('Y-m-d')){
                        $cuti->status_cuti = 'ON LEAVE';
                    } else {
                        $cuti->status_cuti = 'COMPLETED';
                    }
                    
                    $cuti->status_cuti = 'COMPLETED';
                    $cuti->legalized_at = now();
                //LOGIKAN UNTUK CUTI BIASA
                } else {
                    $cuti->legalized_by = $issued_name;
                    $cuti->status_dokumen = 'APPROVED';
                    $cuti->status_cuti = 'SCHEDULED';
                    $cuti->legalized_at = now();
                }
            }
            
            $cuti->save();
            DB::commit();
            return response()->json(['message' => 'Update Dokumen Cuti Berhasil dilakukan!'], 200);
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

    public function update_karyawan_pengganti(Request $request, string $id_cuti)
    {
        $karyawan_pengganti_id = $request->karyawan_pengganti_id;

        $dataValidate = [
            'karyawan_pengganti_id' => ['required'],
        ];
        
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => 'Pilihlah karyawan pengganti terlebih dahulu!'], 402);
        }

        DB::beginTransaction();
        try{
            $cuti = Cutie::find($id_cuti);
            $cuti->karyawan_pengganti_id = $karyawan_pengganti_id;
            $cuti->save();
            DB::commit();
            return response()->json(['message' => 'Update Karyawan Pengganti Berhasil dilakukan!'], 200);
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

    public function cancel(Request $request, string $id_cuti)
    {
        DB::beginTransaction();
        try{
            $cutie = Cutie::find($id_cuti);
            $karyawan = Karyawan::find($cutie->karyawan_id);

            if($cutie->rejected_by == null && $cutie->jenis_cuti == 'PRIBADI'){
                if($cutie->penggunaan_sisa_cuti == 'TB'){
                    $karyawan->sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $cutie->durasi_cuti;
                } else {
                    $karyawan->sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $cutie->durasi_cuti;
                }
                $karyawan->save();
            }

            $data = [
                'sisa_cuti_tahunan' => $karyawan->sisa_cuti_pribadi + $karyawan->sisa_cuti_bersama,
                'sisa_cuti_pribadi' => $karyawan->sisa_cuti_pribadi,
                'sisa_cuti_tahun_lalu' => $karyawan->sisa_cuti_tahun_lalu
            ];

            $cutie->status_cuti = 'CANCELED';
            $cutie->save();
            DB::commit();
            return response()->json(['message' => 'Cuti Berhasil dicancel!', 'data' => $data ], 200);
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

    public function mulai_cuti(Request $request, string $id_cuti)
    {
        $cutie = Cutie::find($id_cuti);

        if($cutie->status_cuti !== 'SCHEDULED'){
            return response()->json(['message' => 'Cuti tidak bisa dimulai, status cuti harus SCHEDULED!'], 402);
        } elseif ($cutie->status_dokumen !== 'APPROVED'){
            return response()->json(['message' => 'Cuti tidak bisa dimulai, status dokumen harus APPROVED!'], 402);
        } elseif ($cutie->approved_by == null){
            return response()->json(['message' => 'Cuti tidak bisa dimulai, Harus melewati approval terlebih dahulu!'], 402);
        } elseif ($cutie->legalized_by == null){
            return response()->json(['message' => 'Cuti tidak bisa dimulai, Harus melewati legalisasi terlebih dahulu!'], 402);
        }

        DB::beginTransaction();
        try{
            $cutie->aktual_mulai_cuti = now();
            $cutie->status_cuti = 'ON LEAVE';
            $cutie->save();
            DB::commit();
            return response()->json(['message' => 'Mulai Cuti Berhasil dilakukan!'], 200);
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

    public function selesai_cuti(Request $request, string $id_cuti)
    {
        $cutie = Cutie::find($id_cuti);

        if($cutie->aktual_mulai_cuti == null){
            return response()->json(['message' => 'Tidak bisa menyelesaikan cuti, mulai saja belum!'], 402);
        } 

        DB::beginTransaction();
        try{
            $cutie->aktual_selesai_cuti = now();
            $cutie->status_cuti = 'COMPLETED';
            $cutie->save();
            DB::commit();
            return response()->json(['message' => 'Selesai Cuti Berhasil dilakukan!'], 200);
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
            $karyawan = Karyawan::find($cutie->karyawan_id);

            if($cutie->rejected_by == null && $cutie->jenis_cuti == 'PRIBADI'){
                if($cutie->penggunaan_sisa_cuti == 'TB'){
                    $karyawan->sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $cutie->durasi_cuti;
                } else {
                    $karyawan->sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $cutie->durasi_cuti;
                }
                $karyawan->save();
            }
            
            $cutie->delete();
            $data = [
                'sisa_cuti_tahunan' => $karyawan->sisa_cuti_pribadi + $karyawan->sisa_cuti_bersama,
                'sisa_cuti_pribadi' => $karyawan->sisa_cuti_pribadi,
                'sisa_cuti_tahun_lalu' => $karyawan->sisa_cuti_tahun_lalu
            ];
            DB::commit();
            return response()->json(['message' => 'Pengajuan Cuti Dihapus!', 'data' => $data],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_karyawan_pengganti(string $id_karyawan)
    {
        $departemen = Karyawan::find($id_karyawan)->posisi[0]->departemen_id;
        $karyawan = Karyawan::whereHas('posisi', function($query) use ($departemen){
            $query->where('departemen_id', $departemen);
        })->where('id_karyawan','!=',$id_karyawan)->get();
        foreach ($karyawan as $k) {
            $data[] = [
            'id' => $k->id_karyawan,
            'text' => $k->nama
            ];
        }
        return response()->json($data, 200);
    }

    public function get_data_cutie_calendar(){
        $organisasi_id = auth()->user()->organisasi_id;
        $cutie = Cutie::where('status_dokumen','APPROVED');

        if(auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = $this->get_member_posisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $members = $id_posisi_members;
        }

        if (isset($members)) {
            $cutie = $cutie->whereHas('karyawan.posisi', function($query) use ($members) {
                $query->whereIn('id_posisi', $members);
            });
            $cutie = $cutie->orWhere('karyawan_id', auth()->user()->karyawan->id_karyawan)->where('status_dokumen','APPROVED');
        } else {
            $cutie = $cutie->organisasi($organisasi_id);
        }

        $event = Event::organisasi($organisasi_id)->where('jenis_event', 'CB')->get();
        $cutie = $cutie->active()->get();
        $data = [];

        if($event){
            foreach ($event as $e) {
                if($e->jenis_event == 'CB'){
                    $className = 'bg-primary';
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
                if($c->status_cuti == 'SCHEDULED'){
                    $classname = 'bg-warning';
                } elseif ($c->status_cuti == 'ON LEAVE'){
                    $classname = 'bg-secondary';
                } elseif ($c->status_cuti == 'COMPLETED'){
                    $classname = 'bg-success';
                } else {
                    $classname = 'bg-danger';
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
                    'status_cuti' => $c->status_cuti,
                    'attachment' => $c->attachment ? '<a href="'.asset('storage/'.$c->attachment).'" target="_blank">Lihat</a>' : 'No Attachment Needed',
                    'aktual_mulai_cuti' => $c->aktual_mulai_cuti ? Carbon::parse($c->aktual_mulai_cuti)->format('d M Y') : '',
                    'aktual_selesai_cuti' => $c->aktual_selesai_cuti ? Carbon::parse($c->aktual_selesai_cuti)->format('d M Y') : '',
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
            $id_posisi_members = $this->get_member_posisi($posisi);

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
            $id_posisi_members = $this->get_member_posisi($posisi);

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

        // $monthly_sakit = Cutie::where('jenis_cuti', 'SAKIT')
        //     ->whereYear('rencana_mulai_cuti', $year)
        //     ->whereMonth('rencana_mulai_cuti', $month)
        //     ->where('status_cuti', '!=' ,'CANCELED')
        //     ->where('status_dokumen', 'APPROVED');

        //     if (isset($members)) {
        //         $monthly_sakit = $monthly_sakit->whereHas('karyawan.posisi', function($query) use ($members) {
        //             $query->whereIn('id_posisi', $members);
        //         });
        //     } else {
        //         $monthly_sakit = $monthly_sakit->organisasi($organisasi_id);
        //     }
        // $monthly_sakit = $monthly_sakit->count();
        

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
                            $tanggal_cuti[] = $r->format('Y-m-d');
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
}
