<?php

namespace App\Http\Controllers\KSK;

use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use App\Helpers\Approval;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReleaseController extends Controller
{
    public function index()
    {
        // return redirect()->route('under-maintenance');
        $dataPage = [
            'pageTitle' => "KSK-E - Release KSK",
            'page' => 'ksk-release',
        ];
        return view('pages.ksk-e.release.index', $dataPage);
    }

    public function datatable_unreleased(Request $request)
    {
        $columns = array(
            0 => 'sub.jabatan_id',
            1 => 'sub.divisi_nama',
            2 => 'sub.departemen_nama',
            3 => 'bulan_selesai',
            4 => 'jumlah_karyawan_habis',
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

        $ksk = Karyawan::getDataKSK($dataFilter, $settings);
        $totalData = Karyawan::countDataKSK($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($ksk)) {
            foreach ($ksk as $data) {
                $actionFormatted = '<button class="btn btn-sm btn-success btnRelease" data-id-departemen="'.$data->id_departemen.'" data-id-divisi="'.$data->id_divisi.'" data-parent-id="'.$data->parent_id.'" data-tahun-selesai="'.$data->tahun_selesai.'" data-bulan-selesai="'.str_pad($data->bulan_selesai, 2, '0', STR_PAD_LEFT).'" data-nama-departemen="'.$data->departemen_nama.'" data-nama-divisi="'.$data->divisi_nama.'"><i class="fas fa-plus"></i> Buat KSK</button>';

                $nestedData['level'] = $data->jabatan_nama;
                $nestedData['divisi'] = $data->divisi_nama;
                $nestedData['departemen'] = $data->departemen_nama;
                $nestedData['release_for'] = Carbon::createFromDate($data->tahun_selesai, $data->bulan_selesai, 1)->format('M Y');
                $nestedData['jumlah_karyawan_habis'] = $data->jumlah_karyawan_habis.' Orang';
                $nestedData['action'] = $actionFormatted;

                $dataTable[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function datatable_released(Request $request)
    {
        $columns = array(
            0 => 'ksk.id_ksk',
            1 => 'ksk.nama_divisi',
            2 => 'ksk.nama_departemen',
            3 => 'ksk.release_date',
            4 => 'ksk.parent_id',
            5 => 'ksk.released_by',
            6 => 'ksk.checked_by',
            7 => 'ksk.approved_by',
            8 => 'ksk.reviewed_div_by',
            9 => 'ksk.reviewed_ph_by',
            10 => 'ksk.reviewed_dir_by',
            11 => 'ksk.legalized_by',
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
        $dataFilter['module'] = 'released';
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $ksk = KSK::getData($dataFilter, $settings);
        $totalData = KSK::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($ksk)) {
            foreach ($ksk as $data) {

                $releasedFormatted = $data->released_by ? '✅'.$data->released_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->released_at)->format('d F Y H:i') : '⏳ Waiting';
                $checkedFormatted = $data->checked_by ? '✅'.$data->checked_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->checked_at)->format('d F Y H:i') : '⏳ Waiting';
                $approvedFormatted = $data->approved_by ? '✅'.$data->approved_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->approved_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedDivFormatted = $data->reviewed_div_by ? '✅'.$data->reviewed_div_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->reviewed_div_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedPhFormatted = $data->reviewed_ph_by ? '✅'.$data->reviewed_ph_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->reviewed_ph_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedDirFormatted = $data->reviewed_dir_by ? '✅'.$data->reviewed_dir_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->reviewed_dir_at)->format('d F Y H:i') : '⏳ Waiting';
                $legalizedFormatted = $data->legalized_by ? '✅'.$data->legalized_by.'<br>'.Carbon::createFromFormat('Y-m-d H:i:s', $data->legalized_at)->format('d F Y H:i') : '⏳ Waiting';

                $actionFormatted = '<a href="javascript:void(0)" class="btnDetail" data-id-ksk="'.$data->id_ksk.'" data-id-departemen="'.$data->departemen_id.'" data-id-divisi="'.$data->divisi_id.'" data-parent-id="'.$data->parent_id.'" data-nama-departemen="'.$data->nama_departemen.'" data-nama-divisi="'.$data->nama_divisi.'" data-id-organisasi="'.$data->organisasi_id.'">'.$data->id_ksk.' <i class="fas fa-search"></i></a>';

                $nestedData['id_ksk'] = $actionFormatted;
                $nestedData['nama_divisi'] = $data->nama_divisi;
                $nestedData['nama_departemen'] = $data->nama_departemen;
                $nestedData['parent_name'] = $data->parent_name;
                $nestedData['release_date'] = Carbon::createFromDate($data->release_date)->format('F Y');
                $nestedData['released_by'] = $releasedFormatted;
                $nestedData['checked_by'] = $checkedFormatted;
                $nestedData['approved_by'] = $approvedFormatted;
                $nestedData['reviewed_div_by'] = $reviewedDivFormatted;
                $nestedData['reviewed_ph_by'] = $reviewedPhFormatted;
                $nestedData['reviewed_dir_by'] = $reviewedDirFormatted;
                $nestedData['legalized_by'] = $legalizedFormatted;

                $dataTable[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }

    public function get_karyawans(Request $request)
    {
        $dataValidate = [
            'id_departemen' => ['nullable','exists:departemens,id_departemen'],
            'id_divisi' => ['nullable', 'exists:divisis,id_divisi'],
            'tahun_selesai' => ['required', 'numeric'],
            'bulan_selesai' => ['required', 'numeric'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $id_departemen = $request->id_departemen;
        $id_divisi = $request->id_divisi;
        $parent_id = $request->parent_id;
        $tahun_selesai = $request->tahun_selesai;
        $bulan_selesai = $request->bulan_selesai;

        try {
            $dataFilter = [
                'id_departemen' => $id_departemen,
                'id_divisi' => $id_divisi,
                'parent_id' => $parent_id,
                'tahun_selesai' => $tahun_selesai,
                'bulan_selesai' => $bulan_selesai,
            ];

            $datas = Karyawan::getKaryawanKsk($dataFilter);
            $html = view('layouts.partials.ksk-list-karyawan-release', ['datas' => $datas])->render();
            return response()->json(['message' => 'success', 'data' => $datas, 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $dataValidate = [

            //Header
            'tahun_selesai_header' => ['required', 'date_format:Y'],
            'bulan_selesai_header' => ['required', 'date_format:m'],
            'id_departemen_header' => ['nullable', 'exists:departemens,id_departemen'],
            'id_divisi_header' => ['nullable', 'exists:divisis,id_divisi'],
            'nama_divisi_header' => ['nullable'],
            'nama_departemen_header' => ['nullable'],
            'parent_id_header' => ['required', 'exists:posisis,id_posisi'],

            //Detail
            'id_karyawan.*' => ['required', 'exists:karyawans,id_karyawan'],
            'ni_karyawan.*' => ['required'],
            'jenis_kontrak.*' => ['nullable'],
            'nama_karyawan.*' => ['nullable'],
            'posisi_id.*' => ['nullable', 'exists:posisis,id_posisi'],
            'nama_posisi.*' => ['nullable'],
            'jabatan_id.*' => ['nullable', 'exists:jabatans,id_jabatan'],
            'nama_jabatan.*' => ['nullable'],
            'jumlah_sakit.*' => ['required', 'numeric', 'min:0'],
            'jumlah_izin.*' => ['required', 'numeric', 'min:0'],
            'jumlah_alpa.*' => ['required', 'numeric', 'min:0'],
            'jumlah_surat_peringatan.*' => ['required', 'numeric', 'min:0'],

        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $tahun_selesai = $request->tahun_selesai_header;
        $bulan_selesai = $request->bulan_selesai_header;
        $id_departemen = $request->id_departemen_header;
        $id_divisi = $request->id_divisi_header;
        $nama_divisi = $request->nama_divisi_header;
        $nama_departemen = $request->nama_departemen_header;
        $parent_id = $request->parent_id_header;

        $id_karyawan = $request->id_karyawan;
        $ni_karyawan = $request->ni_karyawan;
        $nama_karyawan = $request->nama_karyawan;
        $jenis_kontrak = $request->jenis_kontrak;
        $posisi_id = $request->posisi_id;
        $nama_posisi = $request->nama_posisi;
        $jabatan_id = $request->jabatan_id;
        $nama_jabatan = $request->nama_jabatan;
        $jumlah_sakit = $request->jumlah_sakit;
        $jumlah_izin = $request->jumlah_izin;
        $jumlah_alpa = $request->jumlah_alpa;
        $jumlah_surat_peringatan = $request->jumlah_surat_peringatan;

        DB::beginTransaction();
        try {

            $approval = Approval::ApprovalDeptWithPlantHead($parent_id, auth()->user()->organisasi_id);

            $released_by = '';
            $checked_by = '';
            $approved_by = '';
            $reviewed_div_by = '';
            $reviewed_ph_by = '';
            $reviewed_dir_by = '';

            $ksk = KSK::create([
                'id_ksk' => 'KSK-' . Str::random(4).'-'. date('YmdHis'),
                'organisasi_id' => auth()->user()->organisasi_id,
                'divisi_id' => $id_divisi,
                'nama_divisi' => $nama_divisi,
                'departemen_id' => $id_departemen,
                'nama_departemen' => $nama_departemen,
                'parent_id' => $parent_id,
                'release_date' => Carbon::now()->format('Y-m-d'),
                'released_by_id' => $approval['leader'],
                'released_by' => !$approval['leader'] ? 'SYSTEM' : null,
                'released_at' => !$approval['leader'] ? Carbon::now() : null,
                'checked_by_id' => $approval['section_head'],
                'checked_by' => !$approval['section_head'] ? 'SYSTEM' : null,
                'checked_at' => !$approval['section_head'] ? Carbon::now() : null,
                'approved_by_id' => $approval['department_head'],
                'approved_by' => !$approval['department_head'] ? 'SYSTEM' : null,
                'approved_at' => !$approval['department_head'] ? Carbon::now() : null,
                'reviewed_div_by_id' => $approval['division_head'],
                'reviewed_div_by' => !$approval['division_head'] ? 'SYSTEM' : null,
                'reviewed_div_at' => !$approval['division_head'] ? Carbon::now() : null,
                'reviewed_ph_by_id' => $approval['plant_head'],
                'reviewed_ph_by' => !$approval['plant_head'] ? 'SYSTEM' : null,
                'reviewed_ph_at' => !$approval['plant_head'] ? Carbon::now() : null,
                'reviewed_dir_by_id' => $approval['director'],
            ]);

            $detail_ksk = [];
            if (!empty($id_karyawan)) {
                foreach ($id_karyawan as $index => $karyawan) {
                    $detail_ksk[] = [
                        'ksk_id' => $ksk->id_ksk,
                        'organisasi_id' => auth()->user()->organisasi_id,
                        'divisi_id' => $id_divisi,
                        'nama_divisi' => $nama_divisi,
                        'departemen_id' => $id_departemen,
                        'nama_departemen' => $nama_departemen,
                        'karyawan_id' => $karyawan,
                        'ni_karyawan' => $ni_karyawan[$index],
                        'nama_karyawan' => $nama_karyawan[$index],
                        'jumlah_sakit' => $jumlah_sakit[$index],
                        'jumlah_izin' => $jumlah_izin[$index],
                        'jumlah_alpa' => $jumlah_alpa[$index],
                        'jumlah_surat_peringatan' => $jumlah_surat_peringatan[$index],
                        'jenis_kontrak' => $jenis_kontrak[$index],
                        'posisi_id' => $posisi_id[$index],
                        'nama_posisi' => $nama_posisi[$index],
                        'jabatan_id' => $jabatan_id[$index],
                        'nama_jabatan' => $nama_jabatan[$index],
                    ];
                }

                DetailKSK::insert($detail_ksk);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Karyawan tidak ditemukan'], 402);
            }
            DB::commit();
            return response()->json(['message' => 'KSK berhasil di Release'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update_detail_ksk(Request $request, int $id)
    {
        $dataValidate = [
            'jumlah_sakit' => ['required', 'numeric', 'min:0'],
            'jumlah_izin' => ['required', 'numeric', 'min:0'],
            'jumlah_alpa' => ['required', 'numeric', 'min:0'],
            'jumlah_surat_peringatan' => ['required', 'numeric', 'min:0'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $jumlah_sakit = $request->jumlah_sakit;
        $jumlah_izin = $request->jumlah_izin;
        $jumlah_alpa = $request->jumlah_alpa;
        $jumlah_surat_peringatan = $request->jumlah_surat_peringatan;

        DB::beginTransaction();
        try {
            $detail_ksk = DetailKSK::find($id);
            $detail_ksk->jumlah_sakit = $jumlah_sakit;
            $detail_ksk->jumlah_izin = $jumlah_izin;
            $detail_ksk->jumlah_alpa = $jumlah_alpa;
            $detail_ksk->jumlah_surat_peringatan = $jumlah_surat_peringatan;
            $detail_ksk->save();

            $nama_karyawan = $detail_ksk->karyawan->nama;

            DB::commit();
            return response()->json(['message' => 'Detail KSK '.$nama_karyawan.' berhasil diperbaharui.'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_detail_ksk_release(string $id)
    {
        try {
            $detail_ksk = DetailKSK::with(['kontrak' => function ($query) {
                $query->orderBy('tanggal_selesai', 'ASC');
            }])->select('ksk_details.*', 'karyawans.tanggal_mulai', 'karyawans.tanggal_selesai', 'ksk.*', 'kontraks.tanggal_mulai as latest_kontrak_tanggal_mulai', 'kontraks.tanggal_selesai as latest_kontrak_tanggal_selesai')->where('ksk_id', $id)->leftJoin('karyawans', 'ksk_details.karyawan_id', 'karyawans.id_karyawan')->leftJoin('ksk', 'ksk_details.ksk_id', 'ksk.id_ksk')
            ->leftJoin('kontraks', function ($join) {
                $join->on('karyawans.id_karyawan', '=', 'kontraks.karyawan_id')
                    ->whereRaw('kontraks.tanggal_selesai = (select max(tanggal_selesai) from kontraks where kontraks.karyawan_id = karyawans.id_karyawan)');
            })
            ->get();
            $html = view('layouts.partials.ksk-list-karyawan-release-detail', ['datas' => $detail_ksk])->render();
            return response()->json(['message' => 'success', 'data' => $detail_ksk, 'html' => $html], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_ksk(string $id)
    {
        try {
            $ksk = KSK::find($id);
            $data = [];
            if (!empty($ksk)) {
                $releasedFormatted = $ksk->released_by ? '✅'.$ksk->released_by.'<br>'.Carbon::createFromFormat($ksk->released_at)->format('d F Y H:i') : '⏳ Waiting';
                $checkedFormatted = $ksk->checked_by ? '✅'.$ksk->checked_by.'<br>'.Carbon::createFromFormat($ksk->checked_at)->format('d F Y H:i') : '⏳ Waiting';
                $approvedFormatted = $ksk->approved_by ? '✅'.$ksk->approved_by.'<br>'.Carbon::createFromFormat($ksk->approved_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedDivFormatted = $ksk->reviewed_div_by ? '✅'.$ksk->reviewed_div_by.'<br>'.Carbon::createFromFormat($ksk->reviewed_div_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedPhFormatted = $ksk->reviewed_ph_by ? '✅'.$ksk->reviewed_ph_by.'<br>'.Carbon::createFromFormat($ksk->reviewed_ph_at)->format('d F Y H:i') : '⏳ Waiting';
                $reviewedDirFormatted = $ksk->reviewed_dir_by ? '✅'.$ksk->reviewed_dir_by.'<br>'.Carbon::createFromFormat($ksk->reviewed_dir_at)->format('d F Y H:i') : '⏳ Waiting';
                $legalizedFormatted = $ksk->legalized_by ? '✅'.$ksk->legalized_by.'<br>'.Carbon::createFromFormat($ksk->legalized_at)->format('d F Y H:i') : '⏳ Waiting';

                $data = [
                    'released_by' => $releasedFormatted,
                    'checked_by'=> $checkedFormatted,
                    'approved_by'=> $approvedFormatted,
                    'reviewed_div_by'=> $reviewedDivFormatted,
                    'reviewed_ph_by'=> $reviewedPhFormatted,
                    'reviewed_dir_by'=> $reviewedDirFormatted,
                    'legalized_by'=> $legalizedFormatted,
                ];
            }
            return response()->json(['message' => 'success', 'data' => $data], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
