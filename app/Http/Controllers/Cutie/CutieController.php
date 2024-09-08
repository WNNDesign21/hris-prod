<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Posisi;
use App\Models\Karyawan;
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

    public function personalia_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Personalia",
            'page' => 'cutie-personalia-cuti',
        ];
        return view('pages.cuti-e.personalia-cuti', $dataPage);
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
                        $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.$data->legalized_at.'</small>';
                    }
                } else {
                    $rejected = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.$data->rejected_at.'</small>';
                }
                    

                $nestedData['no'] = $data->id_cuti;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? $data->nama_pengganti : '-';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $data->status_cuti == 'SCHEDULED' ? '<span class="badge badge-pill badge-warning">'.$data->status_cuti.'</span>' : ($data->status_cuti == 'ON LEAVE' ? '<span class="badge badge-pill badge-secondary">'.$data->status_cuti.'</span>' : '-');
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    ($data->checked1_by == null && $data->checked2_by == null && $data->approved_by == null && $data->legalized_by == null && $data->rejected_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_cuti.'"><i class="fas fa-edit"></i> Edit</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '-').'
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
        if(auth()->user()->hasRole('user')){
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
                            $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                        }
    
                        if($data->approved_by == null){
                            if($my_jabatan == 4 || $my_jabatan == 3){
                                if($data->checked2_by !== null){
                                    $approved = '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnUpdateDokumen" data-id="'.$data->id_cuti.'"  data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="approved"><i class="far fa-check-circle"></i> Checked</button>';
                                    $reject3 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                    $btn_group_3 = '<div class="btn-group btn-sm">'.$approved.$reject3.'</div>';
                                } else {
                                    $btn_group_3 = 'Need Checked 1 & 2';
                                }
                            } else {
                                $btn_group_3 = 'Need Checked 1 & 2';
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
                            $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.$data->legalized_at.'</small>';
                        }

                    // JUMLAH PARENT 4
                    } elseif ($jumlah_parent >= 4){
                        if($data->checked1_by == null){
                            if($my_jabatan == 4 || $my_jabatan == 3){
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
                            if($my_jabatan == 4 || $my_jabatan == 3){
                                if($data->checked1_by !== null){
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
                            $btn_group_2 = '✅<br><small class="text-bold">'.$data->checked2_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked2_at)->diffForHumans().'</small>';
                        }
    
                        if($data->approved_by == null){
                            if($my_jabatan == 3 || $my_jabatan == 2){
                                if($data->checked2_by !== null){
                                    $approved = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'" data-issued-name="'.auth()->user()->karyawan->nama.'" data-type="approved"><i class="far fa-check-circle"></i> Checked</button>';
                                    $reject3 = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="'.auth()->user()->karyawan->nama.'"><i class="far fa-times-circle"></i> Reject</button>';
                                    $btn_group_3 = '<div class="btn-group btn-sm">'.$approved.$reject3.'</div>';
                                } else {
                                    $btn_group_3 = 'Need Checked 1 & 2';
                                }
                            } else {
                                $btn_group_3 = 'Need Checked 1 & 2';
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
                            $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.$data->legalized_at.'</small>';
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
                            $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.$data->legalized_at.'</small>';
                        }
                    } 
                } else {
                    $rejected = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.$data->rejected_at.'</small>';
                }

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? $data->nama_pengganti : '-';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
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
                            $legalized = '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnUpdateDokumen" data-id="'.$data->id_cuti.'" data-issued-name="HRD & GA" data-type="legalized"><i class="fas fa-thumbs-up"></i> Legalized</button>';
                            $reject = '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnReject" data-id="'.$data->id_cuti.'" data-nama-atasan="HRD & GA"><i class="far fa-times-circle"></i> Reject</button>';
                            $legalized = '<div class="btn-group btn-sm">'.$legalized.$reject.'</div>';
                        } else {
                            $legalized = 'Waiting Checked/Approved';
                        }
                    } else {
                        $legalized = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.$data->legalized_at.'</small>';
                    }
                } else {
                    $rejected = '❌<br><small class="text-bold">'.$data->rejected_by.'</small><br><small class="text-fade">'.$data->rejected_at.'</small>';
                }
                    

                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['rencana_mulai_cuti'] = Carbon::parse($data->rencana_mulai_cuti)->format('d M Y');
                $nestedData['rencana_selesai_cuti'] = Carbon::parse($data->rencana_selesai_cuti)->format('d M Y');
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti ? Carbon::parse($data->aktual_selesai_cuti)->format('d M Y') : '-';
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' <small class="text-fade">('.$data->jenis_cuti_khusus.')</small>';
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti ? $data->nama_pengganti : '-';
                $nestedData['checked_1'] = $rejected == null ? $btn_group_1 : $rejected;
                $nestedData['checked_2'] = $rejected == null ? $btn_group_2 : $rejected;
                $nestedData['approved'] = $rejected == null ? $btn_group_3 : $rejected;
                $nestedData['legalized'] = $rejected == null ? $legalized : $rejected;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $data->status_cuti == 'SCHEDULED' ? '<span class="badge badge-pill badge-warning">'.$data->status_cuti.'</span>' : ($data->status_cuti == 'ON LEAVE' ? '<span class="badge badge-pill badge-secondary">'.$data->status_cuti.'</span>' : '-');
                // $nestedData['created_at'] = Carbon::parse($data->created_at)->diffForHumans() ;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y H:i:s');
                $nestedData['attachment'] = $data->jenis_cuti !== 'SAKIT' ? 'No Attachment Needed' : '<a href="'.asset('storage/'.$data->attachment).'" target="_blank">Lihat</a>';
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm"><button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_cuti.'"><i class="fas fa-edit"></i> Edit</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_cuti.'"><i class="fas fa-trash-alt"></i> Hapus </button></div>';

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
                $cuti->legalized_at = now();
            }
            
            $cuti->save();
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
