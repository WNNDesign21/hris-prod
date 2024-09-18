<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        $dataPage = [
            'pageTitle' => "Cutie - Member Cuti",
            'page' => 'cutie-member-cuti',
        ];
        return view('pages.cuti-e.member-cuti', $dataPage);
    }

    public function personalia_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Personalia",
            'page' => 'cutie-personalia-cuti',
        ];
        return view('pages.cuti-e.personalia-cuti', $dataPage);
    }

    public function export_cuti_view()
    {
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Cutie - Export Data",
            'page' => 'cutie-export',
            'departemens' => $departemens,
        ];
        return view('pages.cuti-e.export-cuti', $dataPage);
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
            7 => 'alasan_cuti',
            8 => 'kp.nama_pengganti',
            9 => 'checked1_at',
            10 => 'checked2_at',
            11 => 'approved_at',
            12 => 'legalize_at',
            13 => 'status_dokumen',
            14 => 'status_cuti',
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
                    $status_cuti = '<span class="badge badge-pill badge-danger">REJECTED</span>';
                }
                    

                $nestedData['no'] = $data->id_cuti;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? '<small class="text-bold">'.$data->nama_pengganti.'</small>' : '-';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    // ($data->checked1_by == null && $data->checked2_by == null && $data->approved_by == null && $data->legalized_by == null && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_cuti.'"><i class="fas fa-edit"></i> Edit</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                    ($data->status_dokumen == 'APPROVED' && $data->status_cuti == 'SCHEDULED' && $data->rencana_mulai_cuti <= date('Y-m-d') && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnCancel" data-id="'.$data->id_cuti.'"><i class="fas fa-history"></i> Cancel </button>' : '').
                    ($data->checked1_by == null && $data->checked2_by == null && $data->approved_by == null && $data->legalized_by == null && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
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
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'aktual_mulai_cuti',
            4 => 'aktual_selesai_cuti',
            5 => 'durasi_cuti',
            6 => 'jenis_cuti',
            7 => 'alasan_cuti',
            8 => 'kp.nama_pengganti',
            9 => 'checked1_at',
            10 => 'checked2_at',
            11 => 'approved_at',
            12 => 'legalize_at',
            13 => 'status_dokumen',
            14 => 'status_cuti',
            15 => 'created_at',
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
                                    if($data->checked1_by !== auth()->user()->karyawan->nama && $data->checked2_by !== auth()->user()->karyawan->nama){
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
                    $status_cuti = '<span class="badge badge-pill badge-danger">REJECTED</span>';
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
                    $status_cuti = '<span class="badge badge-pill badge-danger">REJECTED</span>';
                }

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $btn_karyawan_pengganti;
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
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
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'aktual_mulai_cuti',
            4 => 'aktual_selesai_cuti',
            5 => 'durasi_cuti',
            6 => 'jenis_cuti',
            7 => 'alasan_cuti',
            8 => 'kp.nama_pengganti',
            9 => 'checked1_at',
            10 => 'checked2_at',
            11 => 'approved_at',
            12 => 'legalize_at',
            13 => 'status_dokumen',
            14 => 'status_cuti',
            15 => 'created_at',
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
                    $rejected = '❌<br><small class="text-bold btnAlasan" style="cursor:pointer;" data-alasan="'.$data->rejected_note.'">'.$data->rejected_by.'</small><br><small class="text-fade">'.Carbon::parse($data->rejected_at)->diffForHumans().'</small>';
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
                    $status_cuti = '<span class="badge badge-pill badge-danger">REJECTED</span>';
                }
                    

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? '<small class="text-bold">'.$data->nama_pengganti.'</small>' : '-';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $status_cuti;
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm"><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button></div>';

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
        $sisa_cuti = Karyawan::find($karyawan_id)->sisa_cuti;

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
            } elseif ($jenis_cuti == 'PRIBADI') {
                $sisa_cuti = $sisa_cuti - $durasi_cuti;
                if($sisa_cuti < 0){
                    return response()->json(['message' => 'Sisa cuti anda tidak mencukupi, Silahkan hubungi HRD!'], 402);
                } else {
                    $karyawan = Karyawan::find($karyawan_id);
                    $karyawan->sisa_cuti = $sisa_cuti;
                    $karyawan->save();
                }
                $attachment = null;
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
            $karyawan->sisa_cuti = $karyawan->sisa_cuti + $cuti->durasi_cuti;
            $karyawan->save();

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
                $cuti->legalized_by = $issued_name;
                $cuti->status_dokumen = 'APPROVED';
                $cuti->status_cuti = 'SCHEDULED';
                $cuti->legalized_at = now();
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
                $karyawan->sisa_cuti = $karyawan->sisa_cuti + $cutie->durasi_cuti;
                $karyawan->save();
            }

            $cutie->status_cuti = 'CANCELED';
            $cutie->save();
            DB::commit();
            return response()->json(['message' => 'Cuti Berhasil dicancel!'], 200);
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
                $karyawan->sisa_cuti = $karyawan->sisa_cuti + $cutie->durasi_cuti;
                $karyawan->save();
            }
            
            $cutie->delete();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Cuti Dihapus!'],200);
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
        }

        $cutie = $cutie->active()->get();
        $data = [];
        
        if($cutie){
            foreach ($cutie as $c) {
                if($c->status_cuti == 'SCHEDULED'){
                    $classname = 'bg-warning';
                } elseif ($c->status_cuti == 'ON LEAVE'){
                    $classname = 'bg-secondary';
                } else {
                    $classname = 'bg-success';
                }
                $data[] = [
                    'title' => $c->jenis_cuti.' - '.$c->karyawan->nama,
                    'start' => $c->rencana_mulai_cuti,
                    'end' => $c->rencana_selesai_cuti !== $c->rencana_mulai_cuti ? Carbon::parse($c->rencana_selesai_cuti)->addDay()->format('Y-m-d') : $c->rencana_selesai_cuti,
                    'className' => $classname,
                    'nama_karyawan' => $c->karyawan->nama,
                    'karyawan_pengganti' => $c->karyawan_pengganti_id ? $c->karyawanPengganti->nama : '-',
                    'jenis_cuti' => $c->jenis_cuti,
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
        //Data Cuti Detail perbulan dalam tahun berjalan
        $data['scheduled'] = [];
        $data['onleave'] = [];
        $data['canceled'] = [];
        $data['completed'] = [];
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
            $scheduledCount = Cutie::where('status_cuti', 'SCHEDULED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $scheduledCount = $scheduledCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
                
            $scheduledCount = $scheduledCount->count();

            $onleaveCount = Cutie::where('status_cuti', 'ON LEAVE')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $onleaveCount = $onleaveCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
            $onleaveCount = $onleaveCount->count();
            
            $completedCount = Cutie::where('status_cuti', 'COMPLETED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);
            
            if (isset($members)) {
                $completedCount = $completedCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
            $completedCount = $completedCount->count();

            $canceledCount = Cutie::where('status_cuti', 'CANCELED')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);
            
            if (isset($members)) {
                $canceledCount = $canceledCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
            $canceledCount = $canceledCount->count();

            $totalCount = Cutie::whereIn('status_cuti', ['SCHEDULED','ON LEAVE','COMPLETED','CANCELED'])
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $totalCount = $totalCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
            $totalCount = $totalCount->count();

            $unlegalizedCount = Cutie::whereNull('status_cuti')
                ->whereYear('rencana_mulai_cuti', $year)
                ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

            if (isset($members)) {
                $unlegalizedCount = $unlegalizedCount->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
            $unlegalizedCount = $unlegalizedCount->count();

            $data['scheduled'][] = $scheduledCount;
            $data['onleave'][] = $onleaveCount;
            $data['completed'][] = $completedCount;
            $data['canceled'][] = $canceledCount;
            $data['unlegalized'][] = $unlegalizedCount;
            $data['total'][] = $totalCount;
        }

        return response()->json(['data' => $data],200);
    }

    public function get_data_jenis_cuti_monthly_chart(){
        $month = date('m');
        $year = date('Y');

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
            ->where('status_dokumen', 'APPROVED');

            if (isset($members)) {
                $monthly_pribadi = $monthly_pribadi->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
        $monthly_pribadi = $monthly_pribadi->count();

        $monthly_khusus = Cutie::where('jenis_cuti', 'KHUSUS')
            ->whereYear('rencana_mulai_cuti', $year)
            ->whereMonth('rencana_mulai_cuti', $month)
            ->where('status_dokumen', 'APPROVED');

            if (isset($members)) {
                $monthly_khusus = $monthly_khusus->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
        $monthly_khusus = $monthly_khusus->count();

        $monthly_sakit = Cutie::where('jenis_cuti', 'SAKIT')
            ->whereYear('rencana_mulai_cuti', $year)
            ->whereMonth('rencana_mulai_cuti', $month)
            ->where('status_dokumen', 'APPROVED');

            if (isset($members)) {
                $monthly_sakit = $monthly_sakit->whereHas('karyawan.posisi', function($query) use ($members) {
                    $query->whereIn('id_posisi', $members);
                });
            }
        $monthly_sakit = $monthly_sakit->count();
        

        $data = [$monthly_pribadi, $monthly_khusus, $monthly_sakit];
        return response()->json(['data' => $data], 200);
    }

    public function export_cuti(Request $request){

        //GET DATA CUTI BY FILTER
        $departemen_id = $request->departemen_id;

        //Jenis Cuti
        $pribadi = $request->pribadi;
        $khusus = $request->khusus;
        $sakit = $request->sakit;

        //Range Data Cuti
        $start_date = $request->from;
        $end_date = $request->to;

        $cutie = Cutie::whereBetween('rencana_mulai_cuti', [$start_date, $end_date]);

        if($departemen_id !== 'all'){
            $cutie = $cutie->whereHas('karyawan.posisi', function($query) use ($departemen_id){
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

        if($sakit == 'Y'){
            array_push($jenis_cuti, 'SAKIT');
        }

        if(!empty($jenis_cuti)){
            $cutie = $cutie->whereIn('jenis_cuti', $jenis_cuti)->get();
        } else {
            $cutie = $cutie->get();
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
                    'argb' => 'FFFFFF00'
                ]
            ],
            'font' => [
                'bold' => true,
            ],
        ];
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Cuti');
        $row = 1;
        $col = 'A';
        $headers = [
            'No',
            'ID Karyawan',
            'Nama',
            'Departemen',
            'Jenis Cuti',
            'Durasi Cuti',
            'Rencana Mulai Cuti',
            'Rencana Selesai Cuti',
            'Aktual Mulai Cuti',
            'Aktual Selesai Cuti',
            'Alasan Cuti',
            'Karyawan Pengganti',
            'Checked 1',
            'Checked 2',
            'Approved',
            'Legalized',
            'Status Dokumen',
            'Status Cuti',
            'Created At',
        ];

        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
            $col++;
        }

        $row = 2;

        $columns = range('A', 'S');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth(35);
        }
        $sheet->setAutoFilter('A1:S1');

        foreach ($cutie as $c) {
            $sheet->setCellValue('A' . $row, $row - 1);
            $sheet->setCellValue('B' . $row, $c->karyawan->id_karyawan);
            $sheet->setCellValue('C' . $row, $c->karyawan->nama);
            $sheet->setCellValue('D' . $row, $c->karyawan->posisi[0]->departemen->nama);
            $sheet->setCellValue('E' . $row, $c->jenis_cuti == 'KHUSUS' ? $c->jenisCuti->nama : $c->jenis_cuti);
            $sheet->setCellValue('F' . $row, $c->durasi_cuti);
            $sheet->setCellValue('G' . $row, $c->rencana_mulai_cuti);
            $sheet->setCellValue('H' . $row, $c->rencana_selesai_cuti);
            $sheet->setCellValue('I' . $row, $c->aktual_mulai_cuti);
            $sheet->setCellValue('J' . $row, $c->aktual_selesai_cuti);
            $sheet->setCellValue('K' . $row, $c->alasan_cuti);
            $sheet->setCellValue('L' . $row, $c->karyawan_pengganti_id ? $c->karyawanPengganti->nama : '-');
            $sheet->setCellValue('M' . $row, $c->checked1_by !== null ? $c->checked1_by.' / '.(Carbon::parse($c->checked1_at)->format('d-m-Y')) : '-');
            $sheet->setCellValue('N' . $row, $c->checked2_by !== null ? $c->checked2_by.' / '.(Carbon::parse($c->checked2_at)->format('d-m-Y')) : '-');
            $sheet->setCellValue('O' . $row, $c->approved_by !== null ? $c->approved_by.' / '.(Carbon::parse($c->approved_at)->format('d-m-Y')) : '-');
            $sheet->setCellValue('P' . $row, $c->legalized_by !== null ? $c->legalized_by.' / '.(Carbon::parse($c->legalized_at)->format('d-m-Y')) : '-');
            $sheet->setCellValue('Q' . $row, $c->status_dokumen);
            $sheet->setCellValue('R' . $row, $c->status_cuti);
            $sheet->setCellValue('S' . $row, $c->created_at->format('d-m-Y'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=data-cuti-export.xlsx');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }
}
