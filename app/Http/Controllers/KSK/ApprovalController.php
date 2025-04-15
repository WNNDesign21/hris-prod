<?php

namespace App\Http\Controllers\KSK;

use Throwable;
use Carbon\Carbon;
use App\Models\KSK\KSK;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\KSK\ChangeHistoryKSK;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return redirect()->route('under-maintenance');
        $dataPage = [
            'pageTitle' => "KSK-E - Approval KSK",
            'page' => 'ksk-approval',
        ];
        return view('pages.ksk-e.approval.index', $dataPage);
    }

    public function datatable_must_approved(Request $request)
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
        $dataFilter['module'] = 'approval-must-approved';
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

                $actionFormatted = '<a href="javascript:void(0)" class="btnApproved" data-id-ksk="'.$data->id_ksk.'" data-id-departemen="'.$data->departemen_id.'" data-id-divisi="'.$data->divisi_id.'" data-parent-id="'.$data->parent_id.'" data-nama-departemen="'.$data->nama_departemen.'" data-nama-divisi="'.$data->nama_divisi.'" data-id-organisasi="'.$data->organisasi_id.'">'.$data->id_ksk.' <i class="fas fa-search"></i></a>';

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

    public function datatable_history(Request $request)
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
        $dataFilter['module'] = 'approval-history';
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

    public function approve(Request $request, string $id)
    {
        $dataValidate = [
            'id_ksk_detail' => ['required', 'array'],
            'id_ksk_detail.*' => ['required', 'numeric', 'exists:ksk_details,id_ksk_detail'],
            'status_ksk' => ['required','array'],
            'status_ksk.*' => ['required', 'in:PPJ,PHK,TTP'],
            'durasi_renewal' => ['required','array'],
            'durasi_renewal.*' => ['required', 'numeric', 'min:0'],
            'reason' => ['array'],
            'reason.*' => ['nullable', 'string'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $posisis = auth()->user()->karyawan->posisi->pluck('id_posisi')->toArray();
        DB::beginTransaction();
        try {
            $ksk = KSK::find($id);
            $countKSKDetail = count($request->id_ksk_detail);
            $countChangeHistory = ChangeHistoryKSK::whereIn('ksk_detail_id', $request->id_ksk_detail)->where('changed_by_id', auth()->user()->karyawan->id_karyawan)->count();

            if ($countKSKDetail !== $countChangeHistory) {
                DB::rollback();
                return response()->json(['message' => 'Silahkan klik save change pada setiap data KSK terlebih dahulu sebelum melakukan Konfirmasi!'], 402);
            }

            if (in_array($ksk->released_by_id, $posisis)) {
                $ksk->released_by = auth()->user()->karyawan->nama;
                $ksk->released_at = Carbon::now();
            }

            if (in_array($ksk->checked_by_id, $posisis)) {
                $ksk->checked_by = auth()->user()->karyawan->nama;
                $ksk->checked_at = Carbon::now();
            }

            if (in_array($ksk->approved_by_id, $posisis)) {
                $ksk->approved_by = auth()->user()->karyawan->nama;
                $ksk->approved_at = Carbon::now();
            }

            if (in_array($ksk->reviewed_div_by_id, $posisis)) {
                $ksk->reviewed_div_by = auth()->user()->karyawan->nama;
                $ksk->reviewed_div_at = Carbon::now();
            }

            if (in_array($ksk->reviewed_ph_by_id, $posisis)) {
                $ksk->reviewed_ph_by = auth()->user()->karyawan->nama;
                $ksk->reviewed_ph_at = Carbon::now();
            }

            if (in_array($ksk->reviewed_dir_by_id, $posisis)) {
                $ksk->reviewed_dir_by = auth()->user()->karyawan->nama;
                $ksk->reviewed_dir_at = Carbon::now();
            }

            $ksk->save();
            DB::commit();
            return response()->json(['message' => 'KSK berhasil di approve.'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function legalize(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $ksk = KSK::find($id);
            if (!$ksk->reviewed_dir_by_id) {
                return response()->json(['message' => 'KSK belum di review oleh Direksi.'], 402);
            }

            $ksk_details = $ksk->detailKSK;
            if ($ksk_details){
                foreach ($ksk_details as $ksk_detail) {
                    if ($ksk_detail->status_ksk != 'PHK') {
                        $tanggal_renewal_kontrak = Carbon::parse($ksk_detail->karyawan->tanggal_selesai)->addDay();
                        $ksk_detail->tanggal_renewal_kontrak = $tanggal_renewal_kontrak;
                        $ksk_detail->save();
                    }
                }
            }

            $ksk->legalized_by = 'HRD & GA';
            $ksk->legalized_at = Carbon::now();
            $ksk->save();
            DB::commit();
            return response()->json(['message' => 'KSK berhasil di approve.'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update_detail_ksk(Request $request, int $id)
    {
        $dataValidate = [
            'status_ksk' => ['required', 'in:PPJ,PHK,TTP'],
            'durasi_renewal' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        $status_ksk = $request->status_ksk;
        $durasi_renewal = $request->durasi_renewal;
        $reason = $request->reason;
        $changed_by_id = auth()->user()->karyawan->id_karyawan;
        $changed_by = auth()->user()->karyawan->nama;
        DB::beginTransaction();
        try {
            $detail_ksk = DetailKSK::find($id);

            // Update Change History
            $changeHistoryExists = ChangeHistoryKSK::where('ksk_detail_id', $id)
                ->where('changed_by_id', $changed_by_id)
                ->exists();

            if ($changeHistoryExists) {
                $changeHistory = ChangeHistoryKSK::where('ksk_detail_id', $id)->where('changed_by_id', $changed_by_id)->first();
                $changeHistory->status_ksk_before = $changeHistory->status_ksk_after;
                $changeHistory->status_ksk_after = $status_ksk;
                $changeHistory->durasi_before = $changeHistory->durasi_after;
                $changeHistory->durasi_after = $durasi_renewal;
                $changeHistory->reason = $reason;
                $changeHistory->save();
            } else {
                ChangeHistoryKSK::create([
                    'ksk_detail_id' => $id,
                    'changed_by_id' => $changed_by_id,
                    'changed_by' => $changed_by,
                    'changed_at' => now(),
                    'reason' => $reason,
                    'status_ksk_before' => $status_ksk,
                    'status_ksk_after' => $status_ksk,
                    'durasi_before' => $durasi_renewal,
                    'durasi_after' => $durasi_renewal,
                ]);
            }

            $detail_ksk->status_ksk = $status_ksk;
            $detail_ksk->durasi_renewal = $durasi_renewal;
            $detail_ksk->save();

            DB::commit();
            return response()->json(['message' => 'Detail KSK '.$detail_ksk->nama_karyawan.' berhasil diperbaharui.'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
