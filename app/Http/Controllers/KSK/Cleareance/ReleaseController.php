<?php

namespace App\Http\Controllers\KSK\Cleareance;

use Throwable;
use Carbon\Carbon;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use App\Models\KSK\Cleareance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\KSK\CleareanceDetail;
use App\Models\KSK\CleareanceSetting;
use Illuminate\Support\Facades\Validator;

class ReleaseController extends Controller
{
    public function index()
    {
        return redirect()->route('under-maintenance');
        $deptIT = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'IT')->first();
        $deptGA = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'GA')->first();
        $deptHR = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'HR')->first();
        $deptFAT = CleareanceSetting::where('organisasi_id', auth()->user()->organisasi_id)->where('type', 'FAT')->first();

        $dataPage = [
            'pageTitle' => "KSK-E - Release Cleareance",
            'page' => 'ksk-cleareance-release',
            'deptIT' => $deptIT,
            'deptGA' => $deptGA,
            'deptHR' => $deptHR,
            'deptFAT' => $deptFAT,
        ];
        return view('pages.ksk-e.cleareance.release.index', $dataPage);
    }

    public function datatable_unreleased(Request $request)
    {
        $columns = array(
            0 => 'ksk_details.nama_karyawan',
            1 => 'ksk_details.nama_departemen',
            2 => 'ksk_details.nama_jabatan',
            3 => 'ksk_details.nama_posisi',
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

        $detailKSK = DetailKSK::getData($dataFilter, $settings);
        $totalData = DetailKSK::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($detailKSK)) {
            foreach ($detailKSK as $data) {
                $actionFormatted = '<button class="btn btn-sm btn-success btnRelease" data-id-ksk-detail="'.$data->id_ksk_detail.'" data-karyawan-id="'.$data->karyawan_id.'"><i class="fas fa-plus"></i> Buat Cleareance</button>';

                $nestedData['karyawan'] = $data->nama_karyawan;
                $nestedData['departemen'] = $data->nama_departemen;
                $nestedData['jabatan'] = $data->nama_jabatan;
                $nestedData['posisi'] = $data->nama_posisi;
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
            0 => 'cleareances.id_cleareance',
            1 => 'karyawans.nama',
            2 => 'cleareances.nama_departemen',
            3 => 'cleareances.nama_jabatan',
            4 => 'cleareances.nama_posisi',
            5 => 'cleareances.tanggal_akhir_bekerja',
            7 => 'cleareances.status',
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

        $cleareances = Cleareance::getData($dataFilter, $settings);
        $totalData = Cleareance::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($cleareances)) {
            foreach ($cleareances as $data) {
                $actionFormatted = '<a href="javascript:void(0)" class="btnDetail" data-id-cleareance="'.$data->id_cleareance.'" data-karyawan-id="'.$data->karyawan_id.'">'.$data->id_cleareance.' <i class="fas fa-search"></i></a>';
                $approvalFormatted = $data->approved.'/'.$data->detail;
                $statusFormatted = $data->status == 'Y' ? '<span class="badge badge-success">COMPLETED</span>' : '<span class="badge badge-warning">WAITING</span>';

                $nestedData['id_cleareance'] = $actionFormatted;
                $nestedData['karyawan'] = $data->nama_karyawan.'<br><small>'.$data->ni_karyawan.'</small>';
                $nestedData['departemen'] = $data->nama_departemen.'<br><small>'.$data->nama_divisi.'</small>';
                $nestedData['jabatan'] = $data->nama_jabatan;
                $nestedData['posisi'] = $data->nama_posisi;
                $nestedData['tanggal_akhir_bekerja'] = Carbon::parse($data->tanggal_akhir_bekerja)->translatedFormat('d F Y');
                $nestedData['approval'] = $approvalFormatted;
                $nestedData['status'] = $statusFormatted;

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

    public function update(Request $request, string $id_detail_ksk)
    {
        $dataValidate = [
            'atasan_langsung' => ['required', 'exists:karyawans,id_karyawan'],
            'dept_it' => ['nullable', 'exists:karyawans,id_karyawan'],
            'dept_fat' => ['nullable', 'exists:karyawans,id_karyawan'],
            'dept_ga' => ['nullable', 'exists:karyawans,id_karyawan'],
            'dept_hr' => ['nullable', 'exists:karyawans,id_karyawan'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $atasan_langsung = $request->atasan_langsung;
        $dept_it_id = $request->dept_it;
        $dept_fat_id = $request->dept_fat;
        $dept_ga_id = $request->dept_ga;
        $dept_hr_id = $request->dept_hr;

        DB::beginTransaction();
        try {
            $detailKSK = DetailKSK::findOrFail($id_detail_ksk);
            $cleareance = Cleareance::create([
                'id_cleareance' => 'CLR-' . Str::random(4).'-'. date('YmdHis'),
                'karyawan_id' => $detailKSK->karyawan_id,
                'organisasi_id' => $detailKSK->organisasi_id,
                'divisi_id' => $detailKSK->divisi_id,
                'departemen_id' => $detailKSK->departemen_id,
                'jabatan_id' => $detailKSK->jabatan_id,
                'posisi_id' => $detailKSK->posisi_id,
                'nama_divisi' => $detailKSK->nama_divisi,
                'nama_departemen' => $detailKSK->nama_departemen,
                'nama_jabatan' => $detailKSK->nama_jabatan,
                'nama_posisi' => $detailKSK->nama_posisi,
                'tanggal_akhir_bekerja' => $detailKSK->karyawan->tanggal_selesai,
            ]);


            $departments = [
                'AL' => $atasan_langsung,
                'IT' => $dept_it_id,
                'FAT' => $dept_fat_id,
                'GA' => $dept_ga_id,
                'HR' => $dept_hr_id,
            ];

            foreach ($departments as $type => $confirmed_by_id) {
                $cleareance->cleareanceDetail()->create([
                    'organisasi_id' => $cleareance->organisasi_id,
                    'type' => $type,
                    'confirmed_by_id' => $confirmed_by_id,
                    'confirmed_by' => $confirmed_by_id === null ? 'SYSTEM' : null,
                    'confirmed_at' => $confirmed_by_id === null ? Carbon::now() : null,
                    'is_clear' => $confirmed_by_id === null ? 'Y' : 'N',
                ]);
            }

            $detailKSK->update([
                'cleareance_id' => $cleareance->id_cleareance,
            ]);

            DB::commit();
            return response()->json(['message' => 'Berhasil membuat form clearance untuk karyawan'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function rollback(Request $request, int $id_cleareance_detail)
    {
        $dataValidate = [
            'confirmed_by_id' => ['nullable', 'exists:karyawans,id_karyawan'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $cleareanceDetail = CleareanceDetail::findOrFail($id_cleareance_detail);
            $cleareance = $cleareanceDetail->cleareance;

            if ($cleareance->karyawan->status_karyawan !== 'AT') {
                DB::rollBack();
                return response()->json(['message' => 'Rollback gagal, status karyawan sudah tidak aktif.'], 422);
            }

            if ($request->confirmed_by_id == null && $cleareanceDetail->type == 'AL') {
                DB::rollBack();
                return response()->json(['message' => 'Konfirmasi atasan langsung tidak boleh kosong!'], 422);
            }

            $cleareanceDetail->confirmed_by_id = $request->confirmed_by_id;
            $cleareanceDetail->confirmed_by = $request->confirmed_by_id === null ? 'SYSTEM' : null;
            $cleareanceDetail->confirmed_at = $request->confirmed_by_id === null ? Carbon::now() : null;
            $cleareanceDetail->is_clear = $request->confirmed_by_id === null ? 'Y' : 'N';
            $cleareanceDetail->save();

            if ($request->confirmed_by_id !== null) {
                $cleareance->status = 'N';
                $cleareance->save();
            }

            if ($request->confirmed_by_id !== null) {
                $html = '<p>⏳ Waiting</p>';
            } else {
                $html = '<p>
                            ✅' . $cleareanceDetail->confirmed_by . '<br>
                            <span>' . Carbon::now()->format('d F Y H:i') . '</span>
                         </p>';
            }

            DB::commit();
            return response()->json(
                [
                    'message' => 'Rollback konfirmasi berhasil dilakukan, silahkan konfirmasi ulang pada pihak yang bersangkutan',
                    'html' => $html,
                ], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
