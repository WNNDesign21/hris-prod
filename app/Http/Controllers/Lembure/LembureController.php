<?php

namespace App\Http\Controllers\Lembure;

use Throwable;
use Carbon\Carbon;
use App\Models\Divisi;
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
use App\Jobs\ExportSlipLemburJob;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\SettingLemburKaryawan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Lembure\ExportSlipLembur;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Helpers\Approval;

class LembureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur']) || (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)) {
            $departemens = Departemen::all();
        } else {
            $posisis = auth()->user()->karyawan->posisi;
            $departemen_ids = [];
            $divisi_ids = [];
            foreach ($posisis as $posisi) {
                if ($posisi->departemen_id !== null) {
                    $departemen_ids[] = $posisi->departemen_id;
                }

                if ($posisi->divisi_id !== null) {
                    $divisi_ids[] = $posisi->divisi_id;
                }
            }

            if (!empty($departemen_ids)) {
                $departemens = Departemen::whereIn('id_departemen', $departemen_ids)->get();
            } else {
                $departemens = Departemen::whereIn('divisi_id', $divisi_ids)->get();
            }

        }
        $organisasis = Organisasi::all();

        $dataPage = [
            'pageTitle' => "Lembur-E - Dashboard",
            'page' => 'lembure-dashboard',
            'departemens' => $departemens,
            'organisasis' => $organisasis
        ];
        return view('pages.lembur-e.index', $dataPage);
    }

    public function detail_lembur_view()
    {
        if (auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id >= 5) {
            return redirect()->route('lembure.pengajuan-lembur');
        }

        if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur']) || (auth()->user()->karyawan->posisi[0]->jabatan_id == 2 && auth()->user()->karyawan->posisi[0]->organisasi_id !== null)) {
            $departemens = Departemen::all();
        } else {
            $posisis = auth()->user()->karyawan->posisi;
            $departemen_ids = [];
            $divisi_ids = [];
            foreach ($posisis as $posisi) {
                if ($posisi->departemen_id !== null) {
                    $departemen_ids[] = $posisi->departemen_id;
                }

                if ($posisi->divisi_id !== null) {
                    $divisi_ids[] = $posisi->divisi_id;
                }
            }

            if (!empty($departemen_ids)) {
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

    public function review_lembur_view()
    {
        $posisi = auth()->user()->karyawan->posisi;
        if ($posisi[0]->jabatan_id !== 1) {
            return redirect()->route('lembure.dashboard');
        }

        $departemen_ids = $this->get_member_departemen($posisi);

        foreach ($posisi as $ps) {
            $index = array_search($ps->departemen_id, $departemen_ids);
            array_splice($departemen_ids, $index, 1);
        }
        array_push($departemen_ids, auth()->user()->karyawan->posisi[0]->departemen_id);
        $departemen_ids = array_filter(array_unique($departemen_ids));
        sort($departemen_ids);

        $departemens = Departemen::whereIn('id_departemen', $departemen_ids)->get();
        $organisasis = Organisasi::all();

        $dataPage = [
            'pageTitle' => "Lembur-E - Review Lembur",
            'page' => 'lembure-review-lembur',
            'departemens' => $departemens,
            'organisasis' => $organisasis
        ];
        return view('pages.lembur-e.review-lembur', $dataPage);
    }

    public function bypass_lembur_view()
    {
        if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
            $karyawans = Karyawan::aktif()->organisasi(auth()->user()->organisasi_id)->pluck('nama', 'id_karyawan');
        } else {
            $posisi = auth()->user()->karyawan->posisi;
            $my_posisi_ids = [];
            $member_posisi_ids = $this->get_member_posisi($posisi);
            foreach ($posisi as $ps) {
                $my_posisi_ids[] = $ps->id_posisi;
                $index = array_search($ps->id_posisi, $member_posisi_ids);
                if ($index !== false) {
                    array_splice($member_posisi_ids, $index, 1);
                }
            }

            if (auth()->user()->karyawan->posisi[0]->jabatan_id == 1) {
                $karyawans = Karyawan::select('karyawans.nama', 'karyawans.id_karyawan', 'posisis.jabatan_id')->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $member_posisi_ids)
                    ->where('posisis.jabatan_id', 2)
                    ->aktif()
                    ->pluck('karyawans.nama', 'karyawans.id_karyawan');

                $karyawans_non_member = Karyawan::select('karyawans.nama', 'karyawans.id_karyawan', 'posisis.jabatan_id')->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.parent_id', $my_posisi_ids)
                    ->where('posisis.jabatan_id', '!=', 2)
                    ->aktif()
                    ->pluck('karyawans.nama', 'karyawans.id_karyawan');
            } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 2) {
                $karyawans = Karyawan::select('karyawans.nama', 'karyawans.id_karyawan', 'posisis.jabatan_id')->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $member_posisi_ids)
                    ->where('posisis.jabatan_id', 3)
                    ->organisasi(auth()->user()->organisasi_id)
                    ->aktif()
                    ->pluck('karyawans.nama', 'karyawans.id_karyawan');

                $karyawans_non_member = Karyawan::select('karyawans.nama', 'karyawans.id_karyawan', 'posisis.jabatan_id')->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.parent_id', $my_posisi_ids)
                    ->where('posisis.jabatan_id', '!=', 3)
                    ->organisasi(auth()->user()->organisasi_id)
                    ->aktif()
                    ->pluck('karyawans.nama', 'karyawans.id_karyawan');
            } else {
                $karyawans = Karyawan::select('karyawans.nama', 'karyawans.id_karyawan', 'posisis.jabatan_id')->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.id_posisi', $member_posisi_ids)
                    ->whereIn('posisis.jabatan_id', [4, 5])
                    ->aktif()
                    ->pluck('karyawans.nama', 'karyawans.id_karyawan');

                $karyawans_non_member = Karyawan::select('karyawans.nama', 'karyawans.id_karyawan', 'posisis.jabatan_id')->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
                    ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
                    ->whereIn('posisis.parent_id', $my_posisi_ids)
                    ->whereNotIn('posisis.jabatan_id', [4, 5])
                    ->aktif()
                    ->pluck('karyawans.nama', 'karyawans.id_karyawan');
            }

            $karyawans = $karyawans->merge($karyawans_non_member);
        }

        $dataPage = [
            'pageTitle' => "Lembur-E - Bypass Lembur",
            'page' => 'lembure-bypass-lembur',
            'karyawans' => $karyawans
        ];
        return view('pages.lembur-e.bypass-lembur', $dataPage);
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
        $departemens = Departemen::all();
        $dataPage = [
            'pageTitle' => "Lembur-E - Setting Gaji Departemen",
            'page' => 'lembure-setting-gaji-departemen',
            'departemens' => $departemens
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
            8 => 'lemburs.plan_approved_by',
            9 => 'lemburs.plan_reviewed_by',
            10 => 'lemburs.plan_legalized_by',
            11 => 'lemburs.actual_checked_by',
            12 => 'lemburs.actual_approved_by',
            13 => 'lemburs.actual_reviewed_by',
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

        $issued_by = auth()->user()->karyawan->id_karyawan;
        if (!empty($issued_by)) {
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

                if ($data->status == 'WAITING') {
                    $status = '<span class="badge badge-warning">WAITING</span>';
                } elseif ($data->status == 'PLANNED') {
                    $status = '<span class="badge badge-info">PLANNED</span>';
                } elseif ($data->status == 'COMPLETED') {
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                } else {
                    $rejected = true;
                    $status = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ ' . $data->rejected_by . '-' . Carbon::parse($data->rejected_at)->format('Y-m-d') . '</small><br><small class="text-fade"> Note : ' . $data->rejected_note . '</small>';
                }

                if (auth()->user()->karyawan->posisi[0]->jabatan_id >= 4) {
                    $is_member = true;
                }

                $nestedData['id_lembur'] = $data->id_lembur;
                $nestedData['issued_date'] = Carbon::parse($data->issued_date)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['rencana_mulai_lembur'] = Carbon::parse($data->detailLembur[0]->rencana_mulai_lembur)->locale('id')->translatedFormat('l, d F Y');
                $nestedData['issued_by'] = $data->nama_karyawan;
                $nestedData['jenis_hari'] = $data->jenis_hari;
                $nestedData['total_durasi'] = $jam . ' Jam ' . $menit . ' Menit';
                $nestedData['status'] = $status;
                $nestedData['plan_checked_by'] = $data->plan_checked_by ? '✅<br><small class="text-bold">' . $data?->plan_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_checked_at)->diffForHumans() . '</small>' : '';
                $nestedData['plan_approved_by'] = $data->plan_approved_by ? '✅<br><small class="text-bold">' . $data?->plan_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_approved_at)->diffForHumans() . '</small>' : '';
                $nestedData['plan_reviewed_by'] = $data->plan_reviewed_by ? '✅<br><small class="text-bold">' . $data?->plan_reviewed_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_reviewed_at)->diffForHumans() . '</small>' : '';
                $nestedData['plan_legalized_by'] = $data->plan_legalized_by ? '✅<br><small class="text-bold">' . $data?->plan_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_legalized_at)->diffForHumans() . '</small>' : '';
                $nestedData['actual_checked_by'] = $data->actual_checked_by ? '✅<br><small class="text-bold">' . $data?->actual_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_checked_at)->diffForHumans() . '</small>' : '';
                $nestedData['actual_approved_by'] = $data->actual_approved_by ? '✅<br><small class="text-bold">' . $data?->actual_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_approved_at)->diffForHumans() . '</small>' : '';
                $nestedData['actual_reviewed_by'] = $data->actual_reviewed_by ? '✅<br><small class="text-bold">' . $data?->actual_reviewed_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_reviewed_at)->diffForHumans() . '</small>' : '';
                $nestedData['actual_legalized_by'] = $data->actual_legalized_by ? '✅<br><small class="text-bold">' . $data?->actual_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_legalized_at)->diffForHumans() . '</small>' : '';
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-info btnDetail" data-id-lembur="' . $data->id_lembur . '" data-is-member="' . ($is_member ? 'true' : 'false') . '"><i class="fas fa-eye"></i> Detail</button>
                    ' . ($data->status == 'PLANNED' && $data->issued_by == auth()->user()->karyawan->id_karyawan ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id-lembur="' . $data->id_lembur . '"><i class="far fa-check-circle"></i> Done</button><button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnRejectLembur" data-id-lembur="' . $data->id_lembur . '"><i class="far fa-times-circle"></i> Cancel</button>' : '') . '
                    ' . ($data->plan_checked_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id-lembur="' . $data->id_lembur . '"><i class="fas fa-edit"></i> Edit</button>' : '') . '
                    ' . ($data->plan_checked_by == null ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id-lembur="' . $data->id_lembur . '"><i class="fas fa-trash"></i> Delete</button>' : '') . '
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
            "column" => $request->input('order.0.column')
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
            10 => 'lemburs.plan_approved_by',
            11 => 'lemburs.plan_reviewed_by',
            12 => 'lemburs.plan_legalized_by',
            13 => 'lemburs.actual_checked_by',
            14 => 'lemburs.actual_approved_by',
            15 => 'lemburs.actual_reviewed_by',
            16 => 'lemburs.actual_legalized_by'
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

        if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
            $dataFilter['organisasi_id'] = $organisasi_id;
            $is_can_legalized = true;
        } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3) {
            $member_posisi_ids = $this->get_member_posisi($posisi);
            $dataFilter['member_posisi_ids'] = $member_posisi_ids;
            $is_can_checked = true;
            $is_has_department_head = $this->has_department_head($posisi);
        } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 2) {
            // JIKA PLANT HEAD
            if (auth()->user()->karyawan->posisi[0]->divisi_id == 3) {
                $posisis_has_div_head = Posisi::where('jabatan_id', 2)
                    ->whereHas('karyawan')
                    ->whereNot('divisi_id', 3)
                    ->where(function ($query) {
                        $query->whereNull('organisasi_id')
                            ->orWhere('organisasi_id', auth()->user()->organisasi_id);
                    })
                    ->distinct()
                    ->pluck('divisi_id')
                    ->toArray();
                $divisis = Divisi::whereNotIn('id_divisi', $posisis_has_div_head)->pluck('id_divisi');
                $dataFilter['divisi_id'] = $divisis;
                $dataFilter['organisasi_id'] = $organisasi_id;
                $is_can_approved = true;
                // JIKA NON PLANT HEAD
            } else {
                $member_posisi_ids = $this->get_member_posisi($posisi);
                $dataFilter['member_posisi_ids'] = $member_posisi_ids;
                $dataFilter['is_div_head'] = true;
                $is_can_approved = true;
            }
        }

        $filterPeriode = $request->periode;
        if (!empty($filterPeriode)) {
            $dataFilter['month'] = Carbon::createFromFormat('Y-m', $filterPeriode)->format('m');
            $dataFilter['year'] = Carbon::createFromFormat('Y-m', $filterPeriode)->format('Y');
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
                if ($data->status == 'WAITING') {
                    $status = '<span class="badge badge-warning">WAITING</span>';
                } elseif ($data->status == 'PLANNED') {
                    $status = '<span class="badge badge-info">PLANNED</span>';
                } elseif ($data->status == 'COMPLETED') {
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                } else {
                    $rejected = true;
                    $status = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ ' . $data->rejected_by . '-' . Carbon::parse($data->rejected_at)->format('Y-m-d') . '</small><br><small class="text-fade"> Note : ' . $data->rejected_note . '</small>';
                }

                //BUTTON ACTION DATATABLE
                $button_checked_plan = '';
                $button_approved_plan = '';
                $button_reviewed_plan = '';
                $button_legalized_plan = '';
                $button_checked_actual = '';
                $button_approved_actual = '';
                $button_reviewed_actual = '';
                $button_legalized_actual = '';

                $is_planned = true;
                if ($data->status == 'WAITING') {
                    $is_planned = false;
                }

                //TOMBOL REVIEWED
                if ($data->plan_reviewed_by !== null) {
                    $button_reviewed_plan = '✅<br><small class="text-bold">' . $data?->plan_reviewed_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_reviewed_at)->diffForHumans() . '</small>';
                }

                if ($data->actual_reviewed_by !== null) {
                    $button_reviewed_actual = '✅<br><small class="text-bold">' . $data?->actual_reviewed_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_reviewed_at)->diffForHumans() . '</small>';
                }

                //TOMBOL CHECKED
                if ($is_can_checked) {
                    //BUTTON CHECKED DI SISI SECTION HEAD / DEPT HEAD
                    if ($is_has_department_head) {
                        //BEFORE PLANNED
                        if ($data->plan_checked_by == null) {
                            $button_checked_plan = 'MUST CHECKED BY DEPT.HEAD';
                        } else {
                            $button_checked_plan = '✅<br><small class="text-bold">' . $data?->plan_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_checked_at)->diffForHumans() . '</small>';
                        }

                        //AFTER PLANNED
                        if ($data->status == 'COMPLETED' && $data->actual_checked_by == null) {
                            $button_checked_actual = 'MUST CHECKED BY DEPT.HEAD';
                        } elseif ($data->status == 'COMPLETED' && $data->actual_checked_by !== null) {
                            $button_checked_actual = '✅<br><small class="text-bold">' . $data?->actual_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_checked_at)->diffForHumans() . '</small>';
                        }
                    } else {
                        //BEFORE PLANNED
                        if ($data->plan_checked_by == null) {
                            $button_checked_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnChecked" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-can-checked="' . ($is_can_checked ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="far fa-check-circle"></i> Checked</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="' . $data->id_lembur . '"><i class="far fa-times-circle"></i> Reject</button></div>';
                        } else {
                            $button_checked_plan = '✅<br><small class="text-bold">' . $data?->plan_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_checked_at)->diffForHumans() . '</small>';
                        }

                        //AFTER PLANNED
                        if ($data->status == 'COMPLETED' && $data->actual_checked_by == null) {
                            $button_checked_actual = '<button class="btn btn-sm btn-success btnCheckedAktual" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="far fa-check-circle"></i> Checked</button>';
                        } elseif ($data->status == 'COMPLETED' && $data->actual_checked_by !== null) {
                            $button_checked_actual = '✅<br><small class="text-bold">' . $data?->actual_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_checked_at)->diffForHumans() . '</small>';
                        }
                    }

                    //BEFORE PLANNED
                    //BUTTON APPROVED DI SISI SECTION HEAD / DEPT HEAD
                    if ($data->plan_approved_by !== null) {
                        $button_approved_plan = '✅<br><small class="text-bold">' . $data?->plan_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_approved_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON APPROVED DI SISI SECTION HEAD / DEPT HEAD
                    if ($data->actual_approved_by !== null) {
                        $button_approved_actual = '✅<br><small class="text-bold">' . $data?->actual_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_approved_at)->diffForHumans() . '</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON LEGALIZED DI SISI SECTION HEAD / DEPT HEAD
                    if ($data->plan_legalized_by !== null) {
                        $button_legalized_plan = '✅<br><small class="text-bold">' . $data?->plan_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_legalized_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON LEGALIZED DI SISI SECTION HEAD / DEPT HEAD
                    if ($data->actual_legalized_by !== null) {
                        $button_legalized_actual = '✅<br><small class="text-bold">' . $data?->actual_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_legalized_at)->diffForHumans() . '</small>';
                    }
                }

                //TOMBOL APPROVED
                if ($is_can_approved) {
                    //BEFORE PLANNED
                    //BUTTON CHECKED DI SISI PLANT HEAD
                    if ($data->plan_checked_by !== null) {
                        $button_checked_plan = '✅<br><small class="text-bold">' . $data?->plan_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_checked_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON CHECKED DI SISI PLANT HEAD
                    if ($data->actual_checked_by !== null) {
                        $button_checked_actual = '✅<br><small class="text-bold">' . $data?->plan_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_checked_at)->diffForHumans() . '</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON APPROVED DI SISI PLANT HEAD
                    if ($data->plan_approved_by == null) {
                        if ($data->plan_checked_by !== null) {
                            $button_approved_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-can-checked="' . ($is_can_checked ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="' . $data->id_lembur . '"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }

                        //APPROVAL LANGSUNG OLEH PLANT HEAD JIKA USER YANG MEMBUAT DOKUMEN TIDAK PUNYA DEPT.HEAD
                        if (!$this->has_department_head($data->issued->posisi) && !$this->has_section_head($data->issued->posisi) && $data->plan_checked_by == null) {
                            $button_approved_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnApproved" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-can-checked="' . ($is_can_checked ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="fas fa-thumbs-up"></i> Approved</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="' . $data->id_lembur . '"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    } else {
                        //BEFORE PLANNED
                        $button_approved_plan = '✅<br><small class="text-bold">' . $data?->plan_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_approved_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON APPROVED DI SISI PLANT HEAD
                    if ($data->actual_approved_by == null) {
                        if ($data->status == 'COMPLETED' && $data->actual_checked_by !== null) {
                            $button_approved_actual = '<button class="btn btn-sm btn-success btnApprovedAktual" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="fas fa-thumbs-up"></i> Approved</button>';
                        }

                        if ($data->status == 'COMPLETED' && !$this->has_department_head($data->issued->posisi) && $data->actual_checked_by == null) {
                            $button_approved_actual = '<button class="btn btn-sm btn-success btnApprovedAktual" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="fas fa-thumbs-up"></i> Approved</button>';
                        }
                    } else {
                        //AFTER PLANNED
                        $button_approved_actual = '✅<br><small class="text-bold">' . $data?->actual_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_approved_at)->diffForHumans() . '</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON LEGALIZED DI SISI PLANT HEAD
                    if ($data->plan_legalized_by !== null) {
                        $button_legalized_plan = '✅<br><small class="text-bold">' . $data?->plan_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_legalized_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON LEGALIZED DI SISI PLANT HEAD
                    if ($data->actual_legalized_by !== null) {
                        $button_legalized_actual = '✅<br><small class="text-bold">' . $data?->actual_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_legalized_at)->diffForHumans() . '</small>';
                    }
                }

                //TOMBOL APPROVED
                if ($is_can_legalized) {
                    //BEFORE PLANNED
                    //BUTTON CHECKED DI SISI PERSONALIA
                    if ($data->plan_checked_by !== null) {
                        $button_checked_plan = '✅<br><small class="text-bold">' . $data?->plan_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_checked_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON CHECKED DI SISI PERSONALIA
                    if ($data->actual_checked_by !== null) {
                        $button_checked_actual = '✅<br><small class="text-bold">' . $data?->actual_checked_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_checked_at)->diffForHumans() . '</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON APPROVED DI SISI PERSONALIA
                    if ($data->plan_approved_by !== null) {
                        $button_approved_plan = '✅<br><small class="text-bold">' . $data?->plan_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_approved_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON APPROVED DI SISI PERSONALIA
                    if ($data->actual_approved_by !== null) {
                        $button_approved_actual = '✅<br><small class="text-bold">' . $data?->actual_approved_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_approved_at)->diffForHumans() . '</small>';
                    }

                    //BEFORE PLANNED
                    //BUTTON LEGALIZED DI SISI PERSONALIA
                    if ($data->plan_legalized_by == null) {
                        if ($data->plan_reviewed_by !== null) {
                            $button_legalized_plan = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-can-checked="' . ($is_can_checked ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="fas fa-balance-scale"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnRejectLembur" data-id-lembur="' . $data->id_lembur . '"><i class="far fa-times-circle"></i> Reject</button></div>';
                        }
                    } else {
                        //BEFORE PLANNED
                        $button_legalized_plan = '✅<br><small class="text-bold">' . $data?->plan_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->plan_legalized_at)->diffForHumans() . '</small>';
                    }

                    //AFTER PLANNED
                    //BUTTON LEGALIZED DI SISI PERSONALIA
                    if ($data->actual_legalized_by == null) {
                        if ($data->actual_reviewed_by !== null) {
                            $button_legalized_actual = '<button class="btn btn-sm btn-success btnLegalizedAktual" data-id-lembur="' . $data->id_lembur . '" data-can-approved="' . ($is_can_approved ? 'true' : 'false') . '" data-is-planned="' . ($is_planned ? 'true' : 'false') . '"><i class="fas fa-balance-scale"></i> Legalized</button>';
                        }
                    } else {
                        //AFTER PLANNED
                        $button_legalized_actual = '✅<br><small class="text-bold">' . $data?->actual_legalized_by . '</small><br><small class="text-fade">' . Carbon::parse($data->actual_legalized_at)->diffForHumans() . '</small><br><button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnRollback" data-id-lembur="' . $data->id_lembur . '"><i class="fas fa-undo"></i> Rollback</button>';
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
                $nestedData['plan_reviewed_by'] = !$rejected ? $button_reviewed_plan : '';
                $nestedData['plan_legalized_by'] = !$rejected ? $button_legalized_plan : '';
                $nestedData['actual_checked_by'] = !$rejected ? $button_checked_actual : '';
                $nestedData['actual_approved_by'] = !$rejected ? $button_approved_actual : '';
                $nestedData['actual_reviewed_by'] = !$rejected ? $button_reviewed_actual : '';
                $nestedData['actual_legalized_by'] = !$rejected ? $button_legalized_actual : '';
                $nestedData['action'] = '<button type="button" class="waves-effect waves-light btn btn-sm btn-info btnDetail" data-id-lembur="' . $data->id_lembur . '"><i class="fas fa-eye"></i> Detail</button>';

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
            "column" => $request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function review_lembur_datatable(Request $request)
    {
        $columns = array(
            1 => 'subquery.tanggal_lembur',
            2 => 'subquery.departemen',
            3 => 'subquery.status',
            4 => 'subquery.total_nominal_lembur',
            5 => 'subquery.total_durasi_lembur',
            6 => 'subqyery.total_karyawan',
            7 => 'subquery.total_dokumen'
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

        $filterPeriode = $request->periode;
        if (!empty($filterPeriode)) {
            $dataFilter['month'] = Carbon::createFromFormat('Y-m', $filterPeriode)->format('m');
            $dataFilter['year'] = Carbon::createFromFormat('Y-m', $filterPeriode)->format('Y');
        }

        $filterOrganisasi = $request->organisasi;
        if (!empty($filterOrganisasi)) {
            $dataFilter['organisasi'] = $filterOrganisasi;
        }

        $filterStatus = $request->status;
        if (!empty($filterStatus)) {
            $dataFilter['status'] = $filterStatus;
        }

        $posisi = auth()->user()->karyawan->posisi;
        $departemen_ids = $this->get_member_departemen($posisi);

        foreach ($posisi as $ps) {
            $index = array_search($ps->departemen_id, $departemen_ids);
            array_splice($departemen_ids, $index, 1);
        }
        array_push($departemen_ids, auth()->user()->karyawan->posisi[0]->departemen_id);
        $departemen_ids = array_filter(array_unique($departemen_ids));
        sort($departemen_ids);

        $filterDepartemen = $request->departemen;
        if ($filterDepartemen) {
            $dataFilter['departemen'] = $filterDepartemen;
        } else {
            $dataFilter['departemen'] = $departemen_ids;
        }

        $totalData = DetailLembur::getDataReviewLembur($dataFilter, $settings)->count();
        $totalFiltered = $totalData;

        $reviewLembur = DetailLembur::getDataReviewLembur($dataFilter, $settings);
        $totalFiltered = DetailLembur::countDataReviewLembur($dataFilter);
        $dataTable = [];

        if (!empty($reviewLembur)) {
            $count = $start;
            foreach ($reviewLembur as $data) {
                $count++;
                if ($data->status == 'PLANNING') {
                    $status = '<span class="badge badge-info">PLANNING</span>';
                } else {
                    $status = '<span class="badge badge-success">ACTUAL</span>';
                }

                if ($data->departemen) {
                    $departemen = '<p>' . $data->departemen . '<br><small class="text-fade">' . $data->organisasi . '</small></p>';
                } else {
                    $departemen = '<p>' . $data->divisi . '<br><small class="text-fade">' . $data->organisasi . '</small></p>';
                }

                $jam = floor($data->total_durasi_lembur / 60);
                $menit = $data->total_durasi_lembur % 60;

                $nestedData['checkbox'] = $data->tanggal_lembur . '|' . $data->departemen_id . '|' . $data->divisi_id . '|' . $data->organisasi_id . '|' . $data->status;
                $nestedData['tanggal_lembur'] = Carbon::parse($data->tanggal_lembur)->format('d M Y');
                $nestedData['departemen'] = $departemen;
                $nestedData['status'] = $status;
                $nestedData['total_durasi_lembur'] = $jam . ' Jam ' . $menit . ' Menit';
                $nestedData['total_nominal_lembur'] = 'Rp. ' . number_format($data->total_nominal_lembur, 0, ',', '.');
                $nestedData['total_karyawan'] = $data->total_karyawan;
                $nestedData['total_dokumen'] = $data->total_dokumen;
                $nestedData['aksi'] = '<button type="button" class="waves-effect waves-light btn btn-sm btn-info btnDetail" data-departemen-id="' . $data->departemen_id . '" data-divisi-id="' . $data->divisi_id . '" data-organisasi-id="' . $data->organisasi_id . '" data-tanggal-lembur="' . $data->tanggal_lembur . '" data-status="' . $data->status . '" data-departemen="' . $data->departemen . '" data-organisasi="' . $data->organisasi . '"><i class="fas fa-eye"></i> Detail</button>';

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
            "column" => $request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function detail_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'subquery.lembur_id',
            1 => 'subquery.nama',
            2 => 'subquery.posisi',
            3 => 'subquery.departemen',
            4 => 'subquery.aktual_mulai_lembur',
            5 => 'subquery.aktual_selesai_lembur',
            6 => 'subquery.durasi',
            7 => 'subquery.nominal',
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

        $filterPeriode = $request->periode;
        if (isset($filterPeriode)) {
            $dataFilter['month'] = Carbon::parse($filterPeriode)->format('m');
            $dataFilter['year'] = Carbon::parse($filterPeriode)->format('Y');
        } else {
            $dataFilter['month'] = date('m');
            $dataFilter['year'] = date('Y');
        }

        if (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3)) {
            $posisi = auth()->user()->karyawan->posisi;
            $member_posisi_ids = $this->get_member_posisi($posisi);
            $dataFilter['member_posisi_ids'] = $member_posisi_ids;
        }

        $filterDepartemen = $request->departemen;
        if (isset($filterDepartemen)) {
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
                $nestedData['durasi'] = $jam . ' jam ' . $menit . ' menit';
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
            "column" => $request->input('order.0.column')
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
                        <button class="btn btn-warning updateUpahLembur" type="button" data-id-setting-lembur-karyawan="' . $data->id_setting_lembur_karyawan . '" data-karyawan-id="' . $data->id_karyawan . '"><i class="fas fa-save"></i></button>
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
            "column" => $request->input('order.0.column')
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

        $departemen = $request->departemen;
        if (!empty($departemen)) {
            $dataFilter['departemen'] = $departemen;
        }

        $periode = $request->periode;
        if (!empty($periode)) {
            $dataFilter['year'] = Carbon::parse($periode)->format('Y');
            $dataFilter['month'] = Carbon::parse($periode)->format('m');
        } else {
            $dataFilter['year'] = Carbon::now()->format('Y');
            $dataFilter['month'] = Carbon::now()->format('m');
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
                        <input type="number" value="' . ($data->presentase ?? 0) . '" min="0" class="form-control inputGajiDepartemen" id="presentase_' . $count . '"/>
                    </div>
                ';
                $nestedData['total_gaji'] = '
                    <div class="input-group mb-3">
                        <input type="number" value="' . ($data->total_gaji ?? 0) . '" min="0" class="form-control inputGajiDepartemen" id="total_gaji_' . $count . '"/>
                        <button class="btn btn-warning updateGajiDepartemen" type="button" data-id-gaji-departemen="' . $data->id_gaji_departemen . '" data-departemen-id="' . $data->departemen_id . '" data-urutan="' . $count . '"><i class="fas fa-save"></i></button>
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
            "column" => $request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function export_slip_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'departemens.nama',
            1 => 'export_slip_lemburs.created_at',
            2 => 'export_slip_lemburs.periode',
            3 => 'export_slip_lemburs.status',
            4 => 'export_slip_lemburs.message',
            5 => 'export_slip_lemburs.attachment',
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

        $filterDepartemen = $request->departemen;
        if (isset($filterDepartemen)) {
            $dataFilter['departemen_id'] = $filterDepartemen;
        }
        $filterStatus = $request->status;
        if (isset($filterStatus)) {
            $dataFilter['status'] = $filterStatus;
        }
        $filterPeriode = $request->periode;
        if (isset($filterPeriode)) {
            $dataFilter['periode'] = $filterPeriode;
        }

        $totalData = ExportSlipLembur::all()->count();
        $totalFiltered = $totalData;

        $export_slip_lemburs = ExportSlipLembur::getData($dataFilter, $settings);
        $totalFiltered = ExportSlipLembur::countData($dataFilter);

        $dataTable = [];

        if (!empty($export_slip_lemburs)) {
            foreach ($export_slip_lemburs as $data) {
                if ($data->status == 'IP') {
                    $status = '<span class="badge badge-warning">IN PROGRESS</span>';
                    $attachment = '<i class="fas fa-sync-alt fa-spin fs-32 text-fade"></i>';
                } elseif ($data->status == 'FL') {
                    $status = '<span class="badge badge-danger">FAILED</span>';
                    $attachment = 'Attachment Not Found';
                } elseif ($data->status == 'CO') {
                    $status = '<span class="badge badge-success">COMPLETED</span>';
                    if ($data->attachment) {
                        $attachment = '<a class="btn-sm btn-primary" href="' . asset('storage/' . $data->attachment) . '" target="_blank"><i class="fas fa-file-excel"></i> Download</a>';
                    } else {
                        $attachment = '-';
                    }
                }

                $nestedData['departemen'] = $data->departemen ?? 'ALL DEPARTMENT';
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d F Y, H:i');
                $nestedData['periode'] = Carbon::parse($data->periode)->format('F Y');
                $nestedData['status'] = $status;
                $nestedData['message'] = $data->message;
                $nestedData['attachment'] = $attachment;

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
            "column" => $request->input('order.0.column')
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
            'jenis_hari' => ['required', 'in:WD,WE'],
            'karyawan_id.*' => ['required', 'distinct'],
            'job_description.*' => ['required'],
            'rencana_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:rencana_selesai_lembur.*', 'after_or_equal:today'],
            'rencana_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:rencana_mulai_lembur.*'],
        ];

        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 402);
        }

        $jenis_hari = $request->jenis_hari;
        $karyawan_ids = $request->karyawan_id;
        $job_descriptions = $request->job_description;
        $rencana_mulai_lemburs = $request->rencana_mulai_lembur;
        $rencana_selesai_lemburs = $request->rencana_selesai_lembur;

        $user = auth()->user();
        $issued_by = optional($user->karyawan)->id_karyawan;
        $organisasi_id = $user->organisasi_id;
        $departemen_id = optional(optional($user->karyawan)->posisi[0] ?? null)->departemen_id;
        $divisi_id = optional(optional($user->karyawan)->posisi[0] ?? null)->divisi_id;

        DB::beginTransaction();
        try {
            // Batas pengajuan lembur
            $onoff_batas_pengajuan = SettingLembur::where('setting_name', 'onoff_batas_pengajuan_lembur')
                ->where('organisasi_id', $organisasi_id)->value('value') ?? 'Y';
            $jam_batas_pengajuan = SettingLembur::where('setting_name', 'batas_pengajuan_lembur')
                ->where('organisasi_id', $organisasi_id)->value('value') ?? '23:59';

            if ($onoff_batas_pengajuan === 'Y' && Carbon::now()->format('H:i') > $jam_batas_pengajuan) {
                DB::rollBack();
                return response()->json([
                    'message' => "Batas waktu pengajuan lembur telah berakhir ($jam_batas_pengajuan WIB), silahkan lakukan bypass ke Plant Head!"
                ], 402);
            }

            // Semua rencana harus di tanggal yang sama
            $date0 = Carbon::parse($rencana_mulai_lemburs[0])->format('Y-m-d');
            foreach ($rencana_mulai_lemburs as $start) {
                if (Carbon::parse($start)->format('Y-m-d') !== $date0) {
                    DB::rollBack();
                    return response()->json(['message' => 'Seluruh rencana mulai lembur harus berada pada tanggal yang sama!'], 402);
                }
            }

            // Buat header
            /** @var Lembure $header */
            $header = Lembure::create([
                'id_lembur' => 'LEMBUR-' . Str::random(4) . '-' . date('YmdHis'),
                'issued_by' => $issued_by,
                'issued_date' => now(),
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'divisi_id' => $divisi_id,
                'jenis_hari' => $jenis_hari,
                'status' => 'WAITING',
            ]);

            // ====== Auto routing Plan (Checked/Approved/Reviewed/Legalized) ======
            $creator = $user;
            $creatorKaryawan = $creator->karyawan;
            $creatorNama = $creatorKaryawan->nama ?? 'SYSTEM';
            $creatorPosisi = $creatorKaryawan?->posisi ?? collect();
            $creatorJabatanId = optional($creatorPosisi->first())->jabatan_id;
            $isAdminDept = $creator->hasRole('admin-dept');

            // Ketersediaan atasan pada struktur pembuat
            $hasLeader = \App\Helpers\Approval::HasLeader($creatorPosisi);
            $hasSecHead = \App\Helpers\Approval::HasSectionHead($creatorPosisi);

            // Dept.Head ada jika helper mendeteksi ATAU memang ada posisi jabatan_id=2 pada scope pembuat
            $hasDeptHead = \App\Helpers\Approval::HasDepartmentHead($creatorPosisi)
                || \App\Models\Posisi::where('organisasi_id', $organisasi_id)
                    ->where('divisi_id', $divisi_id)
                    ->where('departemen_id', $departemen_id)
                    ->where('jabatan_id', 2) // Dept.Head
                    ->exists();

            // Ambil leader name dengan aman (helper bisa return relasi/collection/null)
            $leader = \App\Helpers\Approval::GetLeader($creatorPosisi);
            $leaderNama = $leader instanceof \Illuminate\Support\Collection
                ? optional($leader->first())->nama
                : optional($leader)->nama;

            // Reviewer (BOD)
            $bodNama = \App\Helpers\Approval::GetDirector($creatorPosisi)
                ?? $this->getDefaultBODName($departemen_id, $divisi_id, $organisasi_id)
                ?? 'AUTO-SYSTEM-BOD';
            $now = now();

            $update = [];

            // Helper set plan fields
            $applyAuto = function (?string $checkedBy, ?string $approvedBy, bool $autoReview, bool $autoLegalize) use (&$update, $now, $bodNama) {
                if ($checkedBy) {
                    $update['plan_checked_by'] = $checkedBy;
                    $update['plan_checked_at'] = $now;
                }
                if ($approvedBy) {
                    $update['plan_approved_by'] = $approvedBy;
                    $update['plan_approved_at'] = $now;
                }
                if ($autoReview) {
                    $update['plan_reviewed_by'] = $bodNama;
                    $update['plan_reviewed_at'] = $now;
                }
                if ($autoLegalize) {
                    $update['plan_legalized_by'] = 'HR & GA';
                    $update['plan_legalized_at'] = $now;
                }
                // Status PLANNED hanya bila sudah Approved di tahap Plan
                $update['status'] = $approvedBy ? 'PLANNED' : 'WAITING';
            };

            // Tentukan tipe pembuat
            $maker = 'unknown';
            if ($isAdminDept) {
                $maker = 'admin';
            } elseif ($creatorJabatanId == 5) {       // Leader
                $maker = 'leader';
            } elseif ($creatorJabatanId == 4) {       // Sec.Head
                $maker = 'sec';
            } elseif ($creatorJabatanId == 2) {       // Dept.Head
                $maker = 'dept';
            }

            // Mapping skenario
            switch ($maker) {
                // ---------------- ADMIN DEPT ----------------
                case 'admin':
                    if (!$hasLeader && !$hasSecHead && !$hasDeptHead) {
                        // 1) Tidak ada Leader/Sec/Dept -> auto semua oleh admin
                        $applyAuto($creatorNama, $creatorNama, true, true);
                    } elseif (!$hasLeader && !$hasSecHead && $hasDeptHead) {
                        // 2) Tidak ada Leader/Sec, ADA Dept -> menunggu approve Dept.Head
                        $applyAuto($creatorNama, null, false, false);
                    } elseif ($hasLeader && !$hasSecHead && $hasDeptHead) {
                        // 3) ADA Leader & Dept, tidak ada Sec -> checked by Leader, tunggu approve Dept.Head
                        $applyAuto($leaderNama ?: $creatorNama, null, false, false);
                    } elseif ($hasSecHead && $hasDeptHead) {
                        // 4) ADA Sec & Dept -> waiting (checked oleh Sec.Head manual)
                        $applyAuto(null, null, false, false);
                    } elseif ($hasSecHead && !$hasDeptHead) {
                        // NEW: ADA Sec.Head TAPI TIDAK ADA Dept.Head
                        //      → tunggu Sec.Head melakukan Plan Checked; setelah itu auto approve/review/legalized di handler berikutnya.
                        $applyAuto(null, null, false, false);
                    } else {
                        // fallback aman
                        $applyAuto($creatorNama, null, false, false);
                    }
                    break;

                // ---------------- LEADER ----------------
                case 'leader':
                    if (!$hasSecHead && !$hasDeptHead) {
                        // 1) Tidak ada Sec/Dept -> auto semua oleh Leader
                        $applyAuto($creatorNama, $creatorNama, true, true);
                    } elseif (!$hasSecHead && $hasDeptHead) {
                        // 2) Tidak ada Sec, ADA Dept -> tunggu approve Dept.Head
                        $applyAuto($creatorNama, null, false, false);
                    } elseif ($hasSecHead && $hasDeptHead) {
                        // 3) ADA Sec & Dept -> waiting (checked oleh Sec.Head manual)
                        $applyAuto(null, null, false, false);
                    } elseif ($hasSecHead && !$hasDeptHead) {
                        // NEW: ADA Sec.Head TAPI TIDAK ADA Dept.Head
                        //      → tunggu Sec.Head melakukan Plan Checked; setelah itu auto approve/review/legalized di handler berikutnya.
                        $applyAuto(null, null, false, false);
                    } else {
                        $applyAuto($creatorNama, null, false, false);
                    }
                    break;

                // ---------------- SECTION HEAD ----------------
                case 'sec':
                    if (!$hasDeptHead) {
                        // 1) Tidak ada Dept.Head -> auto semua oleh Sec.Head
                        $applyAuto($creatorNama, $creatorNama, true, true);
                    } else {
                        // 2) ADA Dept.Head -> approved/reviewed/legalized menunggu
                        $applyAuto($creatorNama, null, false, false);
                    }
                    break;

                // ---------------- DEPARTMENT HEAD ----------------
                case 'dept':
                    // Selalu auto semua oleh Dept.Head
                    $applyAuto($creatorNama, $creatorNama, true, true);
                    break;

                default:
                    // default aman: checked oleh pembuat, approval menunggu
                    $applyAuto($creatorNama, null, false, false);
                    break;
            }

            if (!empty($update)) {
                $header->update($update);
            }
            // ====== /Auto routing Plan ======

            // ==== Detail ====
            $total_durasi = 0;
            $total_nominal = 0;
            $data_detail = [];

            foreach ($karyawan_ids as $i => $karyawan_id) {
                $karyawan = Karyawan::find($karyawan_id);
                if (!$karyawan) {
                    DB::rollBack();
                    return response()->json(['message' => "Karyawan dengan ID $karyawan_id tidak ditemukan."], 404);
                }

                $gaji_lembur = optional($karyawan->settingLembur)->gaji;
                if (is_null($gaji_lembur)) {
                    DB::rollBack();
                    return response()->json(['message' => $karyawan->nama . ' belum memiliki setting gaji lembur!'], 402);
                }

                $pembagi_upah = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')
                    ->where('organisasi_id', $karyawan->user->organisasi_id)
                    ->value('value');

                $startPlan = $this->pembulatan_menit_ke_bawah($rencana_mulai_lemburs[$i]);
                $endPlan = $this->pembulatan_menit_ke_bawah($rencana_selesai_lemburs[$i]);

                // Duplikat: sudah approved rencana & aktual pada rentang tsb
                $exists = DetailLembur::where('karyawan_id', $karyawan_id)
                    ->where('is_rencana_approved', 'Y')
                    ->where('is_aktual_approved', 'Y')
                    ->where(function ($q) use ($startPlan, $endPlan) {
                        $q->whereBetween('rencana_mulai_lembur', [$startPlan, $endPlan])
                            ->orWhereBetween('rencana_selesai_lembur', [$startPlan, $endPlan])
                            ->orWhere(function ($qq) use ($startPlan, $endPlan) {
                                $qq->where('rencana_mulai_lembur', '<=', $startPlan)
                                    ->where('rencana_selesai_lembur', '>=', $endPlan);
                            });
                    })
                    ->exists();

                if ($exists) {
                    DB::rollBack();
                    return response()->json(['message' => $karyawan->nama . ' sudah memiliki data lembur pada range tanggal yang direncanakan'], 402);
                }

                $durIstirahat = $this->overtime_resttime_per_minutes($startPlan, $endPlan, $karyawan->user->organisasi_id);
                $durasi = $this->calculate_overtime_per_minutes($startPlan, $endPlan, $karyawan->user->organisasi_id);

                if ($durasi < 60) {
                    DB::rollBack();
                    return response()->json(['message' => 'Durasi lembur ' . $karyawan->nama . ' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                }

                if (!$karyawan->posisi()->exists()) {
                    DB::rollBack();
                    return response()->json(['message' => $karyawan->nama . ' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
                }

                $durKonv = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $karyawan_id);
                $uangMakan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $karyawan_id);
                $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id);

                $data_detail[] = [
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $karyawan->user->organisasi_id,
                    'departemen_id' => $karyawan->posisi[0]?->departemen_id,
                    'divisi_id' => $karyawan->posisi[0]?->divisi_id,
                    'rencana_mulai_lembur' => $startPlan,
                    'rencana_selesai_lembur' => $endPlan,
                    'deskripsi_pekerjaan' => $job_descriptions[$i],
                    'durasi_istirahat' => $durIstirahat,
                    'durasi_konversi_lembur' => $durKonv,
                    'gaji_lembur' => $gaji_lembur,
                    'pembagi_upah_lembur' => $pembagi_upah,
                    'uang_makan' => $uangMakan,
                    'durasi' => $durasi,
                    'nominal' => $nominal,
                ];

                $total_durasi += $durasi;
                $total_nominal += $nominal;
            }

            $header->detailLembur()->createMany($data_detail);

            // Update total header
            $header->update([
                'total_durasi' => $total_durasi,
                'total_nominal' => $total_nominal,
            ]);

            DB::commit();
            return response()->json(['message' => 'Lembur Berhasil Dibuat'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in store function: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function getDefaultBODName(?int $departemen_id, ?int $divisi_id, ?int $organisasi_id): ?string
    {
        try {
            // 1) BOD by departemen
            if (!is_null($departemen_id)) {
                $pos = \App\Models\Posisi::with([
                    'karyawan' => function ($q) {
                        $q->select('karyawans.id_karyawan', 'karyawans.nama');
                    }
                ])
                    ->where('jabatan_id', 1)
                    ->where('departemen_id', $departemen_id)
                    ->whereHas('karyawan')
                    ->first();
                if ($pos && $pos->karyawan)
                    return optional($pos->karyawan->first())->nama;
            }

            // 2) BOD by divisi
            if (!is_null($divisi_id)) {
                $pos = \App\Models\Posisi::with([
                    'karyawan' => function ($q) {
                        $q->select('karyawans.id_karyawan', 'karyawans.nama');
                    }
                ])
                    ->where('jabatan_id', 1)
                    ->where('divisi_id', $divisi_id)
                    ->whereHas('karyawan')
                    ->first();
                if ($pos && $pos->karyawan)
                    return optional($pos->karyawan->first())->nama;
            }

            // 3) BOD by organisasi
            if (!is_null($organisasi_id)) {
                $pos = \App\Models\Posisi::with([
                    'karyawan' => function ($q) {
                        $q->select('karyawans.id_karyawan', 'karyawans.nama');
                    }
                ])
                    ->where('jabatan_id', 1)
                    ->where('organisasi_id', $organisasi_id)
                    ->whereHas('karyawan')
                    ->first();
                if ($pos && $pos->karyawan)
                    return optional($pos->karyawan->first())->nama;
            }

            // 4) BOD global (semua scope NULL)
            $pos = \App\Models\Posisi::with([
                'karyawan' => function ($q) {
                    $q->select('karyawans.id_karyawan', 'karyawans.nama');
                }
            ])
                ->where('jabatan_id', 1)
                ->whereNull('departemen_id')
                ->whereNull('divisi_id')
                ->whereNull('organisasi_id')
                ->whereHas('karyawan')
                ->first();
            if ($pos && $pos->karyawan)
                return optional($pos->karyawan->first())->nama;

        } catch (\Throwable $e) {
            \Log::warning("getDefaultBODName fallback: " . $e->getMessage());
        }
        return null;
    }

    public function bypass_lembur_store(Request $request)
    {
        $dataValidate = [
            'issued_by' => ['required', 'exists:karyawans,id_karyawan'],
            'jenis_hari' => ['required', 'in:WD,WE'],
            'karyawan_id.*' => ['required', 'distinct'],
            'job_description.*' => ['required'],
            'keterangan.*' => ['nullable'],
            'rencana_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:rencana_selesai_lembur.*'],
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
        $issued_by = $request->issued_by;
        $keterangan = $request->keterangan;

        DB::beginTransaction();
        try {
            $karyawan_issued = Karyawan::find($issued_by);
            $organisasi_id = $karyawan_issued->user->organisasi_id;
            $departemen_id = $karyawan_issued->posisi[0]->departemen_id;
            $divisi_id = $karyawan_issued->posisi[0]->divisi_id;
            $posisi_issued = $karyawan_issued->posisi;

            $date = Carbon::parse($rencana_mulai_lemburs[0])->format('Y-m-d');
            foreach ($rencana_mulai_lemburs as $key => $start) {
                if (Carbon::parse($start)->format('Y-m-d') !== $date) {
                    DB::rollback();
                    return response()->json(['message' => 'Seluruh rencana mulai lembur harus berada pada tanggal yang sama!'], 402);
                }
            }

            $header = Lembure::create([
                'id_lembur' => 'LEMBUR-' . Str::random(4) . '-' . date('YmdHis'),
                'issued_by' => $issued_by,
                'issued_date' => now(),
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'divisi_id' => $divisi_id,
                'jenis_hari' => $jenis_hari
            ]);

            if (auth()->user()->hasRole('atasan')) {
                $prefix = 'BP-';

                if ($posisi_issued[0]->jabatan_id >= 5) {
                    if (!$this->has_department_head($posisi_issued) && !$this->has_section_head($posisi_issued)) {
                        $checked_by = $karyawan_issued->nama;
                        $header->update([
                            'plan_checked_by' => $checked_by,
                            'plan_checked_at' => now(),
                        ]);
                    } else {
                        $checked_by = auth()->user()->karyawan->nama;
                        $header->update([
                            'plan_checked_by' => $checked_by,
                            'plan_checked_at' => now(),
                        ]);
                    }
                }

                if ($posisi_issued[0]->jabatan_id == 4) {
                    if (!$this->has_department_head($posisi_issued)) {
                        $checked_by = $karyawan_issued->nama;
                        $header->update([
                            'plan_checked_by' => $checked_by,
                            'plan_checked_at' => now(),
                        ]);
                    } else {
                        $checked_by = auth()->user()->karyawan->nama;
                        $header->update([
                            'plan_checked_by' => $checked_by,
                            'plan_checked_at' => now(),
                        ]);
                    }
                }

                if ($posisi_issued[0]->jabatan_id == 3) {
                    $checked_by = $karyawan_issued->nama;
                    $header->update([
                        'plan_checked_by' => $checked_by,
                        'plan_checked_at' => now(),
                        'actual_checked_by' => $checked_by,
                        'actual_checked_at' => now(),
                    ]);
                }

                if ($posisi_issued[0]->jabatan_id <= 2) {
                    $checked_and_approved = $karyawan_issued->nama;
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
            } elseif (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
                $prefix = 'BYPASS-';
                $header->update([
                    'status' => 'COMPLETED',
                    'plan_checked_by' => 'BYPASS',
                    'plan_checked_at' => now(),
                    'plan_approved_by' => 'BYPASS',
                    'plan_approved_at' => now(),
                    'plan_reviewed_by' => 'BYPASS',
                    'plan_reviewed_at' => now(),
                    'plan_legalized_by' => 'BYPASS',
                    'plan_legalized_at' => now(),
                    'actual_checked_by' => 'BYPASS',
                    'actual_checked_at' => now(),
                    'actual_approved_by' => 'BYPASS',
                    'actual_approved_at' => now(),
                    'actual_reviewed_by' => 'BYPASS',
                    'actual_reviewed_at' => now(),
                    'actual_legalized_by' => 'BYPASS',
                    'actual_legalized_at' => now(),
                ]);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Anda tidak memiliki akses untuk membuat bypass lembur'], 402);
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

                //PENGECEKAN DUPLIKAT
                $detail_lembur_exist = DetailLembur::where('karyawan_id', $karyawan_id)
                    ->where('is_rencana_approved', 'Y')
                    ->where('is_aktual_approved', 'Y')
                    ->where(function ($query) use ($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur) {
                        $query->whereBetween('rencana_mulai_lembur', [$datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur])
                            ->orWhereBetween('rencana_selesai_lembur', [$datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur])
                            ->orWhere(function ($query) use ($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur) {
                                $query->where('rencana_mulai_lembur', '<=', $datetime_rencana_mulai_lembur)
                                    ->where('rencana_selesai_lembur', '>=', $datetime_rencana_selesai_lembur);
                            });
                    })
                    ->exists();

                if ($detail_lembur_exist) {
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama . ' sudah memiliki data lembur pada range tanggal yang direncanakan'], 402);
                }

                $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $karyawan_id);
                $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $karyawan_id);

                if ($durasi < 60) {
                    DB::rollback();
                    return response()->json(['message' => 'Durasi lembur ' . $karyawan->nama . ' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                }

                if (!$karyawan->posisi()->exists()) {
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama . ' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
                }

                $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id);
                $data_detail_lembur[] = [
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $karyawan->user->organisasi_id,
                    'departemen_id' => $karyawan->posisi[0]?->departemen_id,
                    'divisi_id' => $karyawan->posisi[0]?->divisi_id,
                    'rencana_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'rencana_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'aktual_mulai_lembur' => $datetime_rencana_mulai_lembur,
                    'aktual_selesai_lembur' => $datetime_rencana_selesai_lembur,
                    'keterangan' => $prefix . ($keterangan[$key] ?? ''),
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

                $tanggal_lembur = Carbon::parse($datetime_rencana_mulai_lembur)->format('Y-m-d');
                $lembur_harian = LemburHarian::whereDate('tanggal_lembur', $tanggal_lembur)->where('organisasi_id', $organisasi_id)->where('departemen_id', $departemen_id)->where('divisi_id', $divisi_id)->first();
                if ($lembur_harian) {
                    $lembur_harian->total_durasi_lembur = $lembur_harian->total_durasi_lembur + $durasi;
                    $lembur_harian->total_nominal_lembur = $lembur_harian->total_nominal_lembur + $nominal;
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

            $header->detailLembur()->createMany($data_detail_lembur);

            //Update Total Durasi Lagi
            $header->update([
                'total_durasi' => $total_durasi,
                'total_nominal' => $total_nominal
            ]);

            DB::commit();
            return response()->json(['message' => 'Bypass Lembur Berhasil Dibuat'], 200);
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
                if ($start->format('H:i') > $break['start']) {
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
            if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
                if ($start->format('H:i') > $break['start']) {
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

        if ($jenis_hari == 'WD') {
            if ($jabatan_id >= 5) {
                if ($convert_duration >= 4) {
                    return $uang_makan;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            if ($jabatan_id >= 5) {
                if ($convert_duration >= 4) {
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

        if ($jenis_hari == 'WD') {
            $jam_pertama = $convert_duration == 1 ? ($convert_duration * 1.5) : (1 * 1.5);
            $jam_kedua = $convert_duration > 1 ? ($convert_duration - 1) * 2 : 0;
            $durasi_konversi_lembur = $jam_pertama + $jam_kedua;
        } else {
            $delapan_jam_pertama = $convert_duration <= 8 ? ($convert_duration * 2) : (8 * 2);
            $jam_ke_sembilan = $convert_duration > 8 && $convert_duration <= 9 ? (($convert_duration - 8) * 3) : ($convert_duration > 9 ? 1 * 3 : 0);
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
        if ($jenis_hari == 'WD') {

            //PERHITUNGAN UNTUK LEADER DAN STAFF
            if ($jabatan_id >= 5) {
                $jam_pertama = $convert_duration == 1 ? ($convert_duration * $upah_sejam * 1.5) : (1 * $upah_sejam * 1.5);
                $jam_kedua = $convert_duration > 1 ? ($convert_duration - 1) * $upah_sejam * 2 : 0;
                $nominal_lembur = $jam_pertama + $jam_kedua;

                if ($convert_duration >= 4) {
                    $nominal_lembur += $uang_makan;
                }

                //PERHITUNGAN UNTUK JABATAN LAINNYA
            } elseif ($jabatan_id == 4) {
                if ($convert_duration >= 3) {
                    $nominal_lembur = $insentif_section_head_3;
                } elseif ($convert_duration >= 2) {
                    $nominal_lembur = $insentif_section_head_2;
                } elseif ($convert_duration >= 1) {
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
            if ($jabatan_id >= 5) {

                //lOGIC AFTER REVISI
                $delapan_jam_pertama = $convert_duration <= 8 ? ($convert_duration * $upah_sejam * 2) : (8 * $upah_sejam * 2);
                $jam_ke_sembilan = $convert_duration > 8 && $convert_duration <= 9 ? (($convert_duration - 8) * $upah_sejam * 3) : ($convert_duration > 9 ? $upah_sejam * 3 : 0);
                $jam_ke_sepuluh = $convert_duration >= 10 ? ($convert_duration - 9) * $upah_sejam * 4 : 0;
                $nominal_lembur = $delapan_jam_pertama + $jam_ke_sembilan + $jam_ke_sepuluh;

                if ($convert_duration >= 4) {
                    $nominal_lembur += $uang_makan;
                }

                //PERHITUNGAN UNTUK SECTION HEAD
            } elseif ($jabatan_id == 4) {
                if ($convert_duration >= 4) {
                    $nominal_lembur = $insentif_section_head_4;
                } elseif ($convert_duration >= 3) {
                    $nominal_lembur = $insentif_section_head_3;
                } elseif ($convert_duration >= 2) {
                    $nominal_lembur = $insentif_section_head_2;
                } elseif ($convert_duration >= 1) {
                    $nominal_lembur = $insentif_section_head_1;
                } else {
                    $nominal_lembur = 0;
                }

                //PERHITUNGAN UNTUK DEPARTEMEN HEAD
            } elseif ($jabatan_id == 3) {
                if ($convert_duration >= 4) {
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
            'jenis_hariEdit' => ['required', 'in:WD,WE'],
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
            $total_nominal = 0;

            if ($lembur) {
                $lembur->jenis_hari = $jenis_hari;
                $lembur->save();
            } else {
                DB::rollback();
                return response()->json(['message' => 'ID Lembur tidak ditemukan, hubungi ICT'], 402);
            }

            if (isset($karyawan_ids_new) || isset($job_descriptions_new) || isset($rencana_mulai_lemburs_new) || isset($rencana_selesai_lemburs_new)) {
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

                $date = Carbon::parse($rencana_mulai_lemburs[0])->format('Y-m-d');
                foreach ($rencana_mulai_lemburs_new as $key => $start) {
                    if (Carbon::parse($start)->format('Y-m-d') !== $date) {
                        DB::rollback();
                        return response()->json(['message' => 'Seluruh rencana mulai lembur harus berada pada tanggal yang sama!'], 402);
                    }
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

                    if ($durasi_new < 60) {
                        DB::rollback();
                        return response()->json(['message' => 'Durasi lembur ' . $karyawan_new->nama . ' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                    }

                    if (!$karyawan_new->posisi()->exists()) {
                        DB::rollback();
                        return response()->json(['message' => $karyawan_new->nama . ' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
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

                    $total_durasi += $durasi_new;
                    $total_nominal += $nominal_new;
                }

                $lembur->detailLembur()->createMany($data_detail_lembur_new);
            }

            foreach ($karyawan_ids as $key => $id_kry) {
                $karyawan = Karyawan::find($id_kry);
                $gaji_lembur = $karyawan->settingLembur->gaji;
                $pembagi_upah_lembur = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')->where('organisasi_id', $karyawan->user->organisasi_id)->first()->value;
                $datetime_rencana_mulai_lembur = $this->pembulatan_menit_ke_bawah($rencana_mulai_lemburs[$key]);
                $datetime_rencana_selesai_lembur = $this->pembulatan_menit_ke_bawah($rencana_selesai_lemburs[$key]);
                $durasi_istirahat = $this->overtime_resttime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi = $this->calculate_overtime_per_minutes($datetime_rencana_mulai_lembur, $datetime_rencana_selesai_lembur, $karyawan->user->organisasi_id);
                $durasi_konversi_lembur = $this->calculate_durasi_konversi_lembur($jenis_hari, $durasi, $id_kry);
                $uang_makan = $this->calculate_overtime_uang_makan($jenis_hari, $durasi, $id_kry);

                if ($durasi < 60) {
                    DB::rollback();
                    return response()->json(['message' => 'Durasi lembur ' . $karyawan->nama . ' kurang dari 1 jam, tidak perlu menginput SPL'], 402);
                }

                if (!$karyawan->posisi()->exists()) {
                    DB::rollback();
                    return response()->json(['message' => $karyawan->nama . ' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 402);
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
                $total_nominal += $nominal;
            }

            //Update Total Durasi Lagi
            $lembur->update([
                'total_durasi' => $total_durasi,
                'total_nominal' => $total_nominal,
            ]);

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
        try {
            $setting_lembur_karyawan = SettingLemburKaryawan::find($request->id_setting_lembur_karyawan);
            if ($setting_lembur_karyawan) {
                $setting_lembur_karyawan->gaji = $request->gaji;
                $setting_lembur_karyawan->save();
            } else {
                $karyawan = Karyawan::find($request->karyawan_id);
                $posisi = $karyawan->posisi[0];

                if (!$karyawan) {
                    DB::rollback();
                    return response()->json(['message' => 'Karyawan tidak ditemukan!'], 402);
                }

                if (!$posisi) {
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
            'onoff_batas_approval_lembur' => ['nullable', 'in:Y'],
            'onoff_batas_pengajuan_lembur' => ['nullable', 'in:Y'],
            'batas_approval_lembur' => ['required', 'date_format:H:i'],
            'batas_pengajuan_lembur' => ['required', 'date_format:H:i'],
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
        $batas_approval_lembur = $request->batas_approval_lembur;
        $batas_pengajuan_lembur = $request->batas_pengajuan_lembur;
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
        $onoff_batas_approval_lembur = 'N';
        $onoff_batas_pengajuan_lembur = 'N';

        if (isset($request->onoff_batas_approval_lembur)) {
            $onoff_batas_approval_lembur = 'Y';
        }

        if (isset($request->onoff_batas_pengajuan_lembur)) {
            $onoff_batas_pengajuan_lembur = 'Y';
        }

        //Durasi Istirahat
        $durasi_istirahat_1 = intval(Carbon::parse($jam_istirahat_mulai_1)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_1)));
        $durasi_istirahat_2 = intval(Carbon::parse($jam_istirahat_mulai_2)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_2)));
        $durasi_istirahat_3 = intval(Carbon::parse($jam_istirahat_mulai_3)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_3)));
        $durasi_istirahat_jumat = intval(Carbon::parse($jam_istirahat_mulai_jumat)->diffInMinutes(Carbon::parse($jam_istirahat_selesai_jumat)));

        DB::beginTransaction();
        try {
            $setting_lembur = SettingLembur::where('organisasi_id', $organisasi_id)->get();
            if ($setting_lembur) {
                $settings = [
                    'batas_approval_lembur' => $batas_approval_lembur,
                    'batas_pengajuan_lembur' => $batas_pengajuan_lembur,
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
                    'onoff_batas_approval_lembur' => $onoff_batas_approval_lembur,
                    'onoff_batas_pengajuan_lembur' => $onoff_batas_pengajuan_lembur,
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

        $periode = $request->periode . '-01';

        DB::beginTransaction();
        try {
            $gaji_departemen_exist = GajiDepartemen::whereDate('periode', $periode)->where('organisasi_id', auth()->user()->organisasi_id)->exists();

            if ($gaji_departemen_exist) {
                DB::rollback();
                return response()->json(['message' => 'Gaji Departemen sudah ada, silahkan update nominalnya pada tabel!'], 402);
            }

            $departemens = Departemen::all();
            if ($departemens) {
                foreach ($departemens as $dept) {
                    GajiDepartemen::create([
                        'departemen_id' => $dept->id_departemen,
                        'organisasi_id' => auth()->user()->organisasi_id,
                        'periode' => $periode,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Gaji Departemen periode' . Carbon::parse($periode)->format('F Y') . ' Berhasil di Tambahkan!'], 200);
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
        try {
            $gaji_departemen = GajiDepartemen::find($request->id_gaji_departemen);
            $presentase = $request->presentase;
            $nominal_batas_lembur = intval($request->total_gaji * ($presentase / 100));
            if ($gaji_departemen) {
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
    public function get_data_karyawan_lembur(Request $request)
    {
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

        foreach ($posisi as $ps) {
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }
        array_push($id_posisi_members, auth()->user()->karyawan->posisi[0]->id_posisi);

        $isLeader = auth()->user()->karyawan->posisi[0]->jabatan_id == 5;
        $isAdminDept = auth()->user()->hasRole('admin-dept');

        if (!empty($search)) {
            if ($isLeader || $isAdminDept) {
                $query->where(function ($q) use ($isLeader, $isAdminDept, $organisasi_id, $id_posisi_members) {
                    if ($isLeader) {
                        $q->where('karyawans.organisasi_id', $organisasi_id)
                            ->whereIn('posisis.id_posisi', $id_posisi_members);
                    }
                    if ($isAdminDept) {
                        $deptIds = auth()->user()->karyawan->posisi->pluck('departemen_id')->filter()->toArray();
                        $q->whereIn('posisis.departemen_id', $deptIds);
                    }
                })
                    ->where(function ($dat) use ($search) {
                        $dat->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                            ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
                    });
            } else {
                $query->where('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
            }
        } else {
            if ($isLeader || $isAdminDept) {
                $query->where(function ($q) use ($isLeader, $isAdminDept, $organisasi_id, $id_posisi_members) {
                    if ($isLeader) {
                        $q->where('karyawans.organisasi_id', $organisasi_id)
                            ->whereIn('posisis.id_posisi', $id_posisi_members);
                    }
                    if ($isAdminDept) {
                        $deptIds = auth()->user()->karyawan->posisi->pluck('departemen_id')->filter()->toArray();
                        $q->whereIn('posisis.departemen_id', $deptIds);
                    }
                });
            } else {
                $query->where('karyawans.id_karyawan', auth()->user()->karyawan->id_karyawan);
            }
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')->whereNull('posisis.deleted_at')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        $query->groupBy('karyawans.id_karyawan', 'karyawans.nama', 'posisis.nama');

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

    public function get_data_karyawan_bypass_lembur(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');
        $issued_by = $request->input('issued_by');
        $issued = Karyawan::find($issued_by);

        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi',
        );

        $organisasi_id = $issued->user->organisasi_id;
        $posisi = $issued->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps) {
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }
        array_push($id_posisi_members, $posisi[0]->id_posisi);


        if (!empty($search)) {
            if ($posisi[0]->jabatan_id == 5) {
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
                $query->where('karyawans.id_karyawan', $issued->id_karyawan);
            }
        } else {
            if ($posisi[0]->jabatan_id == 5) {
                $query->where('users.organisasi_id', $organisasi_id);
                $query->whereIn('posisis.id_posisi', $id_posisi_members);
                $query->orWhere('karyawans.id_karyawan', $issued->id_karyawan);
            } else {
                $query->where('karyawans.id_karyawan', $issued->id_karyawan);
            }
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();
        $query->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
            ->leftJoin('users', 'karyawans.user_id', 'users.id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', 'setting_lembur_karyawans.karyawan_id');

        $query->groupBy('karyawans.id_karyawan', 'karyawans.nama', 'posisis.nama');

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

        foreach ($posisi as $ps) {
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

        $query->groupBy('karyawans.id_karyawan', 'karyawans.nama', 'posisis.nama', );
        $data = $query->get();

        $karyawanLembur = [];
        if ($data) {
            foreach ($data as $karyawan) {
                $karyawanLembur[] = [
                    'id' => $karyawan->id_karyawan,
                    'text' => $karyawan->nama
                ];
            }
        }
        ;

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

    function get_member_departemen($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_departemen($ps->children));
            }
            $data[] = $ps->departemen_id;
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
        if ($posisi) {
            foreach ($posisi as $pos) {
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if (!empty($parent_posisi_ids)) {
                    foreach ($parent_posisi_ids as $parent_id) {
                        if ($parent_id !== 0) {
                            if (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 3) {
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
        if ($posisi) {
            foreach ($posisi as $pos) {
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if (!empty($parent_posisi_ids)) {
                    foreach ($parent_posisi_ids as $parent_id) {
                        if ($parent_id !== 0) {
                            if (Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 4) {
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
            foreach ($lembur->detailLembur as $data) {
                //rencana
                $duration_rencana = $this->calculate_overtime_per_minutes($data->rencana_mulai_lembur, $data->rencana_selesai_lembur, $data->organisasi_id);
                $hour_rencana = floor($duration_rencana / 60);
                $minutes_rencana = $duration_rencana % 60;

                //aktual
                $duration_aktual = $this->calculate_overtime_per_minutes($data->aktual_mulai_lembur, $data->aktual_selesai_lembur, $data->organisasi_id);
                $hour_aktual = floor($duration_aktual / 60);
                $minutes_aktual = $duration_aktual % 60;

                //Can See Nominal
                $is_can_see_nominal = true;
                if (auth()->user()->hasRole('atasan')) {
                    if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 3) {
                        $is_can_see_nominal = true;
                    }
                } elseif (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
                    $is_can_see_nominal = true;
                }

                $data_detail_lembur[] = [
                    'id_detail_lembur' => $data->id_detail_lembur,
                    'lembur_id' => $data->lembur_id,
                    'nama' => $data->karyawan->nama,
                    'karyawan_id' => $data->karyawan_id,
                    'organisasi_id' => $data->organisasi_id,
                    'departemen_id' => $data->departemen_id,
                    'divisi_id' => $data->divisi_id,
                    'rencana_mulai_lembur' => $data->rencana_mulai_lembur ? Carbon::parse($data->rencana_mulai_lembur)->format('Y-m-d H:i') : null,
                    'rencana_selesai_lembur' => $data->rencana_selesai_lembur ? Carbon::parse($data->rencana_selesai_lembur)->format('Y-m-d H:i') : null,
                    'aktual_mulai_lembur' => $data->aktual_mulai_lembur ? Carbon::parse($data->aktual_mulai_lembur)->format('Y-m-d H:i') : null,
                    'aktual_selesai_lembur' => $data->aktual_selesai_lembur ? Carbon::parse($data->aktual_selesai_lembur)->format('Y-m-d H:i') : null,
                    'is_rencana_approved' => $data->is_rencana_approved,
                    'is_aktual_approved' => $data->is_aktual_approved,
                    'deskripsi_pekerjaan' => $data->deskripsi_pekerjaan,
                    'durasi_rencana' => $hour_rencana . ' jam  ' . $minutes_rencana . ' menit',
                    'durasi_aktual' => $hour_aktual . ' jam  ' . $minutes_aktual . ' menit',
                    'keterangan' => $data->keterangan,
                    'nominal' => $is_can_see_nominal ? 'Rp. ' . number_format($data->nominal, 0, ',', '.') : '-',
                    'rencana_last_changed_by' => $data->rencana_last_changed_by,
                    'rencana_last_changed_at' => $data->rencana_last_changed_at ? Carbon::parse($data->rencana_last_changed_at)->format('Y-m-d H:i') : null,
                    'aktual_last_changed_by' => $data->aktual_last_changed_by,
                    'aktual_last_changed_at' => $data->aktual_last_changed_at ? Carbon::parse($data->aktual_last_changed_at)->format('Y-m-d H:i') : null,
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
        try {
            $lembure = Lembure::find($id_lembur);
            $lembure->detailLembur()->delete();
            $lembure->delete();
            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur Dihapus!'], 200);
        } catch (Throwable $error) {
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function rollback(Request $request, string $id_lembur)
    {
        DB::beginTransaction();
        try {
            $lembure = Lembure::find($id_lembur);
            $lembure->actual_legalized_by = null;
            $lembure->actual_legalized_at = null;
            $lembure->save();
            DB::commit();
            return response()->json(['message' => 'Dokumen lembur dengan id ' . $id_lembur . ' berhasil di rollback'], 200);
        } catch (Throwable $error) {
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function checked(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'is_planned' => ['required', 'in:Y,N'],
            'mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:selesai_lembur.*'],
            'selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:mulai_lembur.*'],
        ];

        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 402);
        }

        $checked_detail = $request->input('checked_detail', $request->input('approved_detail'));
        $is_planned = $request->is_planned;
        $mulai_lemburs = $request->mulai_lembur;
        $selesai_lemburs = $request->selesai_lembur;
        $detail_ids = $request->id_detail_lembur;
        $keterangan = $request->keterangan ?? [];
        $changed_by = auth()->user()->karyawan->nama ?? auth()->user()->name;

        if (empty($checked_detail)) {
            return response()->json(['message' => 'Minimal ada 1 orang yang di-Checked!'], 403);
        }
        $checked_detail = is_array($checked_detail) ? $checked_detail : explode(',', $checked_detail);

        // pastikan semua tanggal mulai di hari yang sama
        $firstDate = Carbon::parse($mulai_lemburs[0])->format('Y-m-d');
        foreach ($mulai_lemburs as $dt) {
            if (Carbon::parse($dt)->format('Y-m-d') !== $firstDate) {
                return response()->json(['message' => 'Seluruh tanggal mulai lembur harus berada pada tanggal yang sama!'], 402);
            }
        }

        DB::beginTransaction();
        try {
            $lembur = Lembure::with(['issued.user', 'issued.posisi', 'detailLembur.karyawan.settingLembur'])
                ->where('id_lembur', $id_lembur)
                ->lockForUpdate()
                ->first();

            if (!$lembur) {
                DB::rollBack();
                return response()->json(['message' => 'Data lembur tidak ditemukan.'], 404);
            }

            $organisasi_id = $lembur->organisasi_id;
            $onoff_batas_approval = optional(SettingLembur::where('setting_name', 'onoff_batas_approval_lembur')->where('organisasi_id', $organisasi_id)->first())->value ?? 'Y';
            $jam_batas_approval = optional(SettingLembur::where('setting_name', 'batas_approval_lembur')->where('organisasi_id', $organisasi_id)->first())->value ?? '16:30';
            $batas_approval_datetime = Carbon::parse($firstDate . ' ' . $jam_batas_approval);
            if ($onoff_batas_approval === 'Y' && $batas_approval_datetime->isPast() && $is_planned === 'N') {
                DB::rollBack();
                return response()->json(['message' => 'Tidak bisa melakukan Plan Check karena sudah melewati batas waktu!'], 402);
            }

            // === PLAN CHECKED ===
            if ($is_planned === 'N') {
                if ($lembur->status === 'PLANNED' || $lembur->plan_legalized_by) {
                    DB::rollBack();
                    return response()->json(['message' => 'Proses Plan sudah selesai/terlegalisir.'], 400);
                }
                if ($lembur->plan_checked_by) {
                    DB::rollBack();
                    return response()->json(['message' => 'Pengajuan Lembur (Plan) sudah di-Checked.'], 409);
                }
                if ($lembur->status !== 'WAITING') {
                    DB::rollBack();
                    return response()->json(['message' => 'Dokumen bukan status WAITING.'], 422);
                }

                $creator = $lembur->issued;
                $creatorUser = $creator->user;
                $creatorIsAdmin = $creatorUser ? $creatorUser->hasRole('admin-dept') : false;
                $creatorPos = $creator->posisi;
                $hasLeader = \App\Helpers\Approval::HasLeader($creatorPos);
                $hasSectionHead = \App\Helpers\Approval::HasSectionHead($creatorPos);
                $hasDepartmentHead = \App\Helpers\Approval::HasDepartmentHead($creatorPos);
                $leaderKaryawan = \App\Helpers\Approval::GetLeader($creatorPos);
                $creatorJabatanId = $creatorPos[0]->jabatan_id ?? null;

                // expected checker role
                $expected = null;
                if ($creatorIsAdmin) {
                    if ($hasSectionHead && !$hasDepartmentHead) {
                        $expected = 'SEC_HEAD'; // NEW CASE
                    } elseif (!$hasLeader && !$hasSectionHead && !$hasDepartmentHead) {
                        $expected = 'ADMIN';
                    } elseif (!$hasLeader && !$hasSectionHead && $hasDepartmentHead) {
                        $expected = 'ADMIN';
                    } elseif ($hasLeader && !$hasSectionHead && $hasDepartmentHead) {
                        $expected = 'LEADER';
                    } elseif ($hasSectionHead && $hasDepartmentHead) {
                        $expected = 'SEC_HEAD';
                    }
                } elseif ($creatorJabatanId == 5) { // Leader
                    if ($hasSectionHead && !$hasDepartmentHead) {
                        $expected = 'SEC_HEAD'; // NEW CASE
                    } elseif (!$hasSectionHead && !$hasDepartmentHead) {
                        $expected = 'LEADER';
                    } elseif (!$hasSectionHead && $hasDepartmentHead) {
                        $expected = 'LEADER';
                    } elseif ($hasSectionHead && $hasDepartmentHead) {
                        $expected = 'SEC_HEAD';
                    }
                } elseif ($creatorJabatanId == 4) { // Sec.Head
                    if (!$hasDepartmentHead) {
                        $expected = 'SEC_HEAD'; // langsung auto semua oleh Sec.Head
                    } else {
                        $expected = 'SEC_HEAD';
                    }
                } elseif ($creatorJabatanId == 2) { // Dept.Head
                    $expected = 'DEPT_HEAD';
                }

                $actor = auth()->user();
                $actorKar = $actor->karyawan;
                $actorIsAdmin = $actor->hasRole('admin-dept');
                $sameScopeDH = function ($p) use ($lembur, $actor) {
                    $orgOk = (int) ($p->organisasi_id ?? $actor->organisasi_id) === (int) $lembur->organisasi_id;
                    $deptOk = is_null($p->departemen_id) || (int) $p->departemen_id === (int) $lembur->departemen_id;
                    return $orgOk && $deptOk;
                };
                $inSameDept = function ($p) use ($lembur, $actor) {
                    return (int) ($p->organisasi_id ?? $actor->organisasi_id) === (int) $lembur->organisasi_id
                        && (int) $p->departemen_id === (int) $lembur->departemen_id;
                };

                $actorIsLeader = $actorKar && $actorKar->posisi->contains(fn($p) => (int) $p->jabatan_id === 5 && $inSameDept($p));
                $actorIsSecHead = $actorKar && $actorKar->posisi->contains(fn($p) => (int) $p->jabatan_id === 4 && $inSameDept($p));
                $actorIsDeptHead = $actorKar && $actorKar->posisi->contains(fn($p) => (int) $p->jabatan_id === 2 && $sameScopeDH($p));

                $allowed = match ($expected) {
                    'ADMIN' => $actorIsAdmin,
                    'LEADER' => $actorIsLeader,
                    'SEC_HEAD' => $actorIsSecHead,
                    'DEPT_HEAD' => $actorIsDeptHead,
                    default => false,
                };

                if (!$allowed) {
                    DB::rollBack();
                    return response()->json(['message' => 'Anda tidak berhak melakukan aksi ini untuk dokumen ini.'], 403);
                }

                $planCheckedBy = match ($expected) {
                    'ADMIN' => $creator->nama,
                    'LEADER' => ($leaderKaryawan?->nama) ?? ($actorKar?->nama),
                    'SEC_HEAD' => $actorKar?->nama,
                    'DEPT_HEAD' => $actorKar?->nama,
                    default => $actorKar?->nama,
                };

                // === Update detail & total
                $total_durasi = 0;
                $total_nominal = 0;
                foreach ($detail_ids as $idx => $detailId) {
                    $detail = $lembur->detailLembur->firstWhere('id_detail_lembur', $detailId);
                    if (!$detail)
                        continue;

                    if (!in_array($detail->id_detail_lembur, $checked_detail)) {
                        $detail->is_rencana_approved = 'N';
                        $detail->rencana_last_changed_by = $changed_by;
                        $detail->rencana_last_changed_at = now();
                        $detail->save();
                        continue;
                    }

                    if ($detail->is_rencana_approved == 'Y') {
                        $jenis_hari = $lembur->jenis_hari === 'WEEKDAY' ? 'WD' : 'WE';
                        $startPlan = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$idx]);
                        $endPlan = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$idx]);
                        $durIst = $this->overtime_resttime_per_minutes($startPlan, $endPlan, $detail->organisasi_id);
                        $dur = $this->calculate_overtime_per_minutes($startPlan, $endPlan, $detail->organisasi_id);
                        if ($dur < 60) {
                            DB::rollBack();
                            return response()->json(['message' => 'Durasi lembur ' . $detail->karyawan->nama . ' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                        }
                        $changed = ($detail->rencana_mulai_lembur != $startPlan) || ($detail->rencana_selesai_lembur != $endPlan);
                        if ($changed) {
                            $durKonv = $this->calculate_durasi_konversi_lembur($jenis_hari, $dur, $detail->karyawan_id);
                            $uangMakan = $this->calculate_overtime_uang_makan($jenis_hari, $dur, $detail->karyawan_id);
                            $nominal = $this->calculate_overtime_nominal($jenis_hari, $dur, $detail->karyawan_id);
                            $detail->rencana_mulai_lembur = $startPlan;
                            $detail->rencana_selesai_lembur = $endPlan;
                            $detail->durasi_istirahat = $durIst;
                            $detail->durasi_konversi_lembur = $durKonv;
                            $detail->uang_makan = $uangMakan;
                            $detail->durasi = $dur;
                            $detail->nominal = $nominal;
                            $detail->rencana_last_changed_by = $changed_by;
                            $detail->rencana_last_changed_at = now();
                        } else {
                            $dur = $detail->durasi;
                            $nominal = $detail->nominal;
                        }
                        $detail->save();
                        $total_durasi += $dur;
                        $total_nominal += $nominal;
                    }
                }

                // === CASE: Sec.Head tapi tidak ada Dept.Head → auto approve/review/legalize
                if ($expected === 'SEC_HEAD' && !$hasDepartmentHead) {
                    // ambil nama BOD asli
                    $bod_name = \App\Helpers\Approval::GetDirector($creatorPos)
                        ?? $this->getDefaultBODName($lembur->departemen_id, $lembur->divisi_id, $lembur->organisasi_id)
                        ?? 'AUTO-SYSTEM-BOD';

                    $lembur->update([
                        'plan_checked_by' => $planCheckedBy,
                        'plan_checked_at' => now(),
                        'plan_approved_by' => $planCheckedBy,
                        'plan_approved_at' => now(),
                        'plan_reviewed_by' => $bod_name,
                        'plan_reviewed_at' => now(),
                        'plan_legalized_by' => 'HR & GA',
                        'plan_legalized_at' => now(),
                        'status' => 'PLANNED',
                        'total_durasi' => $total_durasi,
                        'total_nominal' => $total_nominal,
                    ]);
                } else {
                    $lembur->update([
                        'plan_checked_by' => $planCheckedBy,
                        'plan_checked_at' => now(),
                        'total_durasi' => $total_durasi,
                        'total_nominal' => $total_nominal,
                    ]);
                }

                DB::commit();
                return response()->json(['message' => 'Pengajuan Lembur (Plan) berhasil di-Checked!'], 200);
            }

            // === ACTUAL CHECKED ===
            if ($lembur->actual_checked_by) {
                DB::rollBack();
                return response()->json(['message' => 'Aktual Lembur sudah di-Checked.'], 409);
            }

            $total_durasi = 0;
            $total_nominal = 0;
            foreach ($detail_ids as $idx => $detailId) {
                $detail = $lembur->detailLembur->firstWhere('id_detail_lembur', $detailId);
                if (!$detail)
                    continue;

                if (!in_array($detail->id_detail_lembur, $checked_detail)) {
                    $detail->is_aktual_approved = 'N';
                    $detail->aktual_last_changed_by = $changed_by;
                    $detail->aktual_last_changed_at = now();
                    $detail->keterangan = $keterangan[$idx] ?? null;
                    $detail->save();
                    continue;
                }

                if ($detail->is_aktual_approved == 'Y') {
                    $jenis_hari = $lembur->jenis_hari === 'WEEKDAY' ? 'WD' : 'WE';
                    $startAct = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$idx]);
                    $endAct = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$idx]);
                    $durIst = $this->overtime_resttime_per_minutes($startAct, $endAct, $detail->organisasi_id);
                    $dur = $this->calculate_overtime_per_minutes($startAct, $endAct, $detail->organisasi_id);
                    if ($dur < 60) {
                        DB::rollBack();
                        return response()->json(['message' => 'Durasi lembur ' . $detail->karyawan->nama . ' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                    }
                    $changed = ($detail->aktual_mulai_lembur != $startAct) || ($detail->aktual_selesai_lembur != $endAct);
                    if ($changed) {
                        $durKonv = $this->calculate_durasi_konversi_lembur($jenis_hari, $dur, $detail->karyawan_id);
                        $uangMakan = $this->calculate_overtime_uang_makan($jenis_hari, $dur, $detail->karyawan_id);
                        $nominal = $this->calculate_overtime_nominal($jenis_hari, $dur, $detail->karyawan_id);
                        $detail->aktual_mulai_lembur = $startAct;
                        $detail->aktual_selesai_lembur = $endAct;
                        $detail->durasi_istirahat = $durIst;
                        $detail->durasi_konversi_lembur = $durKonv;
                        $detail->uang_makan = $uangMakan;
                        $detail->durasi = $dur;
                        $detail->nominal = $nominal;
                        $detail->aktual_last_changed_by = $changed_by;
                        $detail->aktual_last_changed_at = now();
                    } else {
                        $dur = $detail->durasi;
                        $nominal = $detail->nominal;
                    }
                    $total_durasi += $dur;
                    $total_nominal += $nominal;
                }
                $detail->keterangan = $keterangan[$idx] ?? null;
                $detail->save();
            }

            $lembur->update([
                'actual_checked_by' => auth()->user()->karyawan->nama ?? auth()->user()->name,
                'actual_checked_at' => now(),
                'total_durasi' => $total_durasi,
                'total_nominal' => $total_nominal,
            ]);

            DB::commit();
            return response()->json(['message' => 'Aktual Lembur berhasil di-Checked!'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in checked(): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat melakukan check: ' . $e->getMessage()], 500);
        }
    }

    public function approved(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'is_planned' => ['required', 'in:Y,N'],
            'mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:selesai_lembur.*'],
            'selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:mulai_lembur.*'],
        ];

        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 402);
        }

        $is_planned = $request->is_planned; // 'N' = Plan Approval, 'Y' = Actual Approval
        $approved_detail = $request->input('approved_detail', $request->input('checked_detail'));
        $mulai_lemburs = $request->mulai_lembur;
        $selesai_lemburs = $request->selesai_lembur;
        $detail_ids = $request->id_detail_lembur;
        $keterangan = $request->keterangan ?? [];
        $changed_by = auth()->user()->karyawan->nama ?? auth()->user()->name;

        if (empty($approved_detail)) {
            return response()->json(['message' => 'Minimal ada 1 orang yang di-Approved!'], 403);
        }
        $approved_detail = is_array($approved_detail) ? $approved_detail : explode(',', $approved_detail);

        // semua tanggal mulai harus sama
        $firstDate = Carbon::parse($mulai_lemburs[0])->format('Y-m-d');
        foreach ($mulai_lemburs as $dt) {
            if (Carbon::parse($dt)->format('Y-m-d') !== $firstDate) {
                return response()->json(['message' => 'Seluruh tanggal mulai lembur harus berada pada tanggal yang sama!'], 402);
            }
        }

        DB::beginTransaction();
        try {
            /** @var Lembure $lembur */
            $lembur = Lembure::with(['issued.posisi', 'detailLembur.karyawan.settingLembur'])
                ->where('id_lembur', $id_lembur)
                ->lockForUpdate()
                ->first();

            if (!$lembur) {
                DB::rollBack();
                return response()->json(['message' => 'Data lembur tidak ditemukan.'], 404);
            }

            $organisasi_id = $lembur->organisasi_id;
            $onoff_batas_approval = SettingLembur::where('setting_name', 'onoff_batas_approval_lembur')
                ->where('organisasi_id', $organisasi_id)
                ->value('value') ?? 'Y';
            $jam_batas_approval = SettingLembur::where('setting_name', 'batas_approval_lembur')
                ->where('organisasi_id', $organisasi_id)
                ->value('value') ?? '17:00';
            $batas_approval_time = Carbon::parse($firstDate . ' ' . $jam_batas_approval);

            // normalisasi jenis_hari (dukung legacy)
            $jenis_hari_raw = $lembur->jenis_hari;
            $jenis_hari_map = in_array($jenis_hari_raw, ['WD', 'WE'])
                ? $jenis_hari_raw
                : ($jenis_hari_raw === 'WEEKDAY' ? 'WD' : 'WE');

            // Closure validasi posisi Dept.Head (dipakai PLAN & ACTUAL)
            $matchesDeptDivOrg = function ($p, $lembur) {
                // Wajib DH
                if ((int) $p->jabatan_id !== 2) {
                    return false;
                }
                // Jika posisi mengikat ke departemen → harus match departemen
                if (!is_null($p->departemen_id)) {
                    return (int) $p->departemen_id === (int) $lembur->departemen_id
                        && (int) ($p->organisasi_id ?? auth()->user()->organisasi_id) === (int) $lembur->organisasi_id;
                }
                // Jika tidak mengikat departemen tapi mengikat divisi → harus match divisi
                if (!is_null($p->divisi_id)) {
                    return (int) $p->divisi_id === (int) $lembur->divisi_id
                        && (int) ($p->organisasi_id ?? auth()->user()->organisasi_id) === (int) $lembur->organisasi_id;
                }
                // Hanya level organisasi
                return (int) ($p->organisasi_id ?? auth()->user()->organisasi_id) === (int) $lembur->organisasi_id;
            };

            // =========================
            // ===== PLAN APPROVAL =====
            // =========================
            if ($is_planned === 'N') {

                if ($lembur->plan_approved_by) {
                    DB::rollBack();
                    return response()->json(['message' => 'Pengajuan Lembur (Plan) sudah di-Approved.'], 409);
                }
                if (!$lembur->plan_checked_by) {
                    DB::rollBack();
                    return response()->json(['message' => 'Pengajuan Lembur belum di-Checked oleh pihak berwenang.'], 403);
                }
                if ($onoff_batas_approval === 'Y' && $batas_approval_time->isPast()) {
                    DB::rollBack();
                    return response()->json(['message' => 'Tidak bisa melakukan approval karena sudah melewati batas waktu!'], 402);
                }

                // Jika departemen PEMBUAT tidak punya Dept.Head → approval seharusnya auto saat store
                $issuedPos = $lembur->issued->posisi ?? collect();
                $hasDepartmentHead = \App\Helpers\Approval::HasDepartmentHead($issuedPos);
                if (!$hasDepartmentHead) {
                    $hasDepartmentHead = \App\Models\Posisi::where('organisasi_id', $lembur->organisasi_id)
                        ->where('jabatan_id', 2) // Dept.Head
                        ->where(function ($q) use ($lembur) {
                            if (!is_null($lembur->departemen_id)) {
                                $q->orWhere('departemen_id', $lembur->departemen_id);
                            }
                            if (!is_null($lembur->divisi_id)) {
                                $q->orWhere('divisi_id', $lembur->divisi_id);
                            }
                        })
                        ->exists();
                }

                if (!$hasDepartmentHead) {
                    if ($lembur->status === 'PLANNED' || $lembur->plan_legalized_by) {
                        DB::rollBack();
                        return response()->json(['message' => 'Plan Approved tidak dapat diproses manual karena Dept.Head tidak tersedia (sudah otomatis saat pengajuan).'], 400);
                    }
                    DB::rollBack();
                    return response()->json(['message' => 'Plan Approved tidak dapat diproses manual karena Dept.Head tidak tersedia.'], 400);
                }

                // Validasi: aktor harus Dept.Head pada scope yang sama
                $actor = auth()->user();
                $actorKar = $actor->karyawan;
                $actorIsDeptHead = $actorKar && $actorKar->posisi->contains(function ($p) use ($lembur, $matchesDeptDivOrg) {
                    return $matchesDeptDivOrg($p, $lembur);
                });

                if (!$actorIsDeptHead) {
                    DB::rollBack();
                    return response()->json(['message' => 'Plan Approved harus dilakukan oleh Department Head departemen pembuat.'], 403);
                }

                // === proses detail + hitung total hanya untuk yang disetujui ===
                $total_durasi = 0;
                $total_nominal = 0;

                foreach ($detail_ids as $idx => $detailId) {
                    /** @var DetailLembur|null $detail */
                    $detail = $lembur->detailLembur->firstWhere('id_detail_lembur', $detailId);
                    if (!$detail) {
                        continue;
                    }

                    if (!in_array($detail->id_detail_lembur, $approved_detail)) {
                        $detail->is_rencana_approved = 'N';
                        $detail->rencana_last_changed_by = $changed_by;
                        $detail->rencana_last_changed_at = now();
                        $detail->save();
                        continue;
                    }

                    // pastikan flag Y untuk yang di-approve
                    $detail->is_rencana_approved = 'Y';

                    $startPlan = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$idx]);
                    $endPlan = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$idx]);
                    $durIst = $this->overtime_resttime_per_minutes($startPlan, $endPlan, $detail->organisasi_id);
                    $dur = $this->calculate_overtime_per_minutes($startPlan, $endPlan, $detail->organisasi_id);

                    if ($dur < 60) {
                        DB::rollBack();
                        return response()->json(['message' => 'Durasi lembur ' . $detail->karyawan->nama . ' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                    }

                    $changed = ($detail->rencana_mulai_lembur != $startPlan) || ($detail->rencana_selesai_lembur != $endPlan);
                    if ($changed) {
                        $durKonv = $this->calculate_durasi_konversi_lembur($jenis_hari_map, $dur, $detail->karyawan_id);
                        $uangMakan = $this->calculate_overtime_uang_makan($jenis_hari_map, $dur, $detail->karyawan_id);
                        $nominal = $this->calculate_overtime_nominal($jenis_hari_map, $dur, $detail->karyawan_id);

                        $detail->rencana_mulai_lembur = $startPlan;
                        $detail->rencana_selesai_lembur = $endPlan;
                        $detail->durasi_istirahat = $durIst;
                        $detail->durasi_konversi_lembur = $durKonv;
                        $detail->uang_makan = $uangMakan;
                        $detail->durasi = $dur;
                        $detail->nominal = $nominal;

                        $detail->rencana_last_changed_by = $changed_by;
                        $detail->rencana_last_changed_at = now();
                    } else {
                        // gunakan nilai yang sudah ada
                        $dur = $detail->durasi;
                        $nominal = $detail->nominal;
                    }

                    $detail->save();
                    $total_durasi += $dur;
                    $total_nominal += $nominal;
                }

                // set approved by Dept.Head
                $actorName = auth()->user()->karyawan->nama ?? auth()->user()->name;
                $lembur->update([
                    'plan_approved_by' => $actorName,
                    'plan_approved_at' => now(),
                    'total_durasi' => $total_durasi,
                    'total_nominal' => $total_nominal,
                ]);

                // auto sesudah Dept.Head approve: reviewed (BOD) + legalized (HR & GA) + status PLANNED
                $bod_name = \App\Helpers\Approval::GetDirector($issuedPos)
                    ?? $this->getDefaultBODName($lembur->departemen_id, $lembur->divisi_id, $lembur->organisasi_id)
                    ?? 'AUTO-SYSTEM-BOD';
                $lembur->update([
                    'plan_reviewed_by' => $bod_name,
                    'plan_reviewed_at' => now(),
                    'plan_legalized_by' => 'HR & GA',
                    'plan_legalized_at' => now(),
                    'status' => 'PLANNED',
                ]);

                DB::commit();
                return response()->json(['message' => 'Pengajuan Lembur (Plan) berhasil di-Approved, lalu otomatis di-Reviewed & Legalized.'], 200);
            }

            // ==========================
            // ===== ACTUAL APPROVAL ====
            // ==========================
            if ($lembur->actual_approved_by) {
                DB::rollBack();
                return response()->json(['message' => 'Aktual Lembur sudah di-Approved.'], 409);
            }
            if (!$lembur->actual_checked_by) {
                DB::rollBack();
                return response()->json(['message' => 'Aktual Lembur belum di-Checked.'], 403);
            }
            // Wajib: Plan sudah dilegalisir & LKH sudah diupload
            if (empty($lembur->plan_legalized_by)) {
                DB::rollBack();
                return response()->json(['message' => 'Pengajuan Lembur (Plan) belum di-Legalized oleh HR & GA.'], 403);
            }
            if (empty($lembur->attachmentLembur)) {
                \Log::warning('Lembur done tanpa LKH', ['id_lembur' => $lembur->id_lembur]);
                // tidak return, tetap lanjut proses
            }

            // Cek keberadaan Dept.Head pada departemen pembuat
            $issuedPos = $lembur->issued->posisi ?? collect();
            $hasDepartmentHead = \App\Helpers\Approval::HasDepartmentHead($issuedPos);
            // fallback eksplisit ke tabel posisi (jabatan_id = 2 = Dept.Head) — fleksibel (departemen ATAU divisi)
            if (!$hasDepartmentHead) {
                $hasDepartmentHead = \App\Models\Posisi::where('organisasi_id', $lembur->organisasi_id)
                    ->where('jabatan_id', 2) // Dept.Head
                    ->where(function ($q) use ($lembur) {
                        if (!is_null($lembur->departemen_id)) {
                            $q->orWhere('departemen_id', $lembur->departemen_id);
                        }
                        if (!is_null($lembur->divisi_id)) {
                            $q->orWhere('divisi_id', $lembur->divisi_id);
                        }
                    })
                    ->exists();
            }

            // Jika TIDAK ada Dept.Head → approval aktual harusnya AUTO di `done()`, tolak manual
            if (!$hasDepartmentHead) {
                DB::rollBack();
                return response()->json(['message' => 'Actual Approved tidak dapat diproses manual karena Dept.Head tidak tersedia (sudah otomatis di tahap Done).'], 400);
            }

            // Validasi AKTOR: harus Dept.Head pada scope yang sama (mengikuti aturan fleksibel di atas)
            $actor = auth()->user();
            $actorKar = $actor->karyawan;
            $actorIsDeptHead = $actorKar
                && $actorKar->posisi->contains(fn($p) => $matchesDeptDivOrg($p, $lembur));

            if (!$actorIsDeptHead) {
                DB::rollBack();
                return response()->json(['message' => 'Actual Approved harus dilakukan oleh Department Head departemen pembuat.'], 403);
            }

            // Normalisasi jenis_hari untuk perhitungan (dukung legacy 'WEEKDAY'/'WEEKEND')
            $jenis_hari_raw = $lembur->jenis_hari;
            $jenis_hari_map = in_array($jenis_hari_raw, ['WD', 'WE'])
                ? $jenis_hari_raw
                : ($jenis_hari_raw === 'WEEKDAY' ? 'WD' : 'WE');

            $total_durasi = 0;
            $total_nominal = 0;

            foreach ($detail_ids as $idx => $detailId) {
                /** @var DetailLembur|null $detail */
                $detail = $lembur->detailLembur->firstWhere('id_detail_lembur', $detailId);
                if (!$detail) {
                    continue;
                }

                if (!in_array($detail->id_detail_lembur, $approved_detail)) {
                    $detail->is_aktual_approved = 'N';
                    $detail->aktual_last_changed_by = $changed_by;
                    $detail->aktual_last_changed_at = now();
                    $detail->keterangan = $keterangan[$idx] ?? null;
                    $detail->save();
                    continue;
                }

                // pastikan flag Y untuk yang di-approve aktual
                $detail->is_aktual_approved = 'Y';

                $startAct = $this->pembulatan_menit_ke_bawah($mulai_lemburs[$idx]);
                $endAct = $this->pembulatan_menit_ke_bawah($selesai_lemburs[$idx]);
                $durIst = $this->overtime_resttime_per_minutes($startAct, $endAct, $detail->organisasi_id);
                $dur = $this->calculate_overtime_per_minutes($startAct, $endAct, $detail->organisasi_id);

                if ($dur < 60) {
                    DB::rollBack();
                    return response()->json(['message' => 'Durasi lembur ' . $detail->karyawan->nama . ' kurang dari 1 jam, tidak perlu dimasukkan ke SPL'], 402);
                }

                $changed = ($detail->aktual_mulai_lembur != $startAct) || ($detail->aktual_selesai_lembur != $endAct);
                if ($changed) {
                    $durKonv = $this->calculate_durasi_konversi_lembur($jenis_hari_map, $dur, $detail->karyawan_id);
                    $uangMakan = $this->calculate_overtime_uang_makan($jenis_hari_map, $dur, $detail->karyawan_id);
                    $nominal = $this->calculate_overtime_nominal($jenis_hari_map, $dur, $detail->karyawan_id);

                    $detail->aktual_mulai_lembur = $startAct;
                    $detail->aktual_selesai_lembur = $endAct;
                    $detail->durasi_istirahat = $durIst;
                    $detail->durasi_konversi_lembur = $durKonv;
                    $detail->uang_makan = $uangMakan;
                    $detail->durasi = $dur;
                    $detail->nominal = $nominal;

                    $detail->aktual_last_changed_by = $changed_by;
                    $detail->aktual_last_changed_at = now();
                } else {
                    // pakai nilai yang sudah ada
                    $dur = $detail->durasi;
                    $nominal = $detail->nominal;
                }

                $detail->keterangan = $keterangan[$idx] ?? null;
                $detail->save();

                $total_durasi += $dur;
                $total_nominal += $nominal;
            }

            $actorName = $actorKar->nama ?? $actor->name;
            $lembur->update([
                'actual_approved_by' => $actorName,
                'actual_approved_at' => now(),
                'total_durasi' => $total_durasi,
                'total_nominal' => $total_nominal,
            ]);

            DB::commit();
            return response()->json(['message' => 'Aktual Lembur berhasil di-Approved!'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in approved(): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat melakukan approval: ' . $e->getMessage()], 500);
        }
    }

    public function legalized(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'is_planned' => ['required', 'in:Y,N'],
            'mulai_lembur.*' => ['nullable', 'date_format:Y-m-d\TH:i', 'before:selesai_lembur.*'],
            'selesai_lembur.*' => ['nullable', 'date_format:Y-m-d\TH:i', 'after:mulai_lembur.*'],
            'approved_detail' => ['nullable', 'string'],
            'id_detail_lembur' => ['required', 'array'],
        ];
        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 402);
        }

        // Role guard: hanya personalia
        if (!auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
            return response()->json(['message' => 'Hanya role Personalia yang dapat melakukan Legalisasi Lembur.'], 403);
        }

        // ❗ Ambil apa adanya ('Y' untuk ACTUAL, 'N' untuk PLAN)
        $isPlanned = $request->input('is_planned');

        // --- daftar detail yang dipilih (dengan fallback 'checked_detail' / 'legalized_detail')
        $approvedDetailStr = $request->input('approved_detail');
        if (empty($approvedDetailStr)) {
            $approvedDetailStr = $request->input('legalized_detail');
        }
        if (empty($approvedDetailStr)) {
            $approvedDetailStr = $request->input('checked_detail'); // fallback
        }
        $selectedDetailIds = $request->input('id_detail_lembur', []);
        $mulaiArr = $request->input('mulai_lembur', []);
        $selesaiArr = $request->input('selesai_lembur', []);
        $jenisHariApproval = $request->input('jenis_hariApproval');
        $jenisHariAktual = $request->input('jenis_hariAktual');

        // Normalisasi CSV → array integer
        $approvedDetailIds = [];
        if (!empty($approvedDetailStr)) {
            $approvedDetailIds = collect(preg_split('/\s*,\s*/', $approvedDetailStr))
                ->filter()->map(fn($v) => (int) $v)->values()->all();
        }

        DB::beginTransaction();
        try {
            /** @var \App\Models\Lembure $lembur */
            $lembur = Lembure::with('detailLembur.karyawan.settingLembur')->find($id_lembur);
            if (!$lembur) {
                DB::rollBack();
                return response()->json(['message' => 'Data lembur tidak ditemukan.'], 404);
            }

            // ---------- PLAN LEGALIZED ----------
            if ($isPlanned === 'N') {
                if ($lembur->plan_legalized_by !== null) {
                    DB::rollBack();
                    return response()->json(['message' => 'Pengajuan Lembur (Plan) sudah di-legalisir.'], 403);
                }
                if (empty($lembur->plan_checked_by) || empty($lembur->plan_approved_by) || empty($lembur->plan_reviewed_by)) {
                    DB::rollBack();
                    return response()->json(['message' => 'Plan belum lengkap (Checked/Approved/Reviewed).'], 409);
                }

                // Jika user tidak mengirim daftar, gunakan semua PLAN yang sudah Y
                if (empty($approvedDetailIds)) {
                    $approvedDetailIds = $lembur->detailLembur
                        ->where('is_rencana_approved', 'Y')
                        ->pluck('id_detail_lembur')->map(fn($v) => (int) $v)->all();
                }
                if (empty($approvedDetailIds)) {
                    DB::rollBack();
                    return response()->json(['message' => 'Minimal ada 1 detail yang dilegalisir.'], 403);
                }
                $approvedSet = array_flip($approvedDetailIds);

                $totalDur = 0;
                $totalNom = 0;

                foreach ($lembur->detailLembur as $idx => $detail) {
                    // hanya proses detail yang dipilih & memang plan-nya disetujui
                    if (!isset($approvedSet[(int) $detail->id_detail_lembur]) || $detail->is_rencana_approved !== 'Y') {
                        continue; // jangan menurunkan status apa pun
                    }

                    if (!empty($mulaiArr) && isset($mulaiArr[$idx], $selesaiArr[$idx])) {
                        $jenis = $jenisHariApproval ?? ($lembur->jenis_hari === 'WEEKDAY' ? 'WD' : 'WE');

                        $start = $this->pembulatan_menit_ke_bawah($mulaiArr[$idx]);
                        $end = $this->pembulatan_menit_ke_bawah($selesaiArr[$idx]);

                        $durIst = $this->overtime_resttime_per_minutes($start, $end, $detail->organisasi_id);
                        $dur = $this->calculate_overtime_per_minutes($start, $end, $detail->organisasi_id);
                        if ($dur < 60) {
                            DB::rollBack();
                            return response()->json(['message' => 'Durasi lembur ' . ($detail->karyawan->nama ?? '-') . ' kurang dari 1 jam.'], 402);
                        }

                        $durKonv = $this->calculate_durasi_konversi_lembur($jenis, $dur, $detail->karyawan_id);
                        $uangMkn = $this->calculate_overtime_uang_makan($jenis, $dur, $detail->karyawan_id);
                        $nominal = $this->calculate_overtime_nominal($jenis, $dur, $detail->karyawan_id);
                        $gaji = optional($detail->karyawan->settingLembur)->gaji ?? 0;
                        $pembagi = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')
                            ->where('organisasi_id', $detail->organisasi_id)->value('value');

                        $detail->rencana_mulai_lembur = $start;
                        $detail->rencana_selesai_lembur = $end;
                        $detail->durasi_istirahat = $durIst;
                        $detail->durasi_konversi_lembur = $durKonv;
                        $detail->uang_makan = $uangMkn;
                        $detail->gaji_lembur = $gaji;
                        $detail->pembagi_upah_lembur = $pembagi;
                        $detail->durasi = $dur;
                        $detail->nominal = $nominal;
                        $detail->rencana_last_changed_by = 'HRD & GA';
                        $detail->rencana_last_changed_at = now();
                        $detail->save();
                    }

                    $totalDur += (int) ($detail->durasi ?? 0);
                    $totalNom += (float) ($detail->nominal ?? 0);
                }

                if ($totalDur === 0 && $totalNom === 0) {
                    $agg = DetailLembur::where('lembur_id', $lembur->id_lembur)
                        ->where('is_rencana_approved', 'Y')
                        ->selectRaw('COALESCE(SUM(durasi),0) as d, COALESCE(SUM(nominal),0) as n')->first();
                    $totalDur = (int) ($agg->d ?? 0);
                    $totalNom = (float) ($agg->n ?? 0);
                }

                Lembure::where('id_lembur', $lembur->id_lembur)
                    ->whereNull('plan_legalized_by')
                    ->update([
                        'status' => 'PLANNED',
                        'jenis_hari' => $jenisHariApproval ?: $lembur->jenis_hari,
                        'plan_legalized_by' => 'HR & GA',
                        'plan_legalized_at' => now(),
                        'total_durasi' => $totalDur,
                        'total_nominal' => $totalNom,
                    ]);

                DB::commit();
                return response()->json(['message' => 'Pengajuan Lembur (Plan) berhasil di-legalisir.'], 200);
            }

            // ---------- ACTUAL LEGALIZED ----------
            if ($lembur->actual_legalized_by !== null) {
                DB::rollBack();
                return response()->json(['message' => 'Aktual Lembur sudah di-legalisir.'], 403);
            }
            if (empty($lembur->actual_checked_by) || empty($lembur->actual_approved_by) || empty($lembur->actual_reviewed_by)) {
                DB::rollBack();
                return response()->json(['message' => 'Actual belum lengkap (Checked/Approved/Reviewed).'], 409);
            }
            if (empty($lembur->attachmentLembur)) {
                \Log::warning('Lembur done tanpa LKH', ['id_lembur' => $lembur->id_lembur]);
                // tidak return, tetap lanjut proses
            }
            // Jika user tidak mengirim daftar, gunakan semua ACTUAL yang sudah Y
            if (empty($approvedDetailIds)) {
                $approvedDetailIds = $lembur->detailLembur
                    ->where('is_aktual_approved', 'Y')
                    ->pluck('id_detail_lembur')->map(fn($v) => (int) $v)->all();
            }
            if (empty($approvedDetailIds)) {
                DB::rollBack();
                return response()->json(['message' => 'Minimal ada 1 detail yang dilegalisir.'], 403);
            }
            $approvedSet = array_flip($approvedDetailIds);

            $mulaiArr = $request->input('aktual_mulai_lembur', $mulaiArr);
            $selesaiArr = $request->input('aktual_selesai_lembur', $selesaiArr);
            $jenisDefault = in_array($lembur->jenis_hari, ['WD', 'WE'])
                ? $lembur->jenis_hari
                : ($lembur->jenis_hari === 'WEEKDAY' ? 'WD' : 'WE');

            $changedBy = auth()->user()->karyawan->nama ?? auth()->user()->name;

            $totalDur = 0;
            $totalNom = 0;
            $affectedDates = collect();

            foreach ($lembur->detailLembur as $idx => $detail) {
                // proses hanya yang dipilih untuk dilegalisir ATAU yang memang sudah Y
                $included = isset($approvedSet[(int) $detail->id_detail_lembur]) || $detail->is_aktual_approved === 'Y';
                if (!$included) {
                    continue; // jangan turunkan status ke 'N'
                }

                if (!empty($mulaiArr) && array_key_exists($idx, $mulaiArr) && array_key_exists($idx, $selesaiArr)) {
                    $jenis = $jenisHariAktual ?: $jenisDefault;
                    $startAct = $this->pembulatan_menit_ke_bawah($mulaiArr[$idx]);
                    $endAct = $this->pembulatan_menit_ke_bawah($selesaiArr[$idx]);

                    $durIst = $this->overtime_resttime_per_minutes($startAct, $endAct, $detail->organisasi_id);
                    $dur = $this->calculate_overtime_per_minutes($startAct, $endAct, $detail->organisasi_id);
                    if ($dur < 60) {
                        DB::rollBack();
                        return response()->json(['message' => 'Durasi lembur ' . ($detail->karyawan->nama ?? '-') . ' kurang dari 1 jam, tidak bisa dilegalisir.'], 402);
                    }

                    $durKonv = $this->calculate_durasi_konversi_lembur($jenis, $dur, $detail->karyawan_id);
                    $uangMkn = $this->calculate_overtime_uang_makan($jenis, $dur, $detail->karyawan_id);
                    $nominal = $this->calculate_overtime_nominal($jenis, $dur, $detail->karyawan_id);
                    $gaji = optional($detail->karyawan->settingLembur)->gaji;
                    $pembagi = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')
                        ->where('organisasi_id', $detail->organisasi_id)->value('value');

                    $detail->aktual_mulai_lembur = $startAct;
                    $detail->aktual_selesai_lembur = $endAct;
                    $detail->durasi_istirahat = $durIst;
                    $detail->durasi_konversi_lembur = $durKonv;
                    $detail->uang_makan = $uangMkn;
                    $detail->gaji_lembur = $gaji;
                    $detail->pembagi_upah_lembur = $pembagi;
                    $detail->durasi = $dur;
                    $detail->nominal = $nominal;

                    $detail->aktual_last_changed_by = $changedBy;
                    $detail->aktual_last_changed_at = now();
                    $detail->save();
                }

                $totalDur += (int) ($detail->durasi ?? 0);
                $totalNom += (float) ($detail->nominal ?? 0);

                if ($detail->aktual_mulai_lembur) {
                    $affectedDates->push(\Carbon\Carbon::parse($detail->aktual_mulai_lembur)->toDateString());
                }
            }

            if ($totalDur === 0 && $totalNom === 0) {
                $agg = DetailLembur::where('lembur_id', $lembur->id_lembur)
                    ->where('is_aktual_approved', 'Y')
                    ->selectRaw('COALESCE(SUM(durasi),0) as d, COALESCE(SUM(nominal),0) as n')->first();
                $totalDur = (int) ($agg->d ?? 0);
                $totalNom = (float) ($agg->n ?? 0);
            }

            Lembure::where('id_lembur', $lembur->id_lembur)
                ->whereNull('actual_legalized_by')
                ->update([
                    'status' => 'COMPLETED',
                    'jenis_hari' => $jenisHariAktual ?: $lembur->jenis_hari,
                    'actual_legalized_by' => 'HR & GA',
                    'actual_legalized_at' => now(),
                    'total_durasi' => $totalDur,
                    'total_nominal' => $totalNom,
                ]);

            // ===== Rekap harian (tanpa UNIQUE index): updateOrInsert per baris =====
            $affectedDates = $affectedDates->unique()->values();
            foreach ($affectedDates as $tgl) {
                $rows = DetailLembur::query()
                    ->selectRaw("
                    detail_lemburs.organisasi_id,
                    detail_lemburs.departemen_id,
                    detail_lemburs.divisi_id,
                    DATE(detail_lemburs.aktual_mulai_lembur) AS tanggal_lembur,
                    COALESCE(SUM(detail_lemburs.nominal),0) AS total_nominal_lembur,
                    COALESCE(SUM(detail_lemburs.durasi),0)  AS total_durasi_lembur
                ")
                    ->leftJoin('lemburs', 'lemburs.id_lembur', '=', 'detail_lemburs.lembur_id')
                    ->whereDate('detail_lemburs.aktual_mulai_lembur', $tgl)
                    ->where('lemburs.status', 'COMPLETED')
                    ->whereNotNull('lemburs.actual_legalized_by')
                    ->groupByRaw('detail_lemburs.organisasi_id, detail_lemburs.departemen_id, detail_lemburs.divisi_id, DATE(detail_lemburs.aktual_mulai_lembur)')
                    ->get();

                if ($rows->isEmpty())
                    continue;

                foreach ($rows as $r) {
                    DB::table('lembur_harians')->updateOrInsert(
                        [
                            'organisasi_id' => $r->organisasi_id,
                            'departemen_id' => $r->departemen_id,
                            'divisi_id' => $r->divisi_id,
                            'tanggal_lembur' => $r->tanggal_lembur,
                        ],
                        [
                            'total_nominal_lembur' => $r->total_nominal_lembur,
                            'total_durasi_lembur' => $r->total_durasi_lembur,
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json(['message' => 'Aktual Lembur berhasil di-legalisir.'], 200);

        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 402);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in legalized(): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat legalisasi: ' . $e->getMessage()], 500);
        }
    }


    public function reviewed(Request $request)
    {
        $dataValidate = [
            'data' => ['required'], // string "tgl|dept|div|org|PLANNING/ACTUAL,..." atau array item serupa
        ];

        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 402);
        }

        // --- VALIDASI ROLE: hanya Director (jabatan_id == 1) ---
        $user = auth()->user();
        $karyawan = $user->karyawan;
        $isDirector = $karyawan && $karyawan->posisi->contains(fn($p) => (int) $p->jabatan_id === 1);

        if (!$isDirector) {
            return response()->json(['message' => 'Hanya BOD (Director) yang dapat melakukan Review Lembur.'], 403);
        }

        // Normalisasi payload "data"
        $rawItems = $request->input('data');
        $items = is_array($rawItems) ? $rawItems : explode(',', (string) $rawItems);

        DB::beginTransaction();
        try {
            $reviewedCount = 0;
            $skippedPlan = 0;

            foreach ($items as $row) {
                // format: "YYYY-mm-dd|departemen_id|null|organisasi_id|PLANNING/ACTUAL"
                $parts = is_array($row) ? $row : explode('|', $row);
                $parts = array_map(fn($v) => is_string($v) ? trim($v) : $v, $parts);

                if (count($parts) < 5) {
                    \Log::warning('Invalid reviewed item format', ['item' => $row]);
                    continue;
                }

                $tanggal_lembur = $parts[0];
                $departemen_id = ($parts[1] !== 'null' && $parts[1] !== '') ? $parts[1] : null;
                $divisi_id = ($parts[2] !== 'null' && $parts[2] !== '') ? $parts[2] : null;
                $organisasi_id = ($parts[3] !== 'null' && $parts[3] !== '') ? $parts[3] : null;
                $statusReview = strtoupper($parts[4]); // 'PLANNING' atau 'ACTUAL'

                // PLANNING review: sudah otomatis -> skip
                if ($statusReview === 'PLANNING') {
                    $skippedPlan++;
                    continue;
                }

                // ===== ACTUAL REVIEW MANUAL =====
                $actor = auth()->user();
                $actorKar = $actor->karyawan;

                // Validasi role & scope BOD (Director) pembuat dokumen.
                // WILDCARD: jika posisi BOD tidak mengikat org/div/dept (nilai null), dianggap sah untuk semua pada level itu.
                $actorIsBODScope = $actorKar && $actorKar->posisi->contains(function ($p) use ($organisasi_id, $divisi_id, $departemen_id) {
                    if ((int) $p->jabatan_id !== 1)
                        return false; // wajib BOD
                    if (!is_null($organisasi_id) && !is_null($p->organisasi_id) && (int) $p->organisasi_id !== (int) $organisasi_id)
                        return false;
                    if (!is_null($divisi_id) && !is_null($p->divisi_id) && (int) $p->divisi_id !== (int) $divisi_id)
                        return false;
                    if (!is_null($departemen_id) && !is_null($p->departemen_id) && (int) $p->departemen_id !== (int) $departemen_id)
                        return false;
                    return true;
                });

                if (!$actorIsBODScope) {
                    DB::rollBack();
                    return response()->json(['message' => 'Actual Review hanya dapat dilakukan oleh BOD yang membawahi departemen/divisi tersebut.'], 403);
                }

                // Kriteria: status COMPLETED, sudah actual_approved_by, belum actual_reviewed_by & belum actual_legalized_by
                $q = Lembure::query()
                    ->select('lemburs.id_lembur')
                    ->leftJoin('detail_lemburs', 'lemburs.id_lembur', '=', 'detail_lemburs.lembur_id')
                    ->whereDate('detail_lemburs.aktual_mulai_lembur', $tanggal_lembur) // pakai AKTUAL
                    ->where('lemburs.status', 'COMPLETED')
                    ->whereNotNull('lemburs.actual_approved_by')
                    ->whereNull('lemburs.actual_reviewed_by')
                    ->whereNull('lemburs.actual_legalized_by'); // opsional, idempoten

                if (!is_null($departemen_id))
                    $q->where('detail_lemburs.departemen_id', $departemen_id);
                if (!is_null($divisi_id))
                    $q->where('detail_lemburs.divisi_id', $divisi_id);
                if (!is_null($organisasi_id))
                    $q->where('detail_lemburs.organisasi_id', $organisasi_id);

                // Hindari duplikasi karena join
                $ids = $q->distinct()->pluck('lemburs.id_lembur');

                if ($ids->isEmpty()) {
                    continue;
                }

                // Idempotent update (tetap whereNull)
                Lembure::whereIn('id_lembur', $ids)
                    ->whereNull('actual_reviewed_by')
                    ->update([
                        'actual_reviewed_by' => ($karyawan->nama ?? $user->name),
                        'actual_reviewed_at' => now(),
                    ]);

                $reviewedCount += $ids->count();
            }

            DB::commit();

            // Pesan ringkas + informatif
            $msg = "{$reviewedCount} dokumen lembur Aktual selesai direview";
            if ($skippedPlan > 0) {
                $msg .= " ({$skippedPlan} item PLANNING di-skip karena review Plan berjalan otomatis).";
            }

            return response()->json(['message' => $msg], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in reviewed(): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat melakukan review: ' . $e->getMessage()], 500);
        }
    }

    public function review_lembur_rejected(Request $request, string $idDetailLembur)
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
        try {
            $rejected_by = auth()->user()->karyawan->nama;
            $detail = DetailLembur::find($idDetailLembur);
            $detailCount = DetailLembur::where('lembur_id', $detail->lembur_id)->where('is_rencana_approved', 'Y')->count();

            $detail->rencana_last_changed_by = $rejected_by;
            $detail->rencana_last_changed_at = now();
            $detail->is_rencana_approved = 'N';
            $detail->is_aktual_approved = 'N';
            $detail->save();

            $lembur = $detail->lembur;
            if ($detailCount <= 1) {
                $lembur->status = 'REJECTED';
                $lembur->rejected_by = $rejected_by;
                $lembur->rejected_note = $rejected_note;
                $lembur->rejected_at = now();
                $lembur->save();
            }

            DB::commit();
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Rejected!'], 200);
        } catch (Throwable $e) {
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
        try {
            $lembur = Lembure::find($id_lembur);
            if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
                $rejected_by = 'HRD & GA';
            } else {
                $rejected_by = auth()->user()->karyawan->nama;
            }

            $detail_lembur = $lembur->detailLembur;
            foreach ($detail_lembur as $detail) {
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
            return response()->json(['message' => 'Pengajuan Lembur berhasil di Rejected!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function done(Request $request, string $id_lembur)
    {
        $dataValidate = [
            'aktual_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:aktual_selesai_lembur.*'],
            'aktual_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:aktual_mulai_lembur.*'],
            'id_detail_lembur.*' => ['required'],
            // is_aktual_approved: CSV berisi id_detail yang diikutkan aktual
        ];

        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 402);
        }

        $selectedIdsCsv = $request->is_aktual_approved;                 // csv
        $selectedDetailIds = $selectedIdsCsv ? preg_split('/\s*,\s*/', $selectedIdsCsv) : [];
        // 🔧 Normalisasi ke string & buat set keanggotaan
        $selectedSet = array_flip(array_map('strval', $selectedDetailIds));

        $mulaiList = $request->aktual_mulai_lembur;
        $selesaiList = $request->aktual_selesai_lembur;
        $detailIds = $request->id_detail_lembur;
        $keteranganList = $request->keterangan ?? [];

        DB::beginTransaction();
        try {
            /** @var \App\Models\Lembure $lembur */
            $lembur = Lembure::with([
                'detailLembur.karyawan.settingLembur',
                'issued.user',      // user pembuat (cek role admin-dept)
                'issued.posisi',    // posisi pembuat
                'attachmentLembur', // LKH
            ])
                ->lockForUpdate()
                ->find($id_lembur);

            if (!$lembur) {
                DB::rollBack();
                return response()->json(['message' => 'Data lembur tidak ditemukan.'], 404);
            }

            // Guardrail proses Actual
            if ($lembur->status !== 'PLANNED') {
                DB::rollBack();
                return response()->json(['message' => 'Status lembur harus PLANNED untuk melakukan Done.'], 403);
            }
            if (empty($selectedDetailIds)) {
                DB::rollBack();
                return response()->json(['message' => 'Minimal ada 1 orang yang di-Approved untuk aktual!'], 403);
            }
            if (empty($lembur->attachmentLembur)) {
                \Log::warning('Lembur done tanpa LKH', ['id_lembur' => $lembur->id_lembur]);
                // tidak return, tetap lanjut proses
            }

            // Rencana harus sudah dilegalisir
            if (is_null($lembur->plan_legalized_by)) {
                DB::rollBack();
                return response()->json(['message' => 'Pengajuan Lembur (Plan) belum di-legalisir HR & GA.'], 403);
            }

            // Normalisasi jenis hari (dukung legacy)
            $jenisHariRaw = $lembur->jenis_hari;
            $jenisHari = in_array($jenisHariRaw, ['WD', 'WE']) ? $jenisHariRaw : ($jenisHariRaw === 'WEEKDAY' ? 'WD' : 'WE');

            // =====================
            // Update detail aktual
            // =====================
            $totalDurasiAktual = 0;
            $totalNominalAktual = 0;
            $nowName = auth()->user()->karyawan->nama ?? auth()->user()->name;

            foreach ($detailIds as $idx => $detailId) {
                /** @var \App\Models\DetailLembur|null $detail */
                $detail = $lembur->detailLembur->firstWhere('id_detail_lembur', $detailId);
                if (!$detail) {
                    continue;
                }

                // 🔧 Gunakan set string untuk menghindari mismatch tipe
                $isSelected = isset($selectedSet[(string) $detail->id_detail_lembur]);

                if (!$isSelected) {
                    // Tidak diikutkan aktual
                    $detail->is_aktual_approved = 'N';
                    $detail->aktual_last_changed_by = $nowName;
                    $detail->aktual_last_changed_at = now();
                    $detail->keterangan = $keteranganList[$idx] ?? null;
                    $detail->save();
                    continue;
                }

                // Diikutkan aktual
                $karyawan = $detail->karyawan;
                $gaji = optional($karyawan->settingLembur)->gaji;
                if (is_null($gaji)) {
                    DB::rollBack();
                    return response()->json(['message' => 'Setting gaji lembur belum ada untuk ' . ($karyawan->nama ?? 'karyawan ID ' . $detail->karyawan_id)], 402);
                }

                $startAct = $this->pembulatan_menit_ke_bawah($mulaiList[$idx]);
                $endAct = $this->pembulatan_menit_ke_bawah($selesaiList[$idx]);

                $durIst = $this->overtime_resttime_per_minutes($startAct, $endAct, $detail->organisasi_id);
                $dur = $this->calculate_overtime_per_minutes($startAct, $endAct, $detail->organisasi_id);

                if ($dur < 60) {
                    DB::rollBack();
                    return response()->json(['message' => 'Durasi lembur ' . ($karyawan->nama ?? '-') . ' kurang dari 1 jam.'], 402);
                }

                $durKonv = $this->calculate_durasi_konversi_lembur($jenisHari, $dur, $detail->karyawan_id);
                $uangMkn = $this->calculate_overtime_uang_makan($jenisHari, $dur, $detail->karyawan_id);
                $nominal = $this->calculate_overtime_nominal($jenisHari, $dur, $detail->karyawan_id);
                $pembagi = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')
                    ->where('organisasi_id', $detail->organisasi_id)
                    ->value('value');

                // ✅ Set field aktual utk yang dipilih
                $detail->is_aktual_approved = 'Y'; // penting
                $detail->aktual_mulai_lembur = $startAct;
                $detail->aktual_selesai_lembur = $endAct;
                $detail->durasi_istirahat = $durIst;
                $detail->durasi_konversi_lembur = $durKonv;
                $detail->uang_makan = $uangMkn;
                $detail->gaji_lembur = $gaji;
                $detail->pembagi_upah_lembur = $pembagi;
                $detail->durasi = $dur;
                $detail->nominal = $nominal;
                $detail->aktual_last_changed_by = $nowName;
                $detail->aktual_last_changed_at = now();
                $detail->keterangan = $keteranganList[$idx] ?? null;
                $detail->save();

                $totalDurasiAktual += $dur;
                $totalNominalAktual += $nominal;
            }

            // ============================================================
            // Auto-routing ACTUAL (Checked/Approved) berdasarkan *pembuat*
            // ============================================================
            $issuedKar = $lembur->issued;            // Karyawan pembuat
            $issuedUser = optional($issuedKar)->user; // User pembuat (cek role admin-dept)
            $makerIsAdmin = $issuedUser ? $issuedUser->hasRole('admin-dept') : false;
            $creatorNama = $issuedKar->nama ?? 'SYSTEM';
            $creatorPos = $issuedKar?->posisi ?? collect();
            $creatorJabId = optional($creatorPos->first())->jabatan_id;

            $hasLeader = \App\Helpers\Approval::HasLeader($creatorPos);
            $hasSecHead = \App\Helpers\Approval::HasSectionHead($creatorPos);
            $hasDeptHead = \App\Helpers\Approval::HasDepartmentHead($creatorPos)
                || \App\Models\Posisi::where('organisasi_id', $lembur->organisasi_id)
                    ->where('divisi_id', $lembur->divisi_id)
                    ->where('departemen_id', $lembur->departemen_id)
                    ->where('jabatan_id', 2) // Dept.Head
                    ->exists();

            $getAtasanNamaByJabatan = function ($posisiCollection, int $jabatanId): ?string {
                foreach ($posisiCollection as $pos) {
                    $parentIds = \App\Helpers\Approval::GetParentPosisi($pos);
                    foreach ($parentIds as $pid) {
                        if ($pid === 0)
                            continue;
                        $parent = \App\Models\Posisi::with('karyawan')->find($pid);
                        if ($parent && (int) $parent->jabatan_id === $jabatanId) {
                            return optional($parent->karyawan)->nama;
                        }
                    }
                }
                return null;
            };

            $leaderRaw = \App\Helpers\Approval::GetLeader($creatorPos);
            $leaderNama = $leaderRaw instanceof \Illuminate\Support\Collection
                ? optional($leaderRaw->first())->nama
                : optional($leaderRaw)->nama;

            $secRaw = \App\Helpers\Approval::GetSectionHead($creatorPos);
            $secNama = $secRaw?->nama ?: $getAtasanNamaByJabatan($creatorPos, 4);
            $now = now();

            $maker = 'unknown';
            if ($makerIsAdmin)
                $maker = 'admin';
            elseif ((int) $creatorJabId === 5)
                $maker = 'leader';
            elseif ((int) $creatorJabId === 4)
                $maker = 'sec';
            elseif ((int) $creatorJabId === 2)
                $maker = 'dept';

            $updateHeader = [
                'total_durasi' => $totalDurasiAktual,
                'total_nominal' => $totalNominalAktual,
            ];

            $setChecked = function (string $by) use (&$updateHeader, $lembur, $now) {
                if (empty($lembur->actual_checked_by)) {
                    $updateHeader['actual_checked_by'] = $by;
                    $updateHeader['actual_checked_at'] = $now;
                }
            };
            $setApproved = function (string $by) use (&$updateHeader, $lembur, $now) {
                if (empty($lembur->actual_approved_by)) {
                    $updateHeader['actual_approved_by'] = $by;
                    $updateHeader['actual_approved_at'] = $now;
                }
            };

            switch ($maker) {
                case 'admin':
                    if (!$hasLeader && !$hasSecHead && !$hasDeptHead) {
                        $setChecked($creatorNama);
                        $setApproved($creatorNama);
                    } elseif (!$hasLeader && !$hasSecHead && $hasDeptHead) {
                        $setChecked($creatorNama);
                    } elseif ($hasLeader && !$hasSecHead && $hasDeptHead) {
                        $setChecked($leaderNama ?: $creatorNama);
                    } elseif ($hasSecHead && $hasDeptHead) {
                        $setChecked($secNama ?: $creatorNama);
                    } else {
                        $setChecked($creatorNama);
                    }
                    break;

                case 'leader':
                    if (!$hasSecHead && !$hasDeptHead) {
                        $setChecked($creatorNama);
                        $setApproved($creatorNama);
                    } elseif (!$hasSecHead && $hasDeptHead) {
                        $setChecked($creatorNama);
                    } elseif ($hasSecHead && !$hasDeptHead) {
                        // ✅ Leader ada Section Head, tapi tidak ada DeptHead
                        $setChecked($secNama ?: $creatorNama);
                        $setApproved($secNama ?: $creatorNama); // otomatis approve juga
                    } elseif ($hasSecHead && $hasDeptHead) {
                        $setChecked($secNama ?: $creatorNama);
                    } else {
                        $setChecked($creatorNama);
                    }
                    break;

                case 'sec':
                    if (!$hasDeptHead) {
                        $setChecked($creatorNama);
                        $setApproved($creatorNama);
                    } else {
                        $setChecked($creatorNama);
                    }
                    break;

                case 'dept':
                    $setChecked($creatorNama);
                    $setApproved($creatorNama);
                    break;

                default:
                    $setChecked($creatorNama);
                    break;
            }

            // Catatan:
            // - Actual Reviewed: menunggu BOD (actual_reviewed_by NULL)
            // - Actual Legalized: menunggu HR & GA (actual_legalized_by NULL)
            // - JANGAN set status COMPLETED di sini (baru saat legalized actual).

            $updateHeader['status'] = 'COMPLETED';
            $lembur->update($updateHeader);

            DB::commit();
            return response()->json(['message' => 'Aktual Lembur berhasil dikonfirmasi.'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in done(): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    //CHART
    public function get_monthly_lembur_per_departemen(Request $request)
    {
        try {
            $dataFilter = [];

            $filterOrganisasi = $request->organisasi;
            if (isset($filterOrganisasi)) {
                $dataFilter['organisasi'] = $filterOrganisasi;
            }

            $filterDepartemen = $request->departemen;
            if (isset($filterDepartemen)) {
                $dataFilter['departemen'] = $filterDepartemen;
            }

            $filterTahun = $request->tahun;
            if (isset($filterTahun)) {
                $dataFilter['tahun'] = $filterTahun;
            }

            if (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
                $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
            } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3) {
                $dataFilter['departemen_id'] = auth()->user()->karyawan->posisi->pluck('departemen_id')->toArray();
            } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 2) {
                // JIKA PLANT HEAD
                if (auth()->user()->karyawan->posisi[0]->divisi_id == 3) {
                    $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;
                    // JIKA NON PLANT HEAD
                } else {
                    $dataFilter['divisi_id'] = auth()->user()->karyawan->posisi->pluck('divisi_id')->toArray();
                }
            }

            $dataActual = DetailLembur::getMonthlyLemburPerDepartemenActual($dataFilter);
            $dataPlanning = DetailLembur::getMonthlyLemburPerDepartemenPlanning($dataFilter);
            $batas = GajiDepartemen::getMonthlyNominalBatasAllDepartemen($dataFilter)->toArray();

            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'dataActual' => $dataActual, 'dataPlanning' => $dataPlanning, 'batas' => $batas], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_weekly_lembur_per_departemen(Request $request)
    {
        try {
            $dataFilter = [];

            $filterDepartemen = $request->departemen;
            if (isset($filterDepartemen)) {
                $dataFilter['departemen'] = $filterDepartemen;
            }

            $filterPeriode = $request->periode;
            if (isset($filterPeriode)) {
                $dataFilter['month'] = Carbon::parse($filterPeriode)->format('m');
                $dataFilter['year'] = Carbon::parse($filterPeriode)->format('Y');
            } else {
                $dataFilter['month'] = date('m');
                $dataFilter['year'] = date('Y');
            }

            $data = DetailLembur::getWeeklyLemburPerDepartemen($dataFilter);
            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_current_month_lembur_per_departemen(Request $request)
    {
        try {
            $dataFilter = [];

            $filterDepartemen = $request->departemen;
            if (isset($filterDepartemen)) {
                $dataFilter['departemen'] = $filterDepartemen;
            }

            $filterPeriode = $request->periode;
            if (isset($filterPeriode)) {
                $dataFilter['month'] = Carbon::parse($filterPeriode)->format('m');
                $dataFilter['year'] = Carbon::parse($filterPeriode)->format('Y');
            } else {
                $dataFilter['month'] = date('m');
                $dataFilter['year'] = date('Y');
            }

            $data = DetailLembur::getCurrentMonthLemburPerDepartemen($dataFilter)->toArray();
            $batas = GajiDepartemen::getCurrentMonthNominalBatasPerDepartemen($dataFilter)->toArray();

            $existing_batas = [];
            foreach ($batas as $key => $value) {
                foreach ($data as $key2 => $value2) {
                    if ($value['id_departemen'] == $value2->id_departemen) {
                        $existing_batas[] = $value;
                    }
                }
            }

            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data, 'batas' => $existing_batas], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_leaderboard_user_monthly(Request $request)
    {
        try {
            $dataFilter = [];

            if (auth()->user()->karyawan && (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3)) {
                $posisi = auth()->user()->karyawan->posisi;
                $member_posisi_ids = $this->get_member_posisi($posisi);
                $dataFilter['member_posisi_ids'] = $member_posisi_ids;
            }

            $filterPeriode = $request->periode;
            if (isset($filterPeriode)) {
                $dataFilter['month'] = Carbon::parse($filterPeriode)->format('m');
                $dataFilter['year'] = Carbon::parse($filterPeriode)->format('Y');
            } else {
                $dataFilter['month'] = date('m');
                $dataFilter['year'] = date('Y');
            }

            $filterLimit = $request->limit;
            if (isset($filterLimit)) {
                $dataFilter['limit'] = $filterLimit;
            }

            $filterDepartemen = $request->departemen;
            if (isset($filterDepartemen)) {
                $dataFilter['departemen'] = $filterDepartemen;
            }
            $data = DetailLembur::getLeaderboardUserMonthly($dataFilter)->toArray();
            return response()->json(['message' => 'Data Lembur Berhasil Ditemukan', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    //EXPORT REPORT LEMBUR
    public function export_rekap_lembur_perbulan(Request $request)
    {

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
            'GAJI POKOK ' . $year,
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
        $departemens_data = [];
        $man_powers = [];
        $gaji_departemen = 0;
        $jam_lembur = 0;
        $konversi_jam_lembur = 0;
        $uang_makan = 0;
        $gaji_lembur = 0;
        if ($rekapLembur) {
            foreach ($rekapLembur as $index => $data) {
                if ($is_first) {
                    $sheet->setCellValue('B' . $row, 'DEPARTEMEN ' . $data->departemen);
                    $sheet->mergeCells('B' . $row . ':E' . $row);
                    $sheet->getStyle('B' . $row . ':E' . $row)->applyFromArray([
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
                    $departemen_first_data_row = $row + 1;
                    $is_first = false;
                    $row++;
                }

                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $data->nama);
                $sheet->setCellValue('C' . $row, $data->departemen);
                $sheet->setCellValue('D' . $row, $data->posisi);
                $sheet->setCellValue('E' . $row, '1 ' . Carbon::createFromFormat('m', $month)->format('F Y') . ' - ' . Carbon::createFromFormat('Y-m', $year . '-' . $month)->endOfMonth()->format('d F Y'));
                $sheet->setCellValue('F' . $row, $data->gaji);
                $sheet->setCellValue('G' . $row, $data->jabatan_id >= 5 ? $data->upah_lembur_per_jam : '-');
                $sheet->setCellValue('H' . $row, $data->total_jam_lembur);
                $sheet->setCellValue('I' . $row, $data->konversi_jam_lembur);
                $sheet->setCellValue('J' . $row, $data->jabatan_id >= 5 ? $data->gaji_lembur : '-');
                $sheet->setCellValue('K' . $row, $data->jabatan_id >= 5 ? $data->uang_makan : '-');
                $sheet->setCellValue('L' . $row, $data->total_gaji_lembur);

                if ($data->jabatan_id >= 5) {
                    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');
                }

                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode('#,##0');

                //ALIGN CENTER
                $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $gaji_departemen += $data->total_gaji_lembur;
                $jam_lembur += $data->total_jam_lembur;
                $konversi_jam_lembur += $data->konversi_jam_lembur;
                $uang_makan += $data->uang_makan;
                $data->jabatan_id >= 5 ? $gaji_lembur += $data->gaji_lembur : $gaji_lembur += 0;

                if (!in_array($data->nama, $man_powers)) {
                    $man_powers[] = $data->nama;
                }

                $no++;
                $row++;

                if (isset($rekapLembur[$index + 1]) && $rekapLembur[$index + 1]->departemen !== $data->departemen) {
                    $is_first = true;
                    $is_last = true;
                }

                if (!isset($rekapLembur[$index + 1])) {
                    $is_last = true;
                }

                if ($is_last) {
                    $departemens_data[] = [
                        'nama_departemen' => $data->departemen,
                        'total_gaji_departemen' => $gaji_departemen,
                        'total_gaji_lembur' => $gaji_lembur,
                        'total_jam_lembur' => $jam_lembur,
                        'total_konversi_jam_lembur' => $konversi_jam_lembur,
                        'total_uang_makan' => $uang_makan,
                        'total_man_power' => count($man_powers)
                    ];
                    $sheet->setCellValue('A' . $row, '###');
                    $sheet->setCellValue('B' . $row, 'TOTAL GAJI DEPT. ' . $data->departemen);
                    $sheet->setCellValue('C' . $row, $data->departemen);
                    $sheet->setCellValue('D' . $row, '-');
                    $sheet->setCellValue('E' . $row, '-');
                    $sheet->setCellValue('F' . $row, '-');
                    $sheet->setCellValue('G' . $row, '-');
                    $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
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
                    $sheet->setCellValue('H' . $row, '=SUM(H' . $departemen_first_data_row . ':H' . ($row - 1) . ')');
                    $sheet->setCellValue('I' . $row, '=SUM(I' . $departemen_first_data_row . ':I' . ($row - 1) . ')');
                    $sheet->setCellValue('J' . $row, '=SUM(J' . $departemen_first_data_row . ':J' . ($row - 1) . ')');
                    $sheet->setCellValue('K' . $row, '=SUM(K' . $departemen_first_data_row . ':K' . ($row - 1) . ')');
                    $sheet->setCellValue('L' . $row, '=SUM(L' . $departemen_first_data_row . ':L' . ($row - 1) . ')');

                    $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    $is_last = false;
                    $gaji_departemen = 0;
                    $jam_lembur = 0;
                    $konversi_jam_lembur = 0;
                    $uang_makan = 0;
                    $gaji_lembur = 0;
                    $man_powers = [];

                    if (isset($rekapLembur[$index + 1])) {
                        $no = 1;
                        $row++;
                    }
                }
            }
        }

        $spreadsheet->createSheet();
        $summarySheet = $spreadsheet->getSheet(1);
        $summarySheet->setTitle('SUMMARY GAJI LEMBUR');
        $rowSummary = 1;
        $colSummary = 'A';
        $headers = [
            'NO',
            'DEPARTEMEN',
            'JUMLAH KARYAWAN',
            'PERIODE PERHITUNGAN',
            'TOTAL JAM LEMBUR',
            'KONVERSI JAM LEMBUR',
            'GAJI LEMBUR',
            'UANG MAKAN',
            'TOTAL GAJI LEMBUR'
        ];

        foreach ($headers as $header) {
            $summarySheet->setCellValue($colSummary . '1', $header);
            $summarySheet->getStyle($colSummary . '1')->applyFromArray([
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
            $colSummary++;
        }

        $rowSummary = 2;

        $columnsSummary = range('A', 'I');
        foreach ($columnsSummary as $column) {
            $summarySheet->getColumnDimension($column)->setAutoSize(true);
        }

        $total_gaji_departemen = 0;
        $total_gaji_lembur = 0;
        $total_jam_lembur = 0;
        $total_konversi_jam_lembur = 0;
        $total_uang_makan = 0;
        $total_man_power = 0;
        if (!empty($departemens_data)) {
            foreach ($departemens_data as $index => $data) {
                $summarySheet->setCellValue('A' . $rowSummary, $index + 1);
                $summarySheet->setCellValue('B' . $rowSummary, $data['nama_departemen']);
                $summarySheet->setCellValue('C' . $rowSummary, $data['total_man_power']);
                $summarySheet->setCellValue('D' . $rowSummary, Carbon::createFromFormat('Y-m', $year . '-' . $month)->format('F Y'));
                $summarySheet->setCellValue('E' . $rowSummary, $data['total_jam_lembur']);
                $summarySheet->setCellValue('F' . $rowSummary, $data['total_konversi_jam_lembur']);
                $summarySheet->setCellValue('G' . $rowSummary, $data['total_gaji_lembur']);
                $summarySheet->setCellValue('H' . $rowSummary, $data['total_uang_makan']);
                $summarySheet->setCellValue('I' . $rowSummary, $data['total_gaji_departemen']);
                $total_gaji_departemen += $data['total_gaji_departemen'];
                $total_gaji_lembur += $data['total_gaji_lembur'];
                $total_jam_lembur += $data['total_jam_lembur'];
                $total_konversi_jam_lembur += $data['total_konversi_jam_lembur'];
                $total_uang_makan += $data['total_uang_makan'];
                $total_man_power += $data['total_man_power'];

                $summarySheet->getStyle('G' . $rowSummary)->getNumberFormat()->setFormatCode('#,##0');
                $summarySheet->getStyle('H' . $rowSummary)->getNumberFormat()->setFormatCode('#,##0');
                $summarySheet->getStyle('I' . $rowSummary)->getNumberFormat()->setFormatCode('#,##0');
                $rowSummary++;
            }
        }

        $summarySheet->mergeCells('A' . $rowSummary . ':B' . $rowSummary);
        $summarySheet->setCellValue('A' . $rowSummary, 'TOTAL');
        $summarySheet->setCellValue('C' . $rowSummary, $total_man_power);
        $summarySheet->setCellValue('E' . $rowSummary, $total_jam_lembur);
        $summarySheet->setCellValue('F' . $rowSummary, $total_konversi_jam_lembur);
        $summarySheet->setCellValue('G' . $rowSummary, $total_gaji_lembur);
        $summarySheet->setCellValue('H' . $rowSummary, $total_uang_makan);
        $summarySheet->setCellValue('I' . $rowSummary, $total_gaji_departemen);
        $summarySheet->getStyle('G' . $rowSummary)->getNumberFormat()->setFormatCode('#,##0');
        $summarySheet->getStyle('H' . $rowSummary)->getNumberFormat()->setFormatCode('#,##0');
        $summarySheet->getStyle('I' . $rowSummary)->getNumberFormat()->setFormatCode('#,##0');

        //STYLE ALL CELLS
        $sheet->getStyle('A1:L' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $summarySheet->getStyle('A' . $rowSummary . ':B' . $rowSummary)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $summarySheet->getStyle('C' . $rowSummary . ':I' . $rowSummary)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF00',
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $summarySheet->getStyle('A1:I' . $rowSummary)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rekapitulasi Pembayaran Lembur - ' . Carbon::createFromFormat('m', $month)->format('F') . ' ' . $year . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }

    public function export_slip_lembur_perbulan(Request $request)
    {
        $dataValidate = [
            'periode_slip' => ['required', 'date_format:Y-m'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $organisasi_id = auth()->user()->organisasi_id;
            $periode = $request->periode_slip;
            $start = Carbon::createFromFormat('Y-m', $periode)->startOfMonth()->toDateString();
            $end = Carbon::createFromFormat('Y-m', $periode)->endOfMonth()->toDateString();
            $pembagi_upah_lembur_harian = SettingLembur::where('organisasi_id', $organisasi_id)->where('setting_name', 'pembagi_upah_lembur_harian')->first()->value;

            if ($request->departemen_slip) {
                $departemen = Departemen::find($request->departemen_slip)->nama;
                $exists = ExportSlipLembur::where('periode', Carbon::parse($periode)->format('Y-m-d'))->where('departemen_id', $request->departemen_slip)->where('organisasi_id', $organisasi_id)->where('status', 'IP')->exists();
            } else {
                $departemen = 'ALL DEPARTMENT';
                $exists = ExportSlipLembur::where('periode', Carbon::parse($periode)->format('Y-m-d'))->whereNull('departemen_id')->where('organisasi_id', $organisasi_id)->where('status', 'IP')->exists();
            }

            if ($exists) {
                return response()->json(['message' => 'Export Slip Lembur sedang di Proses, silahkan tunggu beberapa saat'], 400);
            }

            $export_slip_lembur = ExportSlipLembur::create([
                'periode' => $periode,
                'departemen_id' => $request->departemen_slip ? $request->departemen_slip : null,
                'organisasi_id' => $organisasi_id,
            ]);

            ExportSlipLemburJob::dispatch($periode, $organisasi_id, $departemen, $request->departemen_slip, $pembagi_upah_lembur_harian, $start, $end, $export_slip_lembur);

            DB::commit();
            return response()->json(['message' => 'Export Slip Lembur sedang di Proses, silahkan tunggu beberapa saat'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => 'Error while Exporting Data : ' . $e->getMessage()], 500);
        }
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

            if ($request->hasFile('upah_lembur_karyawan_file')) {
                $upah_lembur_karyawan_records = 'ULK_' . time() . '.' . $file->getClientOriginalExtension();
                $upah_lembur_karyawan_file = $file->storeAs("attachment/upload-upah-lembur-karyawan", $upah_lembur_karyawan_records);
            }

            if (file_exists(storage_path("app/public/" . $upah_lembur_karyawan_file))) {
                $spreadsheet = IOFactory::load(storage_path("app/public/" . $upah_lembur_karyawan_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                unset($data[0]);
                $error_row = 0;
                foreach ($data as $key => $row) {
                    $error_row++;
                    Log::info($row[0]);
                    activity('upload_upah_lembur_karyawan')->log('Upload Upah Lembur Karyawan ' . $row[0]);
                    $karyawan = Karyawan::where('ni_karyawan', $row[0])->organisasi($organisasi_id)->first();

                    if ($karyawan) {
                        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $karyawan->id_karyawan)->first();
                        if ($karyawan->user->organisasi_id !== auth()->user()->organisasi_id) {
                            DB::rollback();
                            return response()->json(['message' => 'NIK Karyawan ' . $karyawan->nama . ' tidak terdaftar di Plant Anda!'], 404);
                        }

                        if (!$karyawan->posisi) {
                            DB::rollback();
                            return response()->json(['message' => 'Karyawan ' . $karyawan->nama . ' belum memiliki posisi, setting di master data!'], 404);
                        }

                        if (!is_numeric($row[2]) || $row[2] < 0) {
                            DB::rollback();
                            return response()->json(['message' => 'Gaji ' . $karyawan->nama . ' harus berupa angka dan tidak boleh kurang dari 0!'], 402);
                        }

                        if ($setting_lembur_karyawan) {
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
                        continue;
                        return response()->json(['message' => 'Data Karyawan dengan NI Karyawan ' . $row[0]] . ' tidak ditemukan, periksa kembali di data master');
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
            activity('upload_upah_lembur_karyawan')->log('Error Upload Upah Lembur Karyawan ' . $e->getMessage() . ' in row ' . $error_row);
            return response()->json(['message' => 'Error processing the file: ' . $e->getMessage() . 'in row' . $error_row], 500);
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
        try {

            $fileName = $lembur->id_lembur . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file_path = $file->storeAs("attachment/lembur", $fileName, 'public');

            $lembur->attachmentLembur()->create([
                'path' => $file_path
            ]);

            DB::commit();
            return response()->json(['message' => 'LKH Berhasil di Upload!'], 200);
        } catch (Throwable $e) {
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

        try {
            //Can See Nominal
            $is_can_see_nominal = true;
            if (auth()->user()->hasRole('atasan')) {
                if (auth()->user()->karyawan->posisi[0]->jabatan_id <= 3) {
                    $is_can_see_nominal = true;
                }
            } elseif (auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
                $is_can_see_nominal = true;
            }

            $datetime_mulai_lembur = $this->pembulatan_menit_ke_bawah($mulai_lembur);
            $datetime_selesai_lembur = $this->pembulatan_menit_ke_bawah($selesai_lembur);
            $durasi = $this->calculate_overtime_per_minutes($datetime_mulai_lembur, $datetime_selesai_lembur, $detail_lembur->karyawan->user->organisasi_id, $jenis_hari, $karyawan_id);
            $nominal = $this->calculate_overtime_nominal($jenis_hari, $durasi, $karyawan_id);

            $hours = floor($durasi / 60);
            $minutes = $durasi % 60;

            $durasi_text = $hours . ' jam ' . $minutes . ' menit';
            $nominal_text = $is_can_see_nominal ? 'Rp ' . number_format($nominal, 0, ',', '.') : '-';

            $data = [
                'durasi' => $durasi_text,
                'nominal' => $nominal_text
            ];
            return response()->json(['message' => 'Data Durasi dan Nominal Lembur Berhasil Ditemukan', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function generate_lembur_harian()
    {
        DB::beginTransaction();
        try {
            $data = DetailLembur::generateLemburHarian();
            if ($data) {
                foreach ($data as $key => $value) {
                    $lembur_harian = LemburHarian::where('organisasi_id', $value->organisasi_id)->where('divisi_id', $value->divisi_id)->where('departemen_id', $value->departemen_id)->whereDate('tanggal_lembur', $value->tanggal_lembur)->first();
                    if ($lembur_harian) {
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
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_list_data_cross_check(Request $request)
    {
        $dataValidate = [
            'date' => ['required', 'date_format:Y-m-d'],
            'id_karyawan' => ['required', 'exists:karyawans,id_karyawan'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $date = $request->date;
        $id_karyawan = $request->id_karyawan;
        $organisasi_id = auth()->user()->organisasi_id;

        try {
            $karyawan = Karyawan::find($id_karyawan);
            $pin = $karyawan->pin;
            $scanlog = Scanlog::where('pin', $pin)->where('organisasi_id', $organisasi_id)->whereDate('scan_date', $date)->get();
            if ($scanlog->isNotEmpty()) {
                return response()->json(['message' => 'Data Presensi Berhasil Ditemukan', 'data' => $scanlog], 200);
            } else {
                return response()->json(['message' => 'Data Presensi Tidak Ditemukan'], 404);
            }
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_review_lembur_detail(Request $request)
    {
        $dataValidate = [
            'departemen_id' => ['required', 'exists:departemens,id_departemen'],
            'divisi_id' => ['required', 'exists:divisis,id_divisi'],
            'organisasi_id' => ['required', 'exists:organisasis,id_organisasi'],
            'tanggal_lembur' => ['required', 'date_format:Y-m-d'],
            'status' => ['required', 'in:PLANNING,ACTUAL'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $departemen_id = $request->departemen_id;
        $divisi_id = $request->divisi_id;
        $organisasi_id = $request->organisasi_id;
        $tanggal_lembur = $request->tanggal_lembur;
        $status = $request->status;

        try {
            if ($status == 'PLANNING') {
                $data = DetailLembur::selectRaw('detail_lemburs.id_detail_lembur, detail_lemburs.rencana_mulai_lembur as tanggal_mulai, detail_lemburs.rencana_selesai_lembur as tanggal_selesai, detail_lemburs.deskripsi_pekerjaan, detail_lemburs.keterangan, detail_lemburs.nominal, detail_lemburs.durasi, lemburs.status, karyawans.nama as karyawan, detail_lemburs.lembur_id, lemburs.plan_checked_by, lemburs.plan_checked_at, lemburs.plan_approved_by, lemburs.plan_approved_at')
                    ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
                    ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
                    ->where('detail_lemburs.departemen_id', $departemen_id)
                    ->where('detail_lemburs.divisi_id', $divisi_id)
                    ->where('detail_lemburs.organisasi_id', $organisasi_id)
                    ->whereDate('detail_lemburs.rencana_mulai_lembur', $tanggal_lembur)
                    ->where(function ($query) {
                        $query->where('lemburs.status', 'WAITING');
                        $query->whereNotNull('lemburs.plan_approved_by');
                    })->get();
            } else {
                $data = DetailLembur::selectRaw('detail_lemburs.id_detail_lembur, detail_lemburs.aktual_mulai_lembur as tanggal_mulai, detail_lemburs.aktual_selesai_lembur as tanggal_selesai, detail_lemburs.deskripsi_pekerjaan, detail_lemburs.keterangan, detail_lemburs.nominal, detail_lemburs.durasi, lemburs.status, karyawans.nama as karyawan, detail_lemburs.lembur_id, lemburs.actual_checked_by, lemburs.actual_checked_at, lemburs.actual_approved_by, lemburs.actual_approved_at')
                    ->leftJoin('karyawans', 'karyawans.id_karyawan', 'detail_lemburs.karyawan_id')
                    ->leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
                    ->where('detail_lemburs.departemen_id', $departemen_id)
                    ->where('detail_lemburs.divisi_id', $divisi_id)
                    ->where('detail_lemburs.organisasi_id', $organisasi_id)
                    ->whereDate('detail_lemburs.aktual_mulai_lembur', $tanggal_lembur)
                    ->where(function ($query) {
                        $query->where('lemburs.status', 'COMPLETED');
                        $query->whereNotNull('lemburs.actual_approved_by');
                    })->get();
            }
            ;
            return response()->json(['message' => 'Data Detail Lembur Berhasil didapatkan!', 'data' => $data]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reject_ispast_lembur()
    {
        try {
            $organisasi_id = auth()->user()->organisasi_id;
            $date = Carbon::now()->format('Y-m-d');
            $batas_jam_approval_lembur = SettingLembur::where('setting_name', 'batas_approval_lembur')->where('organisasi_id', $organisasi_id)->first() ? SettingLembur::where('setting_name', 'batas_approval_lembur')->where('organisasi_id', $organisasi_id)->first()->value : '16:30';
            $batas_approval_lembur = Carbon::parse($date . ' ' . $batas_jam_approval_lembur);

            $organisasi_id = auth()->user()->organisasi_id;
            $posisi = auth()->user()?->karyawan?->posisi;
            $mustRejectLembur = [];
            if (!auth()->user()->hasAnyRole(['personalia', 'personalia-lembur'])) {
                if (auth()->user()->karyawan->posisi[0]->jabatan_id == 4 || auth()->user()->karyawan->posisi[0]->jabatan_id == 3) {
                    $member_posisi_ids = $this->get_member_posisi($posisi);
                    $dataFilter['member_posisi_ids'] = $member_posisi_ids;
                    $is_has_department_head = $this->has_department_head($posisi);
                } elseif (auth()->user()->karyawan->posisi[0]->jabatan_id == 2) {
                    // JIKA PLANT HEAD
                    if (auth()->user()->karyawan->posisi[0]->divisi_id == 3) {
                        $posisis_has_div_head = Posisi::where('jabatan_id', 2)
                            ->whereHas('karyawan')
                            ->whereNot('divisi_id', 3)
                            ->where(function ($query) {
                                $query->whereNull('organisasi_id')
                                    ->orWhere('organisasi_id', auth()->user()->organisasi_id);
                            })
                            ->distinct()
                            ->pluck('divisi_id')
                            ->toArray();
                        $divisis = Divisi::whereNotIn('id_divisi', $posisis_has_div_head)->pluck('id_divisi');
                        $dataFilter['divisi_id'] = $divisis;
                        $dataFilter['organisasi_id'] = $organisasi_id;
                        // JIKA NON PLANT HEAD
                    } else {
                        $member_posisi_ids = $this->get_member_posisi($posisi);
                        $dataFilter['member_posisi_ids'] = $member_posisi_ids;
                        $dataFilter['is_div_head'] = true;
                    }
                }
                $dataFilter['mustChecked'] = true;
                $dataFilter['batasApprovalLembur'] = $batas_approval_lembur;
                $mustRejectLembur = Lembure::isPastLembur($dataFilter);
            }

            if ($mustRejectLembur->isNotEmpty()) {
                DB::beginTransaction();
                foreach ($mustRejectLembur as $lembur) {
                    $lembur->update([
                        'status' => 'REJECTED',
                        'rejected_at' => now(),
                        'rejected_note' => 'MELEWATI BATAS WAKTU APPROVAL',
                        'rejected_by' => 'SYSTEM',
                    ]);

                    $lembur->detailLembur()->update([
                        'is_rencana_approved' => 'N',
                        'is_aktual_approved' => 'N',
                    ]);
                }
                DB::commit();
                return response()->json(['message' => 'Berhasil Reject Lembur yang Melewati Batas Waktu Approval', 'count' => $mustRejectLembur->count()], 200);
            } else {
                return response()->json(['message' => 'Tidak ada lembur yang harus di reject', 'count' => 0], 200);
            }
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
