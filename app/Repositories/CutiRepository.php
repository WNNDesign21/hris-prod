<?php

namespace App\Repositories;

use App\Models\Cutie;
use App\Models\Posisi;
use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\JenisCuti;
use App\Models\ApprovalCuti;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Collection;

class CutiRepository
{
    public function getById(int $id, array $fields = ['*'])
    {
        return Cutie::select($fields)->with('approval')->findOrFail($id);
    }

    public function getKaryawanPengganti(string $id)
    {
        $departemen = Karyawan::find($id)->posisi()->value('departemen_id');
        $karyawanPengganti = Karyawan::select('id_karyawan as id', 'nama as text')
            ->whereHas('posisi', function($query) use ($departemen) {
            $query->where('departemen_id', $departemen);
            })
            ->where('id_karyawan', '!=', $id)
            ->get()
            ->toArray();
        return $karyawanPengganti;
    }

    public function countApprovalCuti()
    {
        return ApprovalCuti::count();
    }

    private function _queryMustApproved(array $dataFilter)
    {
        $subQuery = DB::table('karyawan_posisi')
            ->select('karyawan_id', 'posisi_id')
            ->whereNull('deleted_at')
            ->distinct('karyawan_id');

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $getJenisCuti = JenisCuti::select("id_jenis_cuti as jc_id", "jenis as jenis_cuti_khusus");
        $data = ApprovalCuti::select(
            'approval_cutis.id_approval_cuti',
            'approval_cutis.checked1_for',
            'approval_cutis.checked1_by as approval_checked1_by',
            'approval_cutis.checked1_karyawan_id',
            'approval_cutis.checked2_for',
            'approval_cutis.checked2_by as approval_checked2_by',
            'approval_cutis.checked2_karyawan_id',
            'approval_cutis.approved_for',
            'approval_cutis.approved_by as approval_approved_by',
            'approval_cutis.approved_karyawan_id',
            'cutis.id_cuti',
            'cutis.created_at',
            'cutis.rencana_mulai_cuti',
            'cutis.rencana_selesai_cuti',
            'cutis.aktual_mulai_cuti',
            'cutis.aktual_selesai_cuti',
            'cutis.durasi_cuti',
            'cutis.jenis_cuti',
            'cutis.alasan_cuti',
            'cutis.checked1_at',
            'cutis.checked2_at',
            'cutis.approved_at',
            'cutis.legalized_at',
            'cutis.checked1_by',
            'cutis.checked2_by',
            'cutis.approved_by',
            'cutis.legalized_by',
            'cutis.rejected_by',
            'cutis.rejected_at',
            'cutis.rejected_note',
            'cutis.status_dokumen',
            'cutis.status_cuti',
            'cutis.attachment',
            'kp.nama_pengganti as nama_pengganti',
            'jc.jenis_cuti_khusus as jenis_cuti_khusus',
            'karyawans.nama as nama_karyawan',
            'cutis.karyawan_id',
            'karyawan_pengganti_id',
            'departemens.nama as nama_departemen',
        )
        ->leftJoin('cutis', 'approval_cutis.cuti_id', 'cutis.id_cuti')
        ->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
            $joinKaryawanPengganti->on('cutis.karyawan_pengganti_id', 'kp.kp_id');
        })
        ->leftJoinSub($getJenisCuti, 'jc', function (JoinClause $joinJenisCuti) {
            $joinJenisCuti->on('cutis.jenis_cuti_id', 'jc.jc_id');
        })
        ->leftJoinSub($subQuery, 'distinct_karyawan_posisi', function ($join) {
            $join->on('cutis.karyawan_id', 'distinct_karyawan_posisi.karyawan_id');
        })
        ->leftJoin('posisis', 'distinct_karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen');

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('cutis.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (auth()->user()->hasRole('personalia')) {
            $data->where(function ($query) {
                $query->whereNot('cutis.status_cuti', 'CANCELED')
                ->orWhereNull('cutis.status_cuti');
            })->where('cutis.status_dokumen', 'WAITING')
            ->whereNotNull('cutis.approved_by')
            ->whereNull('cutis.rejected_by');
        } elseif (auth()->user()->hasRole('atasan')) {
            if (isset($dataFilter['member_posisi_id'])) {
                $my_posisi = auth()->user()->karyawan->posisi;
                if($my_posisi->count() > 1){
                    $my_posisi = $my_posisi->pluck('id_posisi')->toArray();
                } else {
                    $my_posisi = [$my_posisi->first()->id_posisi];
                }

                $data->where('cutis.status_dokumen', 'WAITING');
                $data->where(function ($query) {
                     $query->whereNot('cutis.status_cuti', 'CANCELED')
                    ->orWhereNull('cutis.status_cuti');
                });

                $data->where(function($query) use ($my_posisi){
                    $query->where(function($query) use ($my_posisi){
                        $query->whereIn('approval_cutis.checked1_for', $my_posisi)
                            ->whereNull('approval_cutis.checked1_by');
                    })->orWhere(function($query) use ($my_posisi){
                        $query->whereIn('approval_cutis.checked2_for', $my_posisi)
                            ->whereNull('approval_cutis.checked2_by');
                    })->orWhere(function($query) use ($my_posisi){
                        $query->whereIn('approval_cutis.approved_for', $my_posisi)
                            ->whereNull('approval_cutis.approved_by');
                    });
                });
            }
        }

        if(isset($dataFilter['departemen'])) {
            $data->where('departemens.id_departemen', $dataFilter['departemen']);
        }

        if(isset($dataFilter['jenisCuti'])) {
            $data->where('jenis_cuti', $dataFilter['jenisCuti']);
        }

        if(isset($dataFilter['durasi'])) {
            $data->where('durasi_cuti', $dataFilter['durasi']);
        }

        if(isset($dataFilter['statusCuti'])) {
            $data->where('status_cuti', $dataFilter['statusCuti']);
        }

        if(isset($dataFilter['statusDokumen'])) {
            $data->where('status_dokumen', $dataFilter['statusDokumen']);
        }

        if(isset($dataFilter['nama'])) {
            $data->where('karyawans.nama', 'ILIKE' , '%'.$dataFilter['nama'].'%');
        }

        if(isset($dataFilter['rencanaMulai'])) {
            $data->whereYear('rencana_mulai_cuti', Carbon::parse($dataFilter['rencanaMulai'])->year)
                ->whereMonth('rencana_mulai_cuti', Carbon::parse($dataFilter['rencanaMulai'])->month);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('cutis.rencana_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.rencana_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.aktual_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.aktual_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.durasi_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.jenis_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.alasan_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.checked1_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.checked2_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.approved_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.legalized_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.rejected_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.status_dokumen', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.status_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jc.jenis_cuti_khusus', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.created_at', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('kp.nama_pengganti', 'ILIKE', "%{$search}%");
            });
        }

        if(auth()->user()->hasRole('personalia')){
            $data->orderByRaw("CASE
                WHEN cutis.approved_by IS NOT NULL AND cutis.legalized_by IS NULL THEN 0
                ELSE 1
            END DESC");
        } elseif (auth()->user()->hasRole('atasan')) {
            $data->orderByRaw("CASE
                WHEN approval_cutis.checked1_by IS NULL AND cutis.legalized_by IS NULL AND (cutis.status_cuti IS NULL OR cutis.status_cuti != 'CANCELED') AND cutis.status_dokumen != 'REJECTED' THEN 0
                WHEN approval_cutis.checked2_by IS NULL AND cutis.legalized_by IS NULL AND (cutis.status_cuti IS NULL OR cutis.status_cuti != 'CANCELED') AND cutis.status_dokumen != 'REJECTED' THEN 1
                WHEN approval_cutis.approved_by IS NULL AND cutis.legalized_by IS NULL AND (cutis.status_cuti IS NULL OR cutis.status_cuti != 'CANCELED') AND cutis.status_dokumen != 'REJECTED' THEN 2
                ELSE 3
            END ASC");
        }

        $data->groupBy('cutis.id_cuti', 'cutis.created_at', 'cutis.rencana_mulai_cuti', 'cutis.rencana_selesai_cuti', 'cutis.aktual_mulai_cuti', 'cutis.aktual_selesai_cuti', 'cutis.durasi_cuti', 'cutis.jenis_cuti', 'cutis.alasan_cuti', 'cutis.checked1_at', 'cutis.checked2_at',  'cutis.approved_at', 'cutis.legalized_at','cutis.checked1_by', 'cutis.checked2_by',  'cutis.approved_by', 'cutis.legalized_by', 'cutis.status_dokumen', 'cutis.status_cuti', 'cutis.attachment', 'kp.nama_pengganti', 'jc.jenis_cuti_khusus', 'karyawans.nama', 'cutis.karyawan_id', 'cutis.karyawan_pengganti_id','departemens.nama', 'approval_cutis.id_approval_cuti', 'approval_cutis.checked1_for', 'approval_cutis.checked1_by', 'approval_cutis.checked1_karyawan_id', 'approval_cutis.checked2_for', 'approval_cutis.checked2_by', 'approval_cutis.checked2_karyawan_id', 'approval_cutis.approved_for', 'approval_cutis.approved_by', 'approval_cutis.approved_karyawan_id');

        $result = DB::table(DB::raw('(' . $data->toSql() . ') as sub'));
        $result->mergeBindings($data->getQuery());
        return $result;
    }

    public function getMustApproved(array $dataFilter, array $settings)
    {
        return $this->_queryMustApproved($dataFilter)
            ->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public function countMustApproved(array $dataFilter)
    {
        return $this->_queryMustApproved($dataFilter)->count();
    }

    private function _queryAllData(array $dataFilter)
    {
        $subQuery = DB::table('karyawan_posisi')
            ->select('karyawan_id', 'posisi_id')
            ->whereNull('deleted_at')
            ->distinct('karyawan_id');

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $getJenisCuti = JenisCuti::select("id_jenis_cuti as jc_id", "jenis as jenis_cuti_khusus");
        $data = ApprovalCuti::select(
            'approval_cutis.id_approval_cuti',
            'approval_cutis.checked1_for',
            'approval_cutis.checked1_by as approval_checked1_by',
            'approval_cutis.checked1_karyawan_id',
            'approval_cutis.checked2_for',
            'approval_cutis.checked2_by as approval_checked2_by',
            'approval_cutis.checked2_karyawan_id',
            'approval_cutis.approved_for',
            'approval_cutis.approved_by as approval_approved_by',
            'approval_cutis.approved_karyawan_id',
            'cutis.id_cuti',
            'cutis.created_at',
            'cutis.rencana_mulai_cuti',
            'cutis.rencana_selesai_cuti',
            'cutis.aktual_mulai_cuti',
            'cutis.aktual_selesai_cuti',
            'cutis.durasi_cuti',
            'cutis.jenis_cuti',
            'cutis.alasan_cuti',
            'cutis.checked1_at',
            'cutis.checked2_at',
            'cutis.approved_at',
            'cutis.legalized_at',
            'cutis.checked1_by',
            'cutis.checked2_by',
            'cutis.approved_by',
            'cutis.legalized_by',
            'cutis.rejected_by',
            'cutis.rejected_at',
            'cutis.rejected_note',
            'cutis.status_dokumen',
            'cutis.status_cuti',
            'cutis.attachment',
            'kp.nama_pengganti as nama_pengganti',
            'jc.jenis_cuti_khusus as jenis_cuti_khusus',
            'karyawans.nama as nama_karyawan',
            'cutis.karyawan_id',
            'karyawan_pengganti_id',
            'departemens.nama as nama_departemen'
        )
        ->leftJoin('cutis', 'approval_cutis.cuti_id', 'cutis.id_cuti')
        ->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
            $joinKaryawanPengganti->on('cutis.karyawan_pengganti_id', 'kp.kp_id');
        })
        ->leftJoinSub($getJenisCuti, 'jc', function (JoinClause $joinJenisCuti) {
            $joinJenisCuti->on('cutis.jenis_cuti_id', 'jc.jc_id');
        })
        ->leftJoinSub($subQuery, 'distinct_karyawan_posisi', function ($join) {
            $join->on('cutis.karyawan_id', 'distinct_karyawan_posisi.karyawan_id');
        })
        ->leftJoin('posisis', 'distinct_karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen');

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('cutis.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $my_posisi = auth()->user()->karyawan->posisi;
            if($my_posisi->count() > 1){
                $my_posisi = $my_posisi->pluck('id_posisi')->toArray();
            } else {
                $my_posisi = [$my_posisi->first()->id_posisi];
            }

            $data->where(function($query) use ($my_posisi){
                $query->where(function($query) use ($my_posisi){
                    $query->whereIn('approval_cutis.checked1_for', $my_posisi);
                })->orWhere(function($query) use ($my_posisi){
                    $query->whereIn('approval_cutis.checked2_for', $my_posisi);
                })->orWhere(function($query) use ($my_posisi){
                    $query->whereIn('approval_cutis.approved_for', $my_posisi);
                });
            });
        }

        if(isset($dataFilter['departemen'])) {
            $data->where('departemens.id_departemen', $dataFilter['departemen']);
        }

        if(isset($dataFilter['jenisCuti'])) {
            $data->where('jenis_cuti', $dataFilter['jenisCuti']);
        }

        if(isset($dataFilter['durasi'])) {
            $data->where('durasi_cuti', $dataFilter['durasi']);
        }

        if(isset($dataFilter['statusCuti'])) {
            $data->where('status_cuti', $dataFilter['statusCuti']);
        }

        if(isset($dataFilter['statusDokumen'])) {
            $data->where('status_dokumen', $dataFilter['statusDokumen']);
        }

        if(isset($dataFilter['nama'])) {
            $data->where('karyawans.nama', 'ILIKE' , '%'.$dataFilter['nama'].'%');
        }

        if(isset($dataFilter['rencanaMulai'])) {
            $data->whereYear('rencana_mulai_cuti', Carbon::parse($dataFilter['rencanaMulai'])->year)
                ->whereMonth('rencana_mulai_cuti', Carbon::parse($dataFilter['rencanaMulai'])->month);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('cutis.rencana_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.rencana_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.aktual_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.aktual_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.durasi_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.jenis_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.alasan_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.checked1_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.checked2_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.approved_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.legalized_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.rejected_at', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.status_dokumen', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.status_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jc.jenis_cuti_khusus', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.created_at', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('kp.nama_pengganti', 'ILIKE', "%{$search}%");
            });
        }

        $data->orderByDesc('cutis.updated_at');
        $data->groupBy('cutis.id_cuti', 'cutis.created_at', 'cutis.rencana_mulai_cuti', 'cutis.rencana_selesai_cuti', 'cutis.aktual_mulai_cuti', 'cutis.aktual_selesai_cuti', 'cutis.durasi_cuti', 'cutis.jenis_cuti', 'cutis.alasan_cuti', 'cutis.checked1_at', 'cutis.checked2_at',  'cutis.approved_at', 'cutis.legalized_at','cutis.checked1_by', 'cutis.checked2_by',  'cutis.approved_by', 'cutis.legalized_by', 'cutis.status_dokumen', 'cutis.status_cuti', 'cutis.attachment', 'kp.nama_pengganti', 'jc.jenis_cuti_khusus', 'karyawans.nama', 'cutis.karyawan_id', 'cutis.karyawan_pengganti_id','departemens.nama', 'approval_cutis.id_approval_cuti', 'approval_cutis.checked1_for', 'approval_cutis.checked1_by', 'approval_cutis.checked1_karyawan_id', 'approval_cutis.checked2_for', 'approval_cutis.checked2_by', 'approval_cutis.checked2_karyawan_id', 'approval_cutis.approved_for', 'approval_cutis.approved_by', 'approval_cutis.approved_karyawan_id');

        $result = DB::table(DB::raw('(' . $data->toSql() . ') as sub'));
        $result->mergeBindings($data->getQuery());
        return $result;
    }

    public function getAllData(array $dataFilter, array $settings)
    {
        return $this->_queryAllData($dataFilter)
            ->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
        }

    public function countAllData(array $dataFilter)
    {
        return $this->_queryAllData($dataFilter)->count();
    }

    private function _queryPengajuan($dataFilter)
    {
        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $getJenisCuti = JenisCuti::select("id_jenis_cuti as jc_id", "jenis as jenis_cuti_khusus");
        $data = Cutie::select(
            'id_cuti',
            'cutis.created_at',
            'rencana_mulai_cuti',
            'rencana_selesai_cuti',
            'aktual_mulai_cuti',
            'aktual_selesai_cuti',
            'durasi_cuti',
            'jenis_cuti',
            'alasan_cuti',
            'checked1_at',
            'checked2_at',
            'approved_at',
            'legalized_at',
            'checked1_by',
            'checked2_by',
            'approved_by',
            'legalized_by',
            'rejected_by',
            'rejected_at',
            'rejected_note',
            'status_dokumen',
            'status_cuti',
            'attachment',
            'kp.nama_pengganti as nama_pengganti',
            'jc.jenis_cuti_khusus as jenis_cuti_khusus',
            'karyawans.nama as nama_karyawan',
            'cutis.karyawan_id',
            'karyawan_pengganti_id',
            'departemens.nama as nama_departemen'
            )
            ->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('cutis.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoinSub($getJenisCuti, 'jc', function (JoinClause $joinJenisCuti) {
                $joinJenisCuti->on('cutis.jenis_cuti_id', 'jc.jc_id');
            })
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen');

        $data->where('cutis.karyawan_id', auth()->user()->karyawan->id_karyawan);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('rencana_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('rencana_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('aktual_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('aktual_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('durasi_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('jenis_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('alasan_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('checked1_at', 'ILIKE', "%{$search}%")
                    ->orWhere('checked2_at', 'ILIKE', "%{$search}%")
                    ->orWhere('approved_at', 'ILIKE', "%{$search}%")
                    ->orWhere('legalized_at', 'ILIKE', "%{$search}%")
                    ->orWhere('rejected_at', 'ILIKE', "%{$search}%")
                    ->orWhere('status_dokumen', 'ILIKE', "%{$search}%")
                    ->orWhere('status_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('jc.jenis_cuti_khusus', 'ILIKE', "%{$search}%")
                    ->orWhere('cutis.created_at', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('kp.nama_pengganti', 'ILIKE', "%{$search}%");
            });
        }

        $data->groupBy('id_cuti', 'cutis.created_at', 'rencana_mulai_cuti', 'rencana_selesai_cuti', 'aktual_mulai_cuti', 'aktual_selesai_cuti', 'durasi_cuti', 'jenis_cuti', 'alasan_cuti', 'checked1_at', 'checked2_at',  'approved_at', 'legalized_at','checked1_by', 'checked2_by',  'approved_by', 'legalized_by', 'status_dokumen', 'status_cuti', 'attachment', 'kp.nama_pengganti', 'jc.jenis_cuti_khusus', 'karyawans.nama', 'cutis.karyawan_id', 'karyawan_pengganti_id','departemens.nama');

        $result = $data;
        return $result;
    }

    public function getPengajuan($dataFilter, $settings)
    {
        return $this->_queryPengajuan($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public function countPengajuan($dataFilter)
    {
        return $this->_queryPengajuan($dataFilter)->count();
    }

    public function rejectCuti(int $id, array $newData)
    {
        $cuti = Cutie::findOrFail($id);
        $cuti->update($newData);

        $karyawan = $cuti->karyawan;
        if($cuti->jenis_cuti == 'PRIBADI'){
            if($cuti->penggunaan_sisa_cuti == 'TB'){
                $karyawan->sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $cuti->durasi_cuti;
            } else {
                $karyawan->sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $cuti->durasi_cuti;
            }
            $karyawan->save();
        }
        return $cuti;
    }

    public function deleteCuti(int $id)
    {
        $cuti = Cutie::findOrFail($id);
        $karyawan = $cuti->karyawan;
        if($cuti->rejected_by == null && $cuti->jenis_cuti == 'PRIBADI'){
            if($cuti->penggunaan_sisa_cuti == 'TB'){
                $karyawan->sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $cuti->durasi_cuti;
            } else {
                $karyawan->sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $cuti->durasi_cuti;
            }
            $karyawan->save();
        }

        $cuti->approval->delete();
        $cuti->delete();
        $data = [
            'sisa_cuti_tahunan' => $karyawan->sisa_cuti_pribadi + $karyawan->sisa_cuti_bersama,
            'sisa_cuti_pribadi' => $karyawan->sisa_cuti_pribadi,
            'sisa_cuti_tahun_lalu' => $karyawan->sisa_cuti_tahun_lalu
        ];
        return $data;
    }

    public function cancelCuti(int $id, array $newData)
    {
        $cuti = Cutie::findOrFail($id);
        $cuti->update($newData);
        $karyawan = $cuti->karyawan;
        if($cuti->rejected_by == null && $cuti->jenis_cuti == 'PRIBADI'){
            if($cuti->penggunaan_sisa_cuti == 'TB'){
                $karyawan->sisa_cuti_pribadi = $karyawan->sisa_cuti_pribadi + $cuti->durasi_cuti;
            } else {
                $karyawan->sisa_cuti_tahun_lalu = $karyawan->sisa_cuti_tahun_lalu + $cuti->durasi_cuti;
            }
            $karyawan->save();
        }

        $data = [
            'sisa_cuti_tahunan' => $karyawan->sisa_cuti_pribadi + $karyawan->sisa_cuti_bersama,
            'sisa_cuti_pribadi' => $karyawan->sisa_cuti_pribadi,
            'sisa_cuti_tahun_lalu' => $karyawan->sisa_cuti_tahun_lalu
        ];

        return $data;
    }

    public function createCuti(array $data)
    {
        $cuti = Cutie::create($data);
        return $cuti;
    }

    public function updateCuti(int $id, array $data)
    {
        $cuti = Cutie::findOrFail($id);
        $cuti->update($data);
        return $cuti;
    }

    public function updateApprovalCuti(int $id, array $data)
    {
        $approval = ApprovalCuti::findOrFail($id);
        $approval->update($data);
        return $approval;
    }

    public function createApprovalCuti(array $data)
    {
        $approval = ApprovalCuti::create($data);
        return $approval;
    }

    public function getStructureApprovalCuti(Collection $posisi)
    {
        $list_atasan = Approval::ListAtasan($posisi);
        $my_jabatan = $posisi[0]->jabatan_id;
        $has_leader = $list_atasan['leader'] ?? null;
        $has_section_head = $list_atasan['section_head'] ?? null;
        $has_department_head = $list_atasan['department_head'] ?? null;
        $has_division_head = $list_atasan['division_head'] ?? null;
        $has_director = $list_atasan['director'] ?? null;

        $checked1_for = null;
        $checked2_for = null;
        $approved_for = null;

        //KONDISI 1 (PUNYA SEMUA)
        if($has_leader && $has_section_head && $has_department_head){
            $checked1_for = $has_leader;
            $checked2_for = $has_section_head;
            $approved_for = $has_department_head;
        }

        //KONDISI 2 (HANYA PUNYA LEADER & SECTION HEAD)
        if($has_leader && $has_section_head && !$has_department_head){
            $checked1_for = $has_leader;
            $checked2_for = $has_section_head;
            $approved_for = $has_section_head;
        }

        //KONDISI 3 (HANYA PUNYA LEADER DAN DEPARTMENT HEAD)
        if($has_leader && !$has_section_head && $has_department_head){
            $checked1_for = $has_leader;
            $checked2_for = $has_department_head;
            $approved_for = $has_department_head;
        }

        //KONDISI 4 (HANYA PUNYA DEPARTMENT HEAD)
        if(!$has_leader && !$has_section_head && $has_department_head){
            $checked1_for = $has_department_head;
            $checked2_for = $has_department_head;
            $approved_for = $has_department_head;
        }

        //KONDISI 5 (HANYA PUNYA SECTION HEAD)
        if(!$has_leader && $has_section_head && !$has_department_head){
            $checked1_for = $has_section_head;
            $checked2_for = $has_section_head;
            $approved_for = $has_section_head;
        }

        //KONDISI 6 (HANYA PUNYA SECTION HEAD DAN DEPARTMENT HEAD)
        if(!$has_leader && $has_section_head && $has_department_head){
            $checked1_for = $has_section_head;
            $checked2_for = $has_section_head;
            $approved_for = $has_department_head;
        }

        //KONDISI 7 (HANYA PUNYA DIVISION HEAD)
        if(!$has_leader && !$has_section_head && !$has_department_head){
            $checked1_for = $has_division_head;
            $checked2_for = $has_division_head;
            $approved_for = $has_director;
        }

        //KONDISI 8 (HANYA PUNYA DIRECTOR)
        if(!$has_leader && !$has_section_head && !$has_department_head && $my_jabatan == 2){
            $checked1_for = $has_director;
            $checked2_for = $has_director;
            $approved_for = $has_director;
        }

        //KONDISI 7 (HANYA PUNYA DIVISION HEAD)
        if($has_leader && !$has_section_head && !$has_department_head){
            $checked1_for = $has_leader;
            $checked2_for = $has_division_head;
            $approved_for = $has_division_head;
        }

        $approval = [
            'checked1_for' => $checked1_for,
            'checked2_for' => $checked2_for,
            'approved_for' => $approved_for,
        ];

        return $approval;
    }


}
