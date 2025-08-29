<?php

namespace App\Http\Controllers\Api\APILembure;

use Throwable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lembure;
use App\Models\DetailLembur;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\SettingLembur;
use App\Models\Posisi;
use App\Models\SettingLemburKaryawan;
use App\Models\Divisi;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LembureController extends Controller
{
    // Roles yang berhak lihat/akses Pengajuan Lembur (samakan dengan web)
    private array $requiredRoles = ['atasan', 'member', 'admin-dept'];

    /**
     * Cek akses menu "Pengajuan Lembur" (untuk mobile show/hide).
     * GET /api/access/lembur-e/pengajuan-lembur
     */
    public function pengajuanLemburAccess(Request $request)
    {
        $user = $request->user();

        // Deteksi Spatie Permission (biar nggak error kalau belum dipasang)
        $hasRole = method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole($this->requiredRoles)
            : false;
        $canView = $hasRole;

        return response()->json([
            'feature' => 'lembur_e.pengajuan_lembur',
            'can_view' => $canView,
            'required_roles' => $this->requiredRoles,
            'user_roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [],
            'user_permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [],
            'web_route_name' => 'lembure.pengajuan-lembur',
            'web_route_path' => '/pengajuan-lembur',
        ], 200);
    }

    /**
     * DataTable JSON bersih untuk mobile.
     * POST /api/pengajuan-lembur-datatable
     */
    public function pengajuanLemburDatatable(Request $request)
    {
        $user = $request->user();

        // Hard-guard di dalam controller (biar satu pintu).
        $allowed = method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole($this->requiredRoles)
            : false;

        if (!$allowed && !(method_exists($user, 'can') && $user->can('lembur-e.pengajuan-lembur.view'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Mapping kolom (kompatibel dengan DataTables)
        $columns = [
            0 => 'lemburs.id_lembur',
            1 => 'lemburs.issued_date',
            3 => 'karyawans.nama',
            4 => 'lemburs.jenis_hari',
            5 => 'lemburs.total_durasi',
            6 => 'lemburs.status',
        ];

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $order = $columns[$request->input('order.0.column', 0)] ?? $columns[0];
        $dir = strtoupper($request->input('order.0.dir', 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

        $settings = [
            'start' => $start,
            'limit' => $limit,
            'dir' => $dir,
            'order' => $order,
        ];

        // Filter dasar
        $dataFilter = [];
        if ($search = $request->input('search.value')) {
            $dataFilter['search'] = $search;
        }

        // Batasi data sesuai user login (samakan perilaku web)
        $issued_by = optional(optional($user)->karyawan)->id_karyawan;
        if ($issued_by) {
            $dataFilter['issued_by'] = $issued_by;
        }

        // Ambil data lembur (pakai method model yang sudah ada)
        $totalData = $issued_by ? Lembure::where('issued_by', $issued_by)->count() : 0;
        $lembure = Lembure::getData($dataFilter, $settings);
        $totalFiltered = Lembure::countData($dataFilter);

        // ==== Ambil nama departemen pembuat (pakai Model Karyawan, tanpa definisi relasi baru) ====
        $issuedIds = collect($lembure ?? [])
            ->pluck('issued_by')
            ->filter()
            ->unique()
            ->values();

        $deptMap = [];
        if ($issuedIds->isNotEmpty()) {
            $orgId = optional($user)->organisasi_id;

            $deptRows = Karyawan::query()
                ->select([
                    'karyawans.id_karyawan',
                    'departemens.nama as nama_departemen',
                ])
                ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', '=', 'karyawan_posisi.karyawan_id')
                ->leftJoin('posisis', 'karyawan_posisi.posisi_id', '=', 'posisis.id_posisi')
                ->leftJoin('departemens', 'posisis.departemen_id', '=', 'departemens.id_departemen')
                ->when($orgId, fn($q) => $q->where('karyawans.organisasi_id', $orgId))
                ->whereIn('karyawans.id_karyawan', $issuedIds)
                ->get();

            $deptMap = $deptRows
                ->groupBy('id_karyawan')
                ->map(fn($rows) => optional($rows->first())->nama_departemen)
                ->toArray();
        }
        // ==========================================================================================

        // Susun payload JSON
        $dataTable = [];
        foreach ($lembure ?? [] as $data) {
            $jam = intdiv((int) $data->total_durasi, 60);
            $menit = (int) $data->total_durasi % 60;

            $detail = DetailLembur::where('lembur_id', $data->id_lembur)->first();
            $tanggal_lembur = $detail ? Carbon::parse($detail->rencana_mulai_lembur)->format('Y-m-d') : null;

            $issuedDepartment =
                ($data->nama_departemen ?? $data->department_name ?? null)
                ?? ($deptMap[$data->issued_by] ?? null);

            // === tambahan: info reject (note, by, at) ===
            $statusUpper = strtoupper((string) $data->status);
            $isRejected = $statusUpper === 'REJECTED'
                || (!in_array($statusUpper, ['WAITING', 'PLANNED', 'COMPLETED']) && (
                    !empty($data->rejected_by) || !empty($data->rejected_at) || !empty($data->rejected_note)
                ));

            $rejectedAtIso = $data->rejected_at ? Carbon::parse($data->rejected_at)->toISOString() : null;
            $rejectedAtHuman = $data->rejected_at ? Carbon::parse($data->rejected_at)->diffForHumans() : null;

            $dataTable[] = [
                'id_lembur' => $data->id_lembur,
                'issued_date' => Carbon::parse($data->issued_date)->format('Y-m-d'),
                'rencana_mulai_lembur' => $tanggal_lembur,
                'issued_by' => $data->nama_karyawan,
                'issued_department' => $issuedDepartment,
                'jenis_hari' => $data->jenis_hari,
                'total_durasi' => ['jam' => $jam, 'menit' => $menit],
                'status' => $data->status,
                'rejected' => $isRejected,
                'rejected_by' => $isRejected ? ($data->rejected_by ?? null) : null,
                'rejected_at' => $isRejected ? $rejectedAtIso : null,
                'rejected_at_human' => $isRejected ? $rejectedAtHuman : null,
                'rejected_note' => $isRejected ? ($data->rejected_note ?? null) : null,
                'plan_checked_by' => $data->plan_checked_by,
                'plan_approved_by' => $data->plan_approved_by,
                'plan_reviewed_by' => $data->plan_reviewed_by,
                'plan_legalized_by' => $data->plan_legalized_by,
                'actual_checked_by' => $data->actual_checked_by,
                'actual_approved_by' => $data->actual_approved_by,
                'actual_reviewed_by' => $data->actual_reviewed_by,
                'actual_legalized_by' => $data->actual_legalized_by,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => (int) $totalData,
            'recordsFiltered' => (int) $totalFiltered,
            'data' => $dataTable,
            'order' => $order,
            'dir' => $dir,
        ], 200);
    }

    public function get_attachment_lembur(Request $request, string $id_lembur)
    {
        $lembur = Lembure::find($id_lembur);
        $attachment = $lembur->attachmentLembur;
        return response()->json(['message' => 'Data LKH Berhasil Ditemukan', 'data' => $attachment], 200);
    }

    public function store(Request $request)
    {
        // --- VALIDASI ---
        $dataValidate = [
            'jenis_hari' => ['required', 'in:WD,WE'],
            'karyawan_id.*' => ['required', 'distinct'],
            'job_description.*' => ['required'],
            // tetap pakai format datetime-local agar reuse logic web
            'rencana_mulai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'before:rencana_selesai_lembur.*', 'after_or_equal:today'],
            'rencana_selesai_lembur.*' => ['required', 'date_format:Y-m-d\TH:i', 'after:rencana_mulai_lembur.*'],
        ];

        $validator = Validator::make($request->all(), $dataValidate);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->all()], 422);
        }

        $jenis_hari = $request->jenis_hari;
        $karyawan_ids = $request->karyawan_id;
        $job_descriptions = $request->job_description;
        $rencana_mulai_lemburs = $request->rencana_mulai_lembur;
        $rencana_selesai_lemburs = $request->rencana_selesai_lembur;

        $user = $request->user();
        $issued_by = optional($user->karyawan)->id_karyawan;
        $organisasi_id = $user->organisasi_id;
        $departemen_id = optional(optional($user->karyawan)->posisi[0] ?? null)->departemen_id;
        $divisi_id = optional(optional($user->karyawan)->posisi[0] ?? null)->divisi_id;

        DB::beginTransaction();
        try {
            // ====== BATAS PENGAJUAN (Setting) ======
            $onoff_batas_pengajuan = SettingLembur::where('setting_name', 'onoff_batas_pengajuan_lembur')
                ->where('organisasi_id', $organisasi_id)->value('value') ?? 'Y';

            $jam_batas_pengajuan = SettingLembur::where('setting_name', 'batas_pengajuan_lembur')
                ->where('organisasi_id', $organisasi_id)->value('value') ?? '23:59';

            if ($onoff_batas_pengajuan === 'Y' && now()->format('H:i') > $jam_batas_pengajuan) {
                DB::rollBack();
                return response()->json([
                    'message' => "Batas waktu pengajuan lembur telah berakhir ($jam_batas_pengajuan WIB), silahkan lakukan bypass ke Plant Head!"
                ], 422);
            }

            // Semua rencana HARUS di tanggal yang sama
            $date0 = Carbon::parse($rencana_mulai_lemburs[0])->format('Y-m-d');
            foreach ($rencana_mulai_lemburs as $start) {
                if (Carbon::parse($start)->format('Y-m-d') !== $date0) {
                    DB::rollBack();
                    return response()->json(['message' => 'Seluruh rencana mulai lembur harus berada pada tanggal yang sama!'], 422);
                }
            }

            // ====== HEADER ======
            /** @var \App\Models\Lembure $header */
            $header = Lembure::create([
                'id_lembur' => 'LEMBUR-' . Str::upper(Str::random(4)) . '-' . date('YmdHis'),
                'issued_by' => $issued_by,
                'issued_date' => now(),
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'divisi_id' => $divisi_id,
                'jenis_hari' => $jenis_hari,
                'status' => 'WAITING',
            ]);

            // ====== AUTO ROUTING PLAN (Checked/Approved/Reviewed/Legalized) ======
            $creator = $user;
            $creatorKaryawan = $creator->karyawan;
            $creatorNama = $creatorKaryawan->nama ?? 'SYSTEM';
            $creatorPosisi = $creatorKaryawan?->posisi ?? collect();
            $creatorJabatanId = optional($creatorPosisi->first())->jabatan_id;
            $isAdminDept = method_exists($creator, 'hasRole') ? $creator->hasRole('admin-dept') : false;

            // Ketersediaan atasan pada struktur pembuat
            $hasLeader = \App\Helpers\Approval::HasLeader($creatorPosisi);
            $hasSecHead = \App\Helpers\Approval::HasSectionHead($creatorPosisi);
            $hasDeptHead = \App\Helpers\Approval::HasDepartmentHead($creatorPosisi)
                || Posisi::where('organisasi_id', $organisasi_id)
                    ->where('divisi_id', $divisi_id)
                    ->where('departemen_id', $departemen_id)
                    ->where('jabatan_id', 2) // Dept.Head
                    ->exists();

            $leader = \App\Helpers\Approval::GetLeader($creatorPosisi);
            $leaderNama = $leader instanceof \Illuminate\Support\Collection
                ? optional($leader->first())->nama
                : optional($leader)->nama;

            // Reviewer (BOD)
            $bodNama = \App\Helpers\Approval::GetDirector($creatorPosisi)
                ?? ($this->getDefaultBODName($departemen_id, $divisi_id, $organisasi_id) ?? 'AUTO-SYSTEM-BOD');

            $now = now();
            $update = [];

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
                // Status PLANNED jika sudah Approved pada tahap Plan
                $update['status'] = $approvedBy ? 'PLANNED' : 'WAITING';
            };

            // Kategorikan pembuat
            $maker = 'unknown';
            if ($isAdminDept) {
                $maker = 'admin';
            } elseif ($creatorJabatanId == 5) {
                $maker = 'leader';
            } elseif ($creatorJabatanId == 4) {
                $maker = 'sec';
            } elseif ($creatorJabatanId == 2) {
                $maker = 'dept';
            }

            switch ($maker) {
                case 'admin':
                    if (!$hasLeader && !$hasSecHead && !$hasDeptHead) {
                        $applyAuto($creatorNama, $creatorNama, true, true);
                    } elseif (!$hasLeader && !$hasSecHead && $hasDeptHead) {
                        $applyAuto($creatorNama, null, false, false);
                    } elseif ($hasLeader && !$hasSecHead && $hasDeptHead) {
                        $applyAuto($leaderNama ?: $creatorNama, null, false, false);
                    } elseif ($hasSecHead && $hasDeptHead) {
                        $applyAuto(null, null, false, false);
                    } elseif ($hasSecHead && !$hasDeptHead) {
                        $applyAuto(null, null, false, false);
                    } else {
                        $applyAuto($creatorNama, null, false, false);
                    }
                    break;

                case 'leader':
                    if (!$hasSecHead && !$hasDeptHead) {
                        $applyAuto($creatorNama, $creatorNama, true, true);
                    } elseif (!$hasSecHead && $hasDeptHead) {
                        $applyAuto($creatorNama, null, false, false);
                    } elseif ($hasSecHead && $hasDeptHead) {
                        $applyAuto(null, null, false, false);
                    } elseif ($hasSecHead && !$hasDeptHead) {
                        $applyAuto(null, null, false, false);
                    } else {
                        $applyAuto($creatorNama, null, false, false);
                    }
                    break;

                case 'sec':
                    if (!$hasDeptHead) {
                        $applyAuto($creatorNama, $creatorNama, true, true);
                    } else {
                        $applyAuto($creatorNama, null, false, false);
                    }
                    break;

                case 'dept':
                    $applyAuto($creatorNama, $creatorNama, true, true);
                    break;

                default:
                    $applyAuto($creatorNama, null, false, false);
                    break;
            }

            if (!empty($update)) {
                $header->update($update);
            }

            // ====== DETAIL ======
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
                    return response()->json(['message' => $karyawan->nama . ' belum memiliki setting gaji lembur!'], 422);
                }

                $pembagi_upah = SettingLembur::where('setting_name', 'pembagi_upah_lembur_harian')
                    ->where('organisasi_id', $karyawan->user->organisasi_id)
                    ->value('value');

                $startPlan = $this->pembulatan_menit_ke_bawah($rencana_mulai_lemburs[$i]);
                $endPlan = $this->pembulatan_menit_ke_bawah($rencana_selesai_lemburs[$i]);

                // Cek duplikat pada rentang tsb (sudah approved plan+actual)
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
                    return response()->json(['message' => $karyawan->nama . ' sudah memiliki data lembur pada range tanggal yang direncanakan'], 422);
                }

                $durIstirahat = $this->overtime_resttime_per_minutes($startPlan, $endPlan, $karyawan->user->organisasi_id);
                $durasi = $this->calculate_overtime_per_minutes($startPlan, $endPlan, $karyawan->user->organisasi_id);

                if ($durasi < 60) {
                    DB::rollBack();
                    return response()->json(['message' => 'Durasi lembur ' . $karyawan->nama . ' kurang dari 1 jam, tidak perlu menginput SPL'], 422);
                }

                if (!$karyawan->posisi()->exists()) {
                    DB::rollBack();
                    return response()->json(['message' => $karyawan->nama . ' belum memiliki posisi, Hubungi HRD untuk setting posisi karyawan!'], 422);
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
            return response()->json([
                'message' => 'Lembur Berhasil Dibuat',
                'id_lembur' => $header->id_lembur,
                'status' => $header->status,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error in store function: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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

    public function review_lembur_datatable(Request $request)
    {
        $columns = array(
            1 => 'subquery.tanggal_lembur',
            2 => 'subquery.departemen',
            3 => 'subquery.status',
            4 => 'subquery.total_nominal_lembur',
            5 => 'subquery.total_durasi_lembur',
            6 => 'subquery.total_karyawan',
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
            // Kalau sudah array dari Postman → langsung dipakai
            if (is_array($filterDepartemen)) {
                $dataFilter['departemen'] = $filterDepartemen;
            } else {
                // Kalau integer atau string "2,3" → paksa jadi array integer
                $dataFilter['departemen'] = array_map('intval', explode(',', $filterDepartemen));
            }
        } elseif (!empty($departemen_ids)) {
            $dataFilter['departemen'] = (array) $departemen_ids; // selalu array
        } else {
            // Kalau user gak punya departemen → tetap array kosong
            $dataFilter['departemen'] = [];
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

    public function getDataKaryawanLembur(Request $request)
    {
        $user = $request->user();
        $karyawan = optional($user)->karyawan;
        $orgId = $user->organisasi_id;

        // param pencarian & paging
        $search = trim((string) $request->input('search', ''));
        $perPage = (int) $request->input('per_page', 30);
        $page = (int) $request->input('page', 1);

        // role/level
        $positions = $karyawan?->posisi ?? collect();             // collection Posisi
        $deptIds = $positions->pluck('departemen_id')->filter()->unique()->values()->all();
        $firstPos = $positions->first();
        $isLeader = (int) ($firstPos->jabatan_id ?? 0) === 5;
        $isAdminDept = method_exists($user, 'hasRole') ? $user->hasRole('admin-dept') : false;

        // base query
        $query = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi'
        )
            ->aktif()
            ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', '=', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', '=', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', '=', 'departemens.id_departemen')
            // hanya yang punya setting lembur
            ->rightJoin('setting_lembur_karyawans', 'karyawans.id_karyawan', '=', 'setting_lembur_karyawans.karyawan_id')
            ->whereNull('posisis.deleted_at');

        // scope organisasi
        if ($orgId) {
            $query->where('karyawans.organisasi_id', $orgId);
        }

        // filter akses
        if ($isLeader || $isAdminDept) {
            // semua anggota di departemen2 si pembuat
            if (!empty($deptIds)) {
                $query->whereIn('posisis.departemen_id', $deptIds);
            } else {
                // fallback kalau pembuat tidak punya departemen terdefinisi
                $query->where('karyawans.id_karyawan', $karyawan->id_karyawan ?? '');
            }
        } else {
            // non leader/admin-dept: hanya dirinya
            $query->where('karyawans.id_karyawan', $karyawan->id_karyawan ?? '');
        }

        // pencarian (id atau nama)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                // pakai ILIKE (Postgres) — kalau MySQL otomatis jadi LIKE biasa
                $q->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%");
            });
        }

        $query->groupBy('karyawans.id_karyawan', 'karyawans.nama', 'posisis.nama');

        // paging Select2-friendly
        $data = $query->simplePaginate($perPage, ['*'], 'page', max($page, 1));

        $results = [
            'results' => collect($data->items())->map(function ($row) {
                return [
                    'id' => $row->id_karyawan,
                    'text' => $row->nama,
                    'posisi' => $row->posisi,   // tambahan info jika mau ditampilkan
                ];
            })->values(),
            'pagination' => [
                'more' => !empty($data->nextPageUrl()),
            ],
        ];

        return response()->json($results, 200);
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

    public function export_slip_lembur(Request $request)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $periode = $request->periode_slip;
        $karyawan = auth()->user()->karyawan;
        $id_karyawan = $karyawan->id_karyawan;

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
            'JUMLAH'
        ];
        $start = Carbon::createFromFormat('Y-m', $periode)->startOfMonth()->toDateString();
        $end = Carbon::createFromFormat('Y-m', $periode)->endOfMonth()->toDateString();
        $month = Carbon::createFromFormat('Y-m', $periode)->format('m');
        $year = Carbon::createFromFormat('Y-m', $periode)->format('Y');

        $columns = range('A', 'L');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lembur_karyawan = DetailLembur::leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')->where('detail_lemburs.karyawan_id', $id_karyawan)->whereMonth('detail_lemburs.aktual_mulai_lembur', $month)->whereYear('detail_lemburs.aktual_mulai_lembur', $year)->whereNotNull('lemburs.actual_legalized_by')
        ->where('lemburs.status', 'COMPLETED')->first();
        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $id_karyawan)->first();
        $pembagi_upah_lembur_harian = SettingLembur::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'pembagi_upah_lembur_harian')->first()->value;
        $upah_lembur_per_jam_setting = $lembur_karyawan ? $lembur_karyawan->gaji_lembur / $lembur_karyawan->pembagi_upah_lembur : ($setting_lembur_karyawan ? $setting_lembur_karyawan->gaji / $pembagi_upah_lembur_harian : 0);
        // TEXT "SLIP LEMBUR BULAN INI"
        $sheet->mergeCells('A'.$row.':F'.$row+1);
        $sheet->setCellValue('A'.$row, 'SLIP LEMBUR BULAN '.strtoupper(Carbon::createFromFormat('Y-m', $periode)->format('F Y')));
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
        $sheet->setCellValue('D'.$row, $karyawan->nama);
        $sheet->setCellValue('B'.$row+1, 'NIK');
        $sheet->setCellValue('C'.$row+1, ':');
        $sheet->setCellValue('D'.$row+1, $karyawan->ni_karyawan);
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
            $slipLemburs = DetailLembur::getSlipLemburPerDepartemen($id_karyawan, $date, $organisasi_id);
            if($slipLemburs->count() > 0){
                foreach ($slipLemburs as $index => $slipLembur){
                    $upah_lembur_per_jam = $slipLembur ? $slipLembur->gaji_lembur / $slipLembur->pembagi_upah_lembur : $upah_lembur_per_jam_setting;
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

                    if($slipLembur->keterangan){
                        if (substr($slipLembur->keterangan, 0, 6) === 'BYPASS') {
                            $keterangan = substr($slipLembur->keterangan, 7);
                        } else {
                            $keterangan = '';
                        }
                    } else {
                        $keterangan = '';
                    }

                    $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
                    $sheet->setCellValue('D'.$row, Carbon::parse($slipLembur->aktual_mulai_lembur)->format('H:i'));
                    $sheet->setCellValue('E'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->format('H:i'));
                    $sheet->setCellValue('F'.$row, number_format($slipLembur->durasi_istirahat / 100 , 2));
                    $sheet->setCellValue('G'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->subMinutes($slipLembur->durasi_istirahat)->format('H:i'));
                    $sheet->setCellValue('H'.$row, number_format($slipLembur->durasi / 60, 2));
                    $sheet->setCellValue('I'.$row, number_format($slipLembur->durasi_konversi_lembur / 60, 2));
                    $sheet->setCellValue('J'.$row, $slipLembur->uang_makan);
                    $sheet->setCellValue('K'.$row, 'Rp '. number_format($slipLembur->nominal, 0, ',', '.'));
                    $sheet->setCellValue('L'.$row, $keterangan);

                        //STYLE CELL
                    $sheet->getStyle('C'.$row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle('J'.$row.':J'.$row)->applyFromArray([
                        'font' => [
                            'color' => [
                                'argb' => 'FFFF0000',
                            ],
                        ],
                    ]);
                    $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    if ($slipLemburs->count() > 1 && $index == 0) {
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
                        $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['argb' => 'FF000000'],
                                ],
                            ],
                        ]);
                        $row++;
                    }
                }

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
                $sheet->setCellValue('K'.$row, 'Rp');
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
            $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
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
        $sheet->setCellValue('K'.$row, 'Rp ' . number_format($total_spl, 0, ',', '.'));
        $sheet->setCellValue('J'.$row+1, 'SESUAI SPL');
        $sheet->setCellValue('K'.$row+1, 'Rp ' . number_format($total_spl, 0, ',', '.'));
        $sheet->getStyle('H'.$row.':K'.$row)->applyFromArray([
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
        $sheet->getStyle('J'.($row+1).':K'.($row+1))
        ->applyFromArray([
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

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Slip Pembayaran Lembur - '.Carbon::createFromFormat('Y-m', $periode)->format('F Y').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }
}
