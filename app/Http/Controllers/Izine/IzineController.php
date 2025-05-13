<?php

namespace App\Http\Controllers\Izine;

use Throwable;
use Carbon\Carbon;
use App\Models\Izine;
use App\Models\Piket;
use App\Models\Posisi;
use App\Models\Sakite;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\Departemen;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\SummarizeAttendanceJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Attendance\ScanlogDetail;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Jobs\GenerateSummarizeAttendanceJob;
use App\Models\Attendance\AttendanceSummary;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        if(auth()->user()->hasRole('personalia')){
            return redirect()->route('izine.approval-izin');
        } elseif (auth()->user()->hasRole('security')){
            return redirect()->route('izine.log-book-izin');
        }

        $dataPage = [
            'pageTitle' => "Izin-E - Pengajuan Izin",
            'page' => 'izine-pengajuan-izin',
        ];
        return view('pages.izin-e.pengajuan-izin', $dataPage);
    }

    public function log_book_izin_view()
    {
        $dataPage = [
            'pageTitle' => "Izin-E - Log Book Izin",
            'page' => 'izine-log-book-izin',
        ];
        return view('pages.izin-e.log-book-izin', $dataPage);
    }

    public function approval_izin_view()
    {
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
            'pageTitle' => "Izin-E - Approval Izin",
            'page' => 'izine-approval-izin',
            'departemens' => $departemens
        ];
        return view('pages.izin-e.approval-izin', $dataPage);
    }

    public function export_view()
    {
        $departments = Departemen::all();
        $dataPage = [
            'pageTitle' => "Izin-E - Export Izin & SKD",
            'page' => 'izine-export',
            'departments' => $departments
        ];
        return view('pages.izin-e.export-izin', $dataPage);
    }

    public function piket_view()
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $karyawans = Karyawan::selectRaw('karyawans.*')->organisasi($organisasi_id)->aktif()
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->where('jabatan_id', '<=', 5)
        ->get();

        $dataPage = [
            'pageTitle' => "Izin-E - Piket (Shift Malam)",
            'page' => 'izine-piket',
            'karyawans' => $karyawans
        ];
        return view('pages.izin-e.piket', $dataPage);
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
        $dataFilter['jenis_izin'] = ['TM', 'SH', 'KP', 'PL'];

        $totalData = Izine::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $izine = Izine::getData($dataFilter, $settings);
        $totalFiltered = Izine::countData($dataFilter);
        $dataTable = [];


        if (!empty($izine)) {
            foreach ($izine as $data) {

                $checked_by = '🕛Waiting';
                $approved_by = '🕛Waiting';
                $legalized_by = '🕛Waiting';
                if($data->checked_by){
                    $checked_by = '✅<br><small class="text-bold">'.$data->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                }

                if ($data->jenis_izin == 'TM') {
                    $jenis_izin = '<span class="badge badge-primary">Tidak Masuk</span>';
                    $durasi = $data->durasi . ' Hari';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y') : '-';

                    $aksi = '-';
                    if($data->checked_by && $data->approved_by && $data->legalized_by && !$data->rejected_by && !$data->aktual_mulai_or_masuk && !$data->aktual_selesai_or_keluar){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-success btnDone" data-id-izin="'.$data->id_izin.'"><i class="fas fa-check"></i> Done</button><button class="btn btn-sm btn-danger btnCancel" data-id-izin="'.$data->id_izin.'"><i class="fas fa-history"></i> Cancel</button></div>';
                    }

                    if((!$data->checked_by || !$data->approved_by || !$data->legalized_by) && !$data->rejected_by){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-warning btnEdit" data-id-izin="'.$data->id_izin.'"><i class="fas fa-edit"></i> Edit</button><button class="btn btn-sm btn-danger btnDelete" data-id-izin="'.$data->id_izin.'"><i class="fas fa-trash"></i> Delete</button></div>';
                    }

                } elseif ($data->jenis_izin == 'SH') {
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
                        if(($data->rencana_mulai_or_masuk && !$data->aktual_mulai_or_masuk) || ($data->rencana_selesai_or_keluar && !$data->aktual_selesai_or_keluar)){
                            $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-primary btnShowQR" data-id-izin="'.$data->id_izin.'"><i class="fas fa-qrcode"></i>  Show QR</button><button class="btn btn-sm btn-danger btnCancel" data-id-izin="'.$data->id_izin.'"><i class="fas fa-history"></i> Cancel</button></div>';
                        }
                    }
                } elseif ($data->jenis_izin == 'KP') {
                    $jenis_izin = '<span class="badge badge-light">Keluar Pabrik</span>';
                    $durasi =  '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i') : '-';

                    $aksi = '-';
                    if($data->checked_by && $data->approved_by && $data->legalized_by && !$data->rejected_by && !$data->aktual_mulai_or_masuk){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-primary btnShowQR" data-id-izin="'.$data->id_izin.'"><i class="fas fa-qrcode"></i>  Show QR</button><button class="btn btn-sm btn-danger btnCancel" data-id-izin="'.$data->id_izin.'"><i class="fas fa-history"></i> Cancel</button></div>';
                    }

                    if((!$data->checked_by || !$data->approved_by || !$data->legalized_by) && !$data->rejected_by){
                        $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-warning btnEdit" data-id-izin="'.$data->id_izin.'"><i class="fas fa-edit"></i> Edit</button><button class="btn btn-sm btn-danger btnDelete" data-id-izin="'.$data->id_izin.'"><i class="fas fa-trash"></i> Delete</button></div>';
                    }
                } elseif ($data->jenis_izin == 'PL') {
                    $jenis_izin = '<span class="badge badge-dark">Pulang</span>';
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
                        if($data->rencana_selesai_or_keluar && !$data->aktual_selesai_or_keluar){
                            $aksi = '<div class="btn-group btn-group-sm"><button class="btn btn-sm btn-primary btnShowQR" data-id-izin="'.$data->id_izin.'"><i class="fas fa-qrcode"></i>  Show QR</button><button class="btn btn-sm btn-danger btnCancel" data-id-izin="'.$data->id_izin.'"><i class="fas fa-history"></i> Cancel</button></div>';
                        }
                    }
                };

                //REJECTED
                if ($data->rejected_by){
                    $checked_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    $approved_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    $legalized_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['rencana_mulai_or_masuk'] = $rencana_mulai_or_masuk;
                $nestedData['rencana_selesai_or_keluar'] = $rencana_selesai_or_keluar;
                $nestedData['aktual_mulai_or_masuk'] = $aktual_mulai_or_masuk;
                $nestedData['aktual_selesai_or_keluar'] = $aktual_selesai_or_keluar;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['durasi'] = $durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $checked_by;
                $nestedData['approved_by'] = $approved_by;
                $nestedData['legalized_by'] = $legalized_by;
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

    public function approval_izin_datatable(Request $request)
    {

        $columns = array(
            0 => 'izins.id_izin',
            1 => 'karyawans.nama',
            2 => 'departemens.nama',
            3 => 'posisis.nama',
            4 => 'izins.rencana_mulai_or_masuk',
            5 => 'izins.rencana_mulai_or_masuk',
            6 => 'izins.aktual_selesai_or_keluar',
            7 => 'izins.aktual_selesai_or_keluar',
            8 => 'izins.jenis_izin',
            9 => 'izins.durasi',
            10 => 'izins.keterangan',
            11 => 'izins.checked_by',
            12 => 'izins.approved_by',
            13 => 'izins.legalized_by',
        );

        $totalData = Izine::count();
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

        $is_can_checked = false;
        $is_can_approved = false;
        $is_can_legalized = false;
        $is_shift_malam = 'N';
        $organisasi_id = auth()->user()->organisasi_id;

        // FILTER PERSONALIA
        if(auth()->user()->hasRole('personalia')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_legalized = true;
        }

        //FILTER MEMBER
        if (auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id >= 4){
                $is_can_checked = true;
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 4){
                $is_can_approved = true;
            }



             //CEK APAKAH DIA ORANG PIKET
            $today = Carbon::now()->format('Y-m-d');
            $piket = auth()->user()->karyawan->piket();
            if (!(Carbon::now()->format('H') >= 21 || Carbon::now()->format('H') <= 6)) {
                if ($piket) {
                    $is_can_legalized = true;
                    $is_shift_malam = 'Y';
                    $dataFilter['is_shift_malam'] = true;
                    $dataFilter['organisasi_id'] = $organisasi_id;
                } else {
                    $dataFilter['member_posisi_id'] = $id_posisi_members;
                }
            } else {
                $dataFilter['member_posisi_id'] = $id_posisi_members;
            }
        }

        // FILTER CUSTOM
        $filterUrutan = $request->urutan;
        if(isset($filterUrutan)){
            $dataFilter['urutan'] = $filterUrutan;
        }

        $filterDepartemen = $request->departemen;
        if(isset($filterDepartemen)){
            $dataFilter['departemen'] = $filterDepartemen;
        }

        $filterStatus = $request->status;
        if(isset($filterStatus)){
            $dataFilter['status'] = $filterStatus;
        }

        $izine = Izine::getData($dataFilter, $settings);
        $totalFiltered = Izine::countData($dataFilter);

        $dataTable = [];

        if (!empty($izine)) {
            foreach ($izine as $data) {
                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $has_leader = Approval::HasLeader($posisi);
                $has_section_head = Approval::HasSectionHead($posisi);
                $has_department_head = Approval::HasDepartmentHead($posisi);
                $legalized_by = 'Need Legalized';
                $approved_by = 'Need Approved';
                $checked_by = 'Need Checked';
                $karyawan_pengganti = '-';

                //JENIS IZIN
                if ($data->jenis_izin == 'TM') {
                    $jenis_izin = '<span class="badge badge-primary">Tidak Masuk</span>';
                    $durasi = $data->durasi . ' Hari';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y') : '-';
                } elseif ($data->jenis_izin == 'SH') {
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                } elseif ($data->jenis_izin == 'KP') {
                    $jenis_izin = '<span class="badge badge-light">Keluar Pabrik</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                } elseif ($data->jenis_izin == 'PL') {
                    $jenis_izin = '<span class="badge badge-dark">Pulang</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                }

                if($data->checked_by){
                    $checked_by = '✅<br><small class="text-bold">'.$data->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                }


                //TOMBOL CHECKED
                if ($is_can_checked){
                    $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;

                    if($has_leader && $my_posisi == 5){
                        if(!$data->checked_by){
                            $checked_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && $has_section_head && $has_department_head && $my_posisi == 4){
                        if(!$data->checked_by){
                            $checked_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && !$has_section_head && $has_department_head || !$has_leader && !$has_section_head && !$has_department_head || !$has_leader && $has_section_head && !$has_department_head){
                        if(!$data->checked_by){
                            $checked_by = 'Directly Approved';
                        }
                    }
                }

                //TOMBOL APPROVED
                if ($is_can_approved){
                    $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;

                    //KONDISI UNTUK SECTION HEAD
                    if($has_leader && $my_posisi == 4){
                        if($data->checked_by && !$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && $my_posisi == 4){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    //KONDISI UNTUK DEPT HEAD
                    if($has_leader && !$has_section_head && $my_posisi == 3){
                        if($data->checked_by && !$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && !$has_section_head && $my_posisi == 3){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    //KONDISI UNTUK DIV / PLANT HEAD
                    if(!$has_leader && !$has_section_head && !$has_department_head){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if($has_leader && !$has_section_head && !$has_department_head){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }
                }

                //TOMBOL LEGALIZED
                if ($is_can_legalized){
                    if(!$data->legalized_by){
                        $legalized_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="fas fa-thumbs-up"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                    }
                }

                //REJECTED
                if ($data->rejected_by){
                    $checked_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    $approved_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    $legalized_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['nama'] = $data->nama;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['rencana_mulai_or_masuk'] = $rencana_mulai_or_masuk;
                $nestedData['rencana_selesai_or_keluar'] = $rencana_selesai_or_keluar;
                $nestedData['aktual_mulai_or_masuk'] = $aktual_mulai_or_masuk;
                $nestedData['aktual_selesai_or_keluar'] = $aktual_selesai_or_keluar;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['durasi'] = $durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $checked_by;
                $nestedData['approved_by'] = $approved_by;
                $nestedData['legalized_by'] = $legalized_by;

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

    public function alldata_datatable(Request $request)
    {

        $columns = array(
            0 => 'izins.id_izin',
            1 => 'karyawans.nama',
            2 => 'departemens.nama',
            3 => 'posisis.nama',
            4 => 'izins.rencana_mulai_or_masuk',
            5 => 'izins.rencana_mulai_or_masuk',
            6 => 'izins.aktual_selesai_or_keluar',
            7 => 'izins.aktual_selesai_or_keluar',
            8 => 'izins.jenis_izin',
            9 => 'izins.durasi',
            10 => 'izins.keterangan',
            11 => 'izins.checked_by',
            12 => 'izins.approved_by',
            13 => 'izins.legalized_by',
        );

        $totalData = Izine::count();
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

        $is_can_checked = false;
        $is_can_approved = false;
        $is_can_legalized = false;
        $is_shift_malam = 'N';
        $organisasi_id = auth()->user()->organisasi_id;

        // FILTER PERSONALIA
        if(auth()->user()->hasRole('personalia')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_legalized = true;
        }

        //FILTER MEMBER
        if (auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

             //CEK APAKAH DIA ORANG PIKET
            $today = Carbon::now()->format('Y-m-d');
            $piket = auth()->user()->karyawan->piket();
            if (!(Carbon::now()->format('H') >= 21 || Carbon::now()->format('H') <= 6)) {
                if ($piket) {
                    $is_shift_malam = 'Y';
                    $dataFilter['is_shift_malam'] = true;
                    $dataFilter['organisasi_id'] = $organisasi_id;
                } else {
                    $dataFilter['member_posisi_id'] = $id_posisi_members;
                }
            } else {
                $dataFilter['member_posisi_id'] = $id_posisi_members;
            }
        }

        $filterUrutan = $request->urutan;
        if(isset($filterUrutan)){
            $dataFilter['urutan'] = $filterUrutan;
        }

        $filterDepartemen = $request->departemen;
        if(isset($filterDepartemen)){
            $dataFilter['departemen'] = $filterDepartemen;
        }

        $filterStatus = $request->status;
        if(isset($filterStatus)){
            $dataFilter['status'] = $filterStatus;
        }

        $izine = Izine::getAllData($dataFilter, $settings);
        $totalFiltered = Izine::countAllData($dataFilter);

        $dataTable = [];

        if (!empty($izine)) {
            foreach ($izine as $data) {
                $legalized_by = 'Need Legalized';
                $approved_by = 'Need Approved';
                $checked_by = 'Need Checked';
                $karyawan_pengganti = '-';

                //JENIS IZIN
                if ($data->jenis_izin == 'TM') {
                    $jenis_izin = '<span class="badge badge-primary">Tidak Masuk</span>';
                    $durasi = $data->durasi . ' Hari';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y') : '-';
                } elseif ($data->jenis_izin == 'SH') {
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                } elseif ($data->jenis_izin == 'KP') {
                    $jenis_izin = '<span class="badge badge-light">Keluar Pabrik</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                } elseif ($data->jenis_izin == 'PL') {
                    $jenis_izin = '<span class="badge badge-dark">Pulang</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                }

                if($data->checked_by){
                    $checked_by = '✅<br><small class="text-bold">'.$data->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $checked_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $approved_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $legalized_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['nama'] = $data->nama;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['rencana_mulai_or_masuk'] = $rencana_mulai_or_masuk;
                $nestedData['rencana_selesai_or_keluar'] = $rencana_selesai_or_keluar;
                $nestedData['aktual_mulai_or_masuk'] = $aktual_mulai_or_masuk;
                $nestedData['aktual_selesai_or_keluar'] = $aktual_selesai_or_keluar;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['durasi'] = $durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $checked_by;
                $nestedData['approved_by'] = $approved_by;
                $nestedData['legalized_by'] = $legalized_by;

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

    public function must_approved_datatable(Request $request)
    {
        $columns = array(
            0 => 'izins.id_izin',
            1 => 'karyawans.nama',
            2 => 'departemens.nama',
            3 => 'posisis.nama',
            4 => 'izins.rencana_mulai_or_masuk',
            5 => 'izins.rencana_mulai_or_masuk',
            6 => 'izins.aktual_selesai_or_keluar',
            7 => 'izins.aktual_selesai_or_keluar',
            8 => 'izins.jenis_izin',
            9 => 'izins.durasi',
            10 => 'izins.keterangan',
            11 => 'izins.checked_by',
            12 => 'izins.approved_by',
            13 => 'izins.legalized_by',
        );

        $totalData = Izine::count();
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

        $is_can_checked = false;
        $is_can_approved = false;
        $is_can_legalized = false;
        $is_shift_malam = 'N';
        $organisasi_id = auth()->user()->organisasi_id;

        // FILTER PERSONALIA
        if(auth()->user()->hasRole('personalia')){
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_legalized = true;
        }

        //FILTER MEMBER
        if (auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id >= 4){
                $is_can_checked = true;
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 4){
                $is_can_approved = true;
            }

             //CEK APAKAH DIA ORANG PIKET
            $today = Carbon::now()->format('Y-m-d');
            $piket = auth()->user()->karyawan->piket();
            if (!(Carbon::now()->format('H') >= 21 || Carbon::now()->format('H') <= 6)) {
                if ($piket) {
                    $is_can_legalized = true;
                    $is_shift_malam = 'Y';
                    $dataFilter['is_shift_malam'] = true;
                    $dataFilter['organisasi_id'] = $organisasi_id;
                } else {
                    $dataFilter['member_posisi_id'] = $id_posisi_members;
                }
            } else {
                $dataFilter['member_posisi_id'] = $id_posisi_members;
            }
        }

        // FILTER CUSTOM
        $filterUrutan = $request->urutan;
        if(isset($filterUrutan)){
            $dataFilter['urutan'] = $filterUrutan;
        }

        $filterDepartemen = $request->departemen;
        if(isset($filterDepartemen)){
            $dataFilter['departemen'] = $filterDepartemen;
        }

        $filterStatus = $request->status;
        if(isset($filterStatus)){
            $dataFilter['status'] = $filterStatus;
        }

        $izine = Izine::getMustApprovedData($dataFilter, $settings);
        $totalFiltered = Izine::countMustApprovedData($dataFilter);

        $dataTable = [];

        if (!empty($izine)) {
            foreach ($izine as $data) {
                $karyawan = Karyawan::find($data->karyawan_id);
                $posisi = $karyawan->posisi;
                $has_leader = Approval::HasLeader($posisi);
                $has_section_head = Approval::HasSectionHead($posisi);
                $has_department_head = Approval::HasDepartmentHead($posisi);
                $legalized_by = 'Need Legalized';
                $approved_by = 'Need Approved';
                $checked_by = 'Need Checked';
                $karyawan_pengganti = '-';

                //JENIS IZIN
                if ($data->jenis_izin == 'TM') {
                    $jenis_izin = '<span class="badge badge-primary">Tidak Masuk</span>';
                    $durasi = $data->durasi . ' Hari';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y') : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y') : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y') : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y') : '-';
                } elseif ($data->jenis_izin == 'SH') {
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                } elseif ($data->jenis_izin == 'KP') {
                    $jenis_izin = '<span class="badge badge-light">Keluar Pabrik</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                } elseif ($data->jenis_izin == 'PL') {
                    $jenis_izin = '<span class="badge badge-dark">Pulang</span>';
                    $durasi = '-';
                    $rencana_mulai_or_masuk = $data->rencana_mulai_or_masuk ? Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $rencana_selesai_or_keluar = $data->rencana_selesai_or_keluar ? Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_mulai_or_masuk = $data->aktual_mulai_or_masuk ? Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB' : '-';
                    $aktual_selesai_or_keluar = $data->aktual_selesai_or_keluar ? Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB' : '-';
                }

                if($data->checked_by){
                    $checked_by = '✅<br><small class="text-bold">'.$data->checked_by.'</small><br><small class="text-fade">'.Carbon::parse($data->checked_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $checked_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                if($data->approved_by){
                    $approved_by = '✅<br><small class="text-bold">'.$data->approved_by.'</small><br><small class="text-fade">'.Carbon::parse($data->approved_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $approved_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                if($data->legalized_by){
                    $legalized_by = '✅<br><small class="text-bold">'.$data->legalized_by.'</small><br><small class="text-fade">'.Carbon::parse($data->legalized_at)->diffForHumans().'</small>';
                } elseif ($data->rejected_by) {
                    $legalized_by = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ '.$data->rejected_by.'<br>'.Carbon::parse($data->rejected_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                }

                //TOMBOL CHECKED
                if ($is_can_checked){
                    $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;

                    if($has_leader && $my_posisi == 5){
                        if(!$data->checked_by){
                            $checked_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && $has_section_head && $has_department_head && $my_posisi == 4){
                        if(!$data->checked_by){
                            $checked_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && !$has_section_head && $has_department_head || !$has_leader && !$has_section_head && !$has_department_head || !$has_leader && $has_section_head && !$has_department_head){
                        if(!$data->checked_by){
                            $checked_by = 'Directly Approved';
                        }
                    }
                }

                //TOMBOL APPROVED
                if ($is_can_approved){
                    $my_posisi = auth()->user()->karyawan->posisi[0]->jabatan_id;

                    //KONDISI UNTUK SECTION HEAD
                    if($has_leader && $my_posisi == 4){
                        if($data->checked_by && !$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && $my_posisi == 4){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    //KONDISI UNTUK DEPT HEAD
                    if($has_leader && !$has_section_head && $my_posisi == 3){
                        if($data->checked_by && !$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if(!$has_leader && !$has_section_head && $my_posisi == 3){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    //KONDISI UNTUK DIV / PLANT HEAD
                    if(!$has_leader && !$has_section_head && !$has_department_head){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }

                    if($has_leader && !$has_section_head && !$has_department_head){
                        if(!$data->approved_by){
                            $approved_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-izin="'.$data->id_izin.'"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    }
                }

                //TOMBOL LEGALIZED
                if ($is_can_legalized){
                    if(!$data->legalized_by){
                        $legalized_by = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="fas fa-thumbs-up"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id-izin="'.$data->id_izin.'" data-is-shift-malam="'.$is_shift_malam.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                    }
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['nama'] = $data->nama;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['rencana_mulai_or_masuk'] = $rencana_mulai_or_masuk;
                $nestedData['rencana_selesai_or_keluar'] = $rencana_selesai_or_keluar;
                $nestedData['aktual_mulai_or_masuk'] = $aktual_mulai_or_masuk;
                $nestedData['aktual_selesai_or_keluar'] = $aktual_selesai_or_keluar;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['durasi'] = $durasi;
                $nestedData['keterangan'] = $data->keterangan;
                $nestedData['checked_by'] = $checked_by;
                $nestedData['approved_by'] = $approved_by;
                $nestedData['legalized_by'] = $legalized_by;

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


    public function log_book_izin_datatable(Request $request)
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

        if (auth()->user()->hasRole('security')){
            $dataFilter['is_security'] = 'Y';
            $dataFilter['jenis_izin'] = ['SH', 'KP', 'PL'];
            $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
        }

        $totalData = Izine::count();
        $totalFiltered = $totalData;
        $izine = Izine::getData($dataFilter, $settings);
        $totalFiltered = Izine::countData($dataFilter);
        $dataTable = [];


        if (!empty($izine)) {
            $rencana = '-';
            $aktual = '-';

            foreach ($izine as $data) {
                if($data->jenis_izin == 'SH'){
                    $jenis_izin = '<span class="badge badge-info">1/2 Hari</span>';
                    if ($data->rencana_mulai_or_masuk){
                        $rencana = Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    } elseif ($data->rencana_selesai_or_keluar){
                        $rencana = Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    }

                    if ($data->aktual_mulai_or_masuk){
                        $aktual = Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    } elseif ($data->aktual_selesai_or_keluar){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    }
                } elseif ($data->jenis_izin == 'KP') {
                    $jenis_izin = '<span class="badge badge-light">Keluar Pabrik</span>';
                    if ($data->rencana_selesai_or_keluar && $data->rencana_mulai_or_masuk){
                        $rencana = Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB - '.Carbon::parse($data->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    }

                    if ($data->aktual_selesai_or_keluar){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB - UNKNOWN';
                    }

                    if ($data->aktual_mulai_or_masuk){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB - '.Carbon::parse($data->aktual_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                    }
                } elseif ($data->jenis_izin == 'PL') {
                    $jenis_izin = '<span class="badge badge-dark">Pulang</span>';
                    if ($data->rencana_selesai_or_keluar){
                        $rencana = Carbon::parse($data->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    }

                    if ($data->aktual_selesai_or_keluar){
                        $aktual = Carbon::parse($data->aktual_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                    }
                }

                $nestedData['id_izin'] = $data->id_izin;
                $nestedData['nama'] = $data->nama;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['posisi'] = $data->posisi;
                $nestedData['rencana'] = $rencana;
                $nestedData['aktual'] = $aktual;
                $nestedData['jenis_izin'] = $jenis_izin;
                $nestedData['keterangan'] = $data->keterangan;

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

    public function piket_datatable(Request $request)
    {

        $columns = array(
            0 => 'karyawans.nama',
            1 => 'departemens.nama',
            2 => 'pikets.expired_date'
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

        $organisasi_id = auth()->user()->organisasi_id;
        $dataFilter['organisasi_id'] = $organisasi_id;

        $filterPeriode = $request->periode;
        if (isset($filterPeriode)) {
            $year = Carbon::parse($filterPeriode)->format('Y');
            $month = Carbon::parse($filterPeriode)->format('m');
            $dataFilter['year'] = $year;
            $dataFilter['month'] = $month;
        }

        $totalData = Piket::count();
        $totalFiltered = $totalData;
        $pikets = Piket::getData($dataFilter, $settings);
        $totalFiltered = Piket::countData($dataFilter);
        $dataTable = [];


        if (!empty($pikets)) {
            foreach ($pikets as $data) {
                $nestedData['karyawan'] = $data->karyawan;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['expired_date'] = Carbon::parse($data->expired_date)->format('d M Y');
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id-piket="'.$data->id_piket.'" data-id-karyawan="'.$data->karyawan_id.'" data-expired-date="'.$data->expired_date.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id-piket="'.$data->id_piket.'"><i class="fas fa-trash"></i></button>
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
        } elseif ($jenis_izin == 'SH') {
            $dataValidate = [
                'jenis_izin' => ['required'],
                'keterangan' => ['required'],
                'masuk_or_keluar' => ['required', 'in:M,K'],
                'rencana_masuk_or_keluar' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            ];
        } elseif ($jenis_izin == 'KP') {
            $dataValidate = [
                'jenis_izin' => ['required'],
                'keterangan' => ['required'],
                'rencana_mulai_or_masuk' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:rencana_selesai_or_keluar'],
                'rencana_selesai_or_keluar' => ['required', 'date_format:Y-m-d\TH:i', 'before_or_equal:rencana_mulai_or_masuk', 'after_or_equal:today'],
            ];
        } elseif ($jenis_izin == 'PL') {
            $dataValidate = [
                'jenis_izin' => ['required'],
                'keterangan' => ['required'],
                'rencana_selesai_or_keluar' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            ];
        } else {
            return response()->json(['message' => 'Jenis izin tidak ditemukan'], 404);
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
            } elseif ($jenis_izin == 'SH') {
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
            } elseif ($jenis_izin == 'KP') {
                $izin = Izine::create([
                    'id_izin' => 'IZIN-'.$jenis_izin.'-'. Str::random(4).'-'. date('YmdHis'),
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $departemen_id,
                    'divisi_id' => $divisi_id,
                    'jenis_izin' => $jenis_izin,
                    'rencana_mulai_or_masuk' => $rencana_mulai_or_masuk,
                    'rencana_selesai_or_keluar' => $rencana_selesai_or_keluar,
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
                    'rencana_selesai_or_keluar' => $rencana_selesai_or_keluar,
                    'keterangan' => $keterangan
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Izin berhasil diajukan'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function piket_store(Request $request)
    {
        $dataValidate = [
            'karyawan_id.*' => ['required', 'exists:karyawans,id_karyawan'],
            'expired_date' => ['required', 'date_format:Y-m-d'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $karyawans = $request->karyawan_id;
        $expired_date = $request->expired_date;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try {
            $data_piket = [];
            foreach ($karyawans as $karyawan_id) {
                $piket = Piket::where('karyawan_id', $karyawan_id)->where('expired_date', '>=', $expired_date)->exists();
                $karyawan = Karyawan::find($karyawan_id);
                if($piket) {
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama.' sudah memiliki jadwal piket dengan expired date yang lebih lama!'], 401);
                }

                $data_piket[] = [
                    'karyawan_id' => $karyawan_id,
                    'departemen_id' => $karyawan->posisi[0]->departemen_id,
                    'expired_date' => $expired_date,
                    'organisasi_id' => $organisasi_id
                ];
            }

            Piket::insert($data_piket);
            DB::commit();
            return response()->json(['message' => 'Piket shift malam karyawan berhasil ditambahkan!'], 200);
        } catch (Throwable $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function piket_update(Request $request, string $id_piket)
    {
        $dataValidate = [
            'karyawan_idEdit' => ['required', 'exists:karyawans,id_karyawan'],
            'expired_dateEdit' => ['required', 'date_format:Y-m-d'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $karyawan = $request->karyawan_idEdit;
        $expired_date = $request->expired_dateEdit;
        $organisasi_id = auth()->user()->organisasi_id;

        DB::beginTransaction();
        try {
            $piket_exists = Piket::where('karyawan_id', $karyawan)->where('expired_date', '>=', $expired_date)->exists();
            $kry = Karyawan::find($karyawan);

            if($piket_exists) {
                DB::rollback();
                return response()->json(['message' => $kry->nama.' sudah memiliki jadwal piket dengan expired date yang lebih lama!'], 401);
            }

            $piket = Piket::find($id_piket);
            $piket->karyawan_id = $karyawan;
            $piket->expired_date = $expired_date;
            $piket->save();
            DB::commit();
            return response()->json(['message' => 'Piket shift malam karyawan berhasil diupdate!'], 200);
        } catch (Throwable $e){
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
        } elseif ($izin->jenis_izin == 'SH') {
            $dataValidate = [
                'keteranganEdit' => ['required'],
                'masuk_or_keluarEdit' => ['required', 'in:M,K'],
                'rencana_masuk_or_keluarEdit' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            ];
        } elseif ($izin->jenis_izin == 'KP') {
            $dataValidate = [
                'keteranganEdit' => ['required'],
                'rencana_mulai_or_masukEdit' => ['required', 'date_format:Y-m-d', 'after_or_equal:rencana_selesai_or_keluarEdit'],
                'rencana_selesai_or_keluarEdit' => ['required', 'date_format:Y-m-d', 'before_or_equal:rencana_mulai_or_masukEdit', 'after_or_equal:today'],
            ];
        } elseif ($izin->jenis_izin == 'PL') {
            $dataValidate = [
                'keteranganEdit' => ['required'],
                'rencana_selesai_or_keluarEdit' => ['required', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
            ];
        } else {
            return response()->json(['message' => 'Jenis izin tidak ditemukan'], 404);
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
            } elseif ($izin->jenis_izin == 'SH') {
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
            } elseif ($izin->jenis_izin == 'KP') {
                $izin->update([
                    'rencana_mulai_or_masuk' => $rencana_mulai_or_masuk,
                    'rencana_selesai_or_keluar' => $rencana_selesai_or_keluar,
                    'keterangan' => $keterangan
                ]);
            } elseif ($izin->jenis_izin == 'PL') {
                $izin->update([
                    'rencana_selesai_or_keluar' => $rencana_selesai_or_keluar,
                    'keterangan' => $keterangan
                ]);
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
            return response()->json(['message' => 'Berhasil membatalkan pengajuan izin'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function piket_delete(string $id_piket)
    {
        DB::beginTransaction();
        try{
            $piket = Piket::find($id_piket);

            if(!$piket){
                return response()->json(['message' => 'Data tidak ditemukan!'], 404);
            }

            $piket->delete();
            DB::commit();
            return response()->json(['message' => 'Berhasil menghapus karyawan piket!.'],200);
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
            'masuk_or_keluar' => $izine->jenis_izin !== 'SH' ? null : ($izine->rencana_mulai_or_masuk ? 'M' : 'K'),
        ];
        return response()->json(['data' => $data], 200);
    }

    public function get_qrcode_detail_izin(string $id_izin)
    {
        $izine = Izine::find($id_izin);
        try {
            $rencana = 'Unknown';
            $jenis_izin = '-';

            if(!$izine){
                return response()->json(['message' => 'Invalid QR Code!'], 403);
            }

            if($izine->jenis_izin == 'SH'){
                $jenis_izin = '1/2 Hari';
                if($izine->aktual_mulai_or_masuk || $izine->aktual_selesai_or_keluar){
                    return response()->json(['message' => 'QR Code Confirmed!'], 403);
                }

                $rencana = $izine->rencana_mulai_or_masuk ? Carbon::parse($izine->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB' : ($izine->rencana_selesai_or_keluar ? Carbon::parse($izine->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : 'Unknown');
            } elseif ($izine->jenis_izin == 'KP') {
                $jenis_izin = 'Keluar Pabrik';
                if($izine->aktual_mulai_or_masuk){
                    return response()->json(['message' => 'QR Code Confirmed!'], 403);
                }

                if($izine->aktual_selesai_or_keluar){
                    $rencana = Carbon::parse($izine->rencana_mulai_or_masuk)->format('d M Y, H:i').' WIB';
                } else {
                    $rencana = Carbon::parse($izine->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB';
                }

            } elseif ($izine->jenis_izin == 'PL') {
                $jenis_izin = 'Pulang';
                if($izine->aktual_selesai_or_keluar){
                    return response()->json(['message' => 'QR Code Confirmed!'], 403);
                }

                $rencana = $izine->rencana_selesai_or_keluar ? Carbon::parse($izine->rencana_selesai_or_keluar)->format('d M Y, H:i').' WIB' : 'Unknown';
            } else {
                return response()->json(['message' => 'Invalid QR Code!'], 403);
            }

            $data = [
                'id_izin' => $izine->id_izin,
                'nama' => $izine->karyawan->nama,
                'departemen' => $izine->departemen->nama,
                'jenis_izin' => $jenis_izin,
                'rencana' => $rencana,
                'keterangan' => $izine->keterangan,
            ];
            return response()->json(['message' => 'Data izin ditemukan!', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function checked(Request $request, string $id_izin)
    {
        $izin = Izine::find($id_izin);

        DB::beginTransaction();
        try{
            if ($izin->checked_by) {
                return response()->json(['message' => 'Pengajuan izin sudah di checked!'], 403);
            } elseif ($izin->rejected_by) {
                return response()->json(['message' => 'Pengajuan izin yang sudah di reject tidak dapat di Checked!'], 403);
            }

            $izin->checked_by = auth()->user()->karyawan->nama;
            $izin->checked_at = now();
            $izin->save();

            DB::commit();
            return response()->json(['message' => 'Izin berhasil di Checked!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approved(Request $request, string $id_izin)
    {
        $izin = Izine::find($id_izin);

        DB::beginTransaction();
        try{
            if ($izin->approved_by) {
                return response()->json(['message' => 'Pengajuan izin sudah di approved!'], 403);
            } elseif ($izin->rejected_by) {
                return response()->json(['message' => 'Pengajuan izin yang sudah di reject tidak dapat di Approved!'], 403);
            }

            if(!$izin->checked_by){
                $izin->checked_by = auth()->user()->karyawan->nama;
                $izin->checked_at = now();
            }

            $izin->approved_by = auth()->user()->karyawan->nama;
            $izin->approved_at = now();
            $izin->save();

            DB::commit();
            return response()->json(['message' => 'Izin berhasil di Approved!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function legalized(Request $request, string $id_izin)
    {
        $dataValidate = [
            'is_shift_malam' => ['required', 'in:Y,N'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $is_shift_malam = $request->is_shift_malam;

        DB::beginTransaction();
        try{
            $izin = Izine::find($id_izin);
            if ($izin->legalized_by) {
                DB::rollBack();
                return response()->json(['message' => 'Pengajuan izin sudah di legalized!'], 403);
            } elseif ($izin->rejected_by) {
                DB::rollBack();
                return response()->json(['message' => 'Pengajuan izin yang sudah di reject tidak dapat di Legalized!'], 403);
            }

            if ($is_shift_malam == 'Y' && $izin->jenis_izin == 'TM') {
                DB::rollBack();
                return response()->json(['message' => 'Pengajuan izin tidak masuk tidak dapat di legalized oleh karyawan!'], 403);
            }

            if(!$izin->checked_by){
                $izin->checked_by = $is_shift_malam == 'Y' ? auth()->user()->karyawan->nama : 'HRD & GA';
                $izin->checked_at = now();
            }

            if(!$izin->approved_by){
                $izin->approved_by = $is_shift_malam == 'Y' ? auth()->user()->karyawan->nama : 'HRD & GA';
                $izin->approved_at = now();
            }

            $izin->legalized_by = $is_shift_malam == 'Y' ? auth()->user()->karyawan->nama : 'HRD & GA';
            $izin->legalized_at = now();
            $izin->save();

            if ($izin->jenis_izin == 'TM'){
                $organisasi_id = $izin->organisasi_id;
                $dateArray = [];
                $startDate = Carbon::parse($izin->rencana_mulai_or_masuk);
                $endDate = Carbon::parse($izin->rencana_selesai_or_keluar);

                while ($startDate->lte($endDate)) {
                    $dateArray[] = $startDate->format('Y-m-d');
                    $startDate->addDay();
                }

                $dateArray = array_unique($dateArray);
                if (!empty($dateArray)) {
                    GenerateSummarizeAttendanceJob::dispatch($dateArray, $organisasi_id, auth()->user(), $izin->karyawan_id, 'I');
                }
            }

            DB::commit();
            return response()->json(['message' => 'Izin berhasil di Approved!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function confirmed(Request $request, string $id_izin)
    {
        $decrypt_id = Crypt::decrypt(gzuncompress($id_izin));
        $izin = Izine::find($decrypt_id);

        DB::beginTransaction();
        try{
            if(!auth()->user()->hasRole('security')){
                return response()->json(['message' => 'Anda tidak memiliki akses untuk melakukan konfirmasi!'], 403);
            }

            if ($izin->jenis_izin == 'SH') {
                if ($izin->aktual_mulai_or_masuk || $izin->aktual_selesai_or_keluar) {
                    return response()->json(['message' => 'Data izin sudah di konfirmasi, silahkan reload halaman!'], 403);
                }

                if($izin->rencana_mulai_or_masuk){
                    $izin->aktual_mulai_or_masuk = now();
                } elseif ($izin->rencana_selesai_or_keluar){
                    $izin->aktual_selesai_or_keluar = now();
                }
            } elseif ($izin->jenis_izin == 'KP') {
                if ($izin->aktual_mulai_or_masuk) {
                    return response()->json(['message' => 'Data izin sudah di konfirmasi, silahkan reload halaman!'], 403);
                }

                if ($izin->aktual_selesai_or_keluar) {
                    $izin->aktual_mulai_or_masuk = now();
                } else {
                    $izin->aktual_selesai_or_keluar = now();
                }
            } elseif ($izin->jenis_izin == 'PL') {
                if ($izin->aktual_selesai_or_keluar) {
                    return response()->json(['message' => 'Data izin sudah di konfirmasi, silahkan reload halaman!'], 403);
                }

                $izin->aktual_selesai_or_keluar = now();
            } else {
                return response()->json(['message' => 'Jenis izin tidak ditemukan'], 404);
            }

            $izin->save();
            DB::commit();
            return response()->json(['message' => 'Izin berhasil di Konfirmasi!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function done(Request $request, string $id_izin)
    {
        $izin = Izine::find($id_izin);

        DB::beginTransaction();
        try{
            $aktual_mulai_or_masuk = $request->aktual_mulai_or_masukAktual;
            $aktual_selesai_or_keluar = $request->aktual_selesai_or_keluarAktual;

            $dataValidate = [
                'aktual_mulai_or_masukAktual' => ['required', 'date_format:Y-m-d', 'before_or_equal:aktual_selesai_or_keluarAktual', 'after_or_equal:' . $izin->rencana_mulai_or_masuk],
                'aktual_selesai_or_keluarAktual' => ['required', 'date_format:Y-m-d', 'after_or_equal:aktual_mulai_or_masukAktual'],
            ];

            $validator = Validator::make(request()->all(), $dataValidate);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return response()->json(['message' => $errors], 402);
            }

            $izin->aktual_mulai_or_masuk = $aktual_mulai_or_masuk;
            $izin->aktual_selesai_or_keluar = $aktual_selesai_or_keluar;
            $izin->save();

            DB::commit();
            return response()->json(['message' => 'Izin berhasil diubah!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function rejected(Request $request, string $id_izin)
    {
        $izin = Izine::find($id_izin);

        $dataValidate = [
            'rejected_note' => ['required'],
            'is_shift_malam' => ['required', 'in:Y,N'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $is_shift_malam = $request->is_shift_malam;

        DB::beginTransaction();
        try{
            if ($izin->rejected_by) {
                return response()->json(['message' => 'Pengajuan izin sudah di reject!'], 403);
            }

            if(auth()->user()->hasRole('personalia')){
                $izin->rejected_by = $is_shift_malam == 'Y' ? auth()->user()->karyawan->nama : 'HRD & GA';
            } else {
                $izin->rejected_by = auth()->user()->karyawan->nama;
            }
            $izin->rejected_at = now();
            $izin->rejected_note = $request->rejected_note;
            $izin->save();

            DB::commit();
            return response()->json(['message' => 'Izin berhasil di Reject!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export_izin_dan_skd(Request $request){

        $departemen = $request->departemen;
        $export_data = $request->export_data;
        $periode = $request->periode;
        $organisasi_id = auth()->user()->organisasi_id;
        $izins = null;
        $skds = null;

        if(!$periode){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Not Found');
            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=Not-Found.xlsx');
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            exit();
        }

        if($export_data == 'IZIN'){
            $izins = Izine::where('organisasi_id',$organisasi_id)->whereNull('rejected_by')->whereNotNull('legalized_by');
            if($departemen){
                $izins->where('departemen_id', $departemen);
            }

            if($periode){
                $month = Carbon::parse($periode)->format('m');
                $year = Carbon::parse($periode)->format('Y');
                $izins->where(function($query) use ($month, $year) {
                    $query->where(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'TM')->whereMonth('aktual_mulai_or_masuk', $month)->whereYear('aktual_mulai_or_masuk', $year);
                    })->orWhere(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'SH')
                        ->where(function($query) use ($month, $year) {
                            $query->whereMonth('rencana_selesai_or_keluar', $month)->whereYear('rencana_selesai_or_keluar', $year)
                            ->whereMonth('aktual_selesai_or_keluar', $month)->whereYear('aktual_selesai_or_keluar', $year);
                        })->orWhere(function($query) use ($month, $year) {
                            $query->whereMonth('rencana_mulai_or_masuk', $month)->whereYear('rencana_mulai_or_masuk', $year)
                            ->whereMonth('aktual_mulai_or_masuk', $month)->whereYear('aktual_mulai_or_masuk', $year);
                        });
                    })->orWhere(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'KP')->whereMonth('rencana_mulai_or_masuk', $month)->whereYear('rencana_mulai_or_masuk', $year)
                        ->whereMonth('aktual_mulai_or_masuk', $month)->whereYear('aktual_mulai_or_masuk', $year);
                    })->orWhere(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'PL')->whereMonth('aktual_selesai_or_keluar', $month)->whereYear('aktual_selesai_or_keluar', $year);
                    });
                });
            }
        }

        if($export_data == 'SKD'){
            $skds = Sakite::where('organisasi_id',$organisasi_id)->whereNull('rejected_by')->whereNotNull('legalized_by')->whereNotNull('attachment');
            if($departemen){
                $skds->where('departemen_id', $departemen);
            }

            if($periode){
                $month = Carbon::parse($periode)->format('m');
                $year = Carbon::parse($periode)->format('Y');
                $skds->whereMonth('tanggal_mulai', $month)->whereYear('tanggal_mulai', $year);
            }
        }

        if(!$izins && !$skds){
            $izins = Izine::where('organisasi_id',$organisasi_id)->whereNull('rejected_by')->whereNotNull('legalized_by');
            $skds = Sakite::where('organisasi_id',$organisasi_id)->whereNull('rejected_by')->whereNotNull('legalized_by')->whereNotNull('attachment');

            if($departemen){
                $izins->where('departemen_id', $departemen);
                $skds->where('departemen_id', $departemen);
            }

            if($periode){
                $month = Carbon::parse($periode)->format('m');
                $year = Carbon::parse($periode)->format('Y');
                $izins->where(function($query) use ($month, $year) {
                    $query->where(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'TM')->whereMonth('aktual_mulai_or_masuk', $month)->whereYear('aktual_mulai_or_masuk', $year);
                    })->orWhere(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'SH')
                        ->where(function($query) use ($month, $year) {
                            $query->whereMonth('rencana_selesai_or_keluar', $month)->whereYear('rencana_selesai_or_keluar', $year)
                            ->whereMonth('aktual_selesai_or_keluar', $month)->whereYear('aktual_selesai_or_keluar', $year);
                        })->orWhere(function($query) use ($month, $year) {
                            $query->whereMonth('rencana_mulai_or_masuk', $month)->whereYear('rencana_mulai_or_masuk', $year)
                            ->whereMonth('aktual_mulai_or_masuk', $month)->whereYear('aktual_mulai_or_masuk', $year);
                        });
                    })->orWhere(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'KP')->whereMonth('rencana_mulai_or_masuk', $month)->whereYear('rencana_mulai_or_masuk', $year)
                        ->whereMonth('aktual_mulai_or_masuk', $month)->whereYear('aktual_mulai_or_masuk', $year);
                    })->orWhere(function($query) use ($month, $year) {
                        $query->where('jenis_izin', 'PL')->whereMonth('aktual_selesai_or_keluar', $month)->whereYear('aktual_selesai_or_keluar', $year);
                    });
                });

                $skds->whereMonth('tanggal_mulai', $month)->whereYear('tanggal_mulai', $year);
            }
        }

        $izins = $izins ? $izins->get() : collect();
        $skds = $skds ? $skds->get() : collect();

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

        if($izins->count() > 0){
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Izin - '.Carbon::parse($periode)->format('F Y'));
            $row = 1;
            $col = 'A';
            $headers = [
                'No',
                'Nomor Induk Karyawan',
                'Nama',
                'Departemen',
                'Jenis Izin',
                'Aktual Mulai / Masuk',
                'Aktual Selesai / Keluar',
                'Keterangan',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'H');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:H1');

            // $izins = $izins->get();
            foreach ($izins as $izin) {

                if ($izin->jenis_izin == 'TM') {
                    $jenis_izin = 'Tidak Masuk';
                } elseif ($izin->jenis_izin == 'SH') {
                    $jenis_izin = '1/2 Hari';
                } elseif ($izin->jenis_izin == 'KP') {
                    $jenis_izin = 'Keluar Pabrik';
                } elseif ($izin->jenis_izin == 'PL') {
                    $jenis_izin = 'Pulang';
                } else {
                    $jenis_izin = 'Lainnya';
                }

                $sheet->setCellValue('A' . $row, $row - 1);
                $sheet->setCellValue('B' . $row, $izin->karyawan->ni_karyawan);
                $sheet->setCellValue('C' . $row, $izin->karyawan->nama);
                $sheet->setCellValue('D' . $row, $izin->departemen->nama);
                $sheet->setCellValue('E' . $row, $jenis_izin);
                $sheet->setCellValue('F' . $row, $izin->aktual_mulai_or_masuk);
                $sheet->setCellValue('G' . $row, $izin->aktual_selesai_or_keluar);
                $sheet->setCellValue('H' . $row, $izin->keterangan);
                $row++;
            }
        }

        if($skds->count() > 0){

            if($izins){
                $sheet = $spreadsheet->createSheet();
            } else {
                $sheet = $spreadsheet->getActiveSheet();
            }

            $sheet->setTitle('SKD - '.Carbon::parse($periode)->format('F Y'));
            $row = 1;
            $col = 'A';
            $headers = [
                'No',
                'Nomor Induk Karyawan',
                'Nama',
                'Departemen',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Keterangan',
            ];

            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->applyFromArray($fillStyle);
                $col++;
            }

            $row = 2;

            $columns = range('A', 'G');
            foreach ($columns as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->setAutoFilter('A1:G1');

            // $skds = $skds->get();
            foreach ($skds as $skd) {
                $sheet->setCellValue('A' . $row, $row - 1);
                $sheet->setCellValue('B' . $row, $skd?->karyawan?->ni_karyawan);
                $sheet->setCellValue('C' . $row, $skd?->karyawan?->nama);
                $sheet->setCellValue('D' . $row, $skd?->departemen?->nama);
                $sheet->setCellValue('E' . $row, $skd->tanggal_mulai);
                $sheet->setCellValue('F' . $row, $skd->tanggal_selesai);
                $sheet->setCellValue('G' . $row, $skd->keterangan);
                $row++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.Carbon::parse($periode)->format('M Y').' - Data Izin dan SKD.xlsx');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }
}
