<?php

namespace App\Models;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\DetailLembur;
use App\Models\AttachmentLembur;
use Illuminate\Database\Eloquent\Model;
use Iksaku\Laravel\MassUpdate\MassUpdatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lembure extends Model
{
    use HasFactory, SoftDeletes, MassUpdatable;

    protected $table = 'lemburs';
    protected $primaryKey = 'id_lembur';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_lembur','organisasi_id','departemen_id','divisi_id','plan_checked_by','plan_checked_at','plan_approved_by',
        'plan_approved_at','plan_legalized_by','plan_legalized_at','actual_checked_by','actual_checked_at',
        'actual_approved_by','actual_approved_at','actual_legalized_by','actual_legalized_at','total_durasi',
        'status','attachment','issued_date','issued_by', 'jenis_hari', 'rejected_by', 'rejected_at', 'rejected_note',
        'plan_reviewed_at', 'plan_reviewed_by', 'actual_reviewed_at', 'actual_reviewed_by'
    ];

    public function attachmentLembur()
    {
        return $this->hasMany(AttachmentLembur::class, 'lembur_id', 'id_lembur');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function issued()
    {
        return $this->belongsTo(Karyawan::class, 'issued_by', 'id_karyawan');
    }

    public function rejectedby()
    {
        return $this->belongsTo(Karyawan::class, 'rejected_by', 'id_karyawan');
    }

    public function detailLembur()
    {
        return $this->hasMany(DetailLembur::class, 'lembur_id', 'id_lembur')->orderBy('created_at', 'ASC');
    }

    public function getJenisHariAttribute($value)
    {
        return ($value == 'WE' ? 'WEEKEND'  : 'WEEKDAY');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'lemburs.id_lembur',
            'lemburs.organisasi_id',
            'lemburs.departemen_id',
            'lemburs.divisi_id',
            'lemburs.plan_checked_by',
            'lemburs.plan_checked_at',
            'lemburs.plan_approved_by',
            'lemburs.plan_approved_at',
            'lemburs.plan_reviewed_by',
            'lemburs.plan_reviewed_at',
            'lemburs.plan_legalized_by',
            'lemburs.plan_legalized_at',
            'lemburs.actual_checked_by',
            'lemburs.actual_checked_at',
            'lemburs.actual_approved_by',
            'lemburs.actual_approved_at',
            'lemburs.actual_reviewed_by',
            'lemburs.actual_reviewed_at',
            'lemburs.actual_legalized_by',
            'lemburs.actual_legalized_at',
            'lemburs.total_durasi',
            'lemburs.status',
            'lemburs.attachment',
            'lemburs.issued_date',
            'lemburs.issued_by',
            'lemburs.jenis_hari',
            'lemburs.rejected_by',
            'lemburs.rejected_at',
            'lemburs.rejected_note',
            'organisasis.nama as nama_organisasi',
            'departemens.nama as nama_departemen',
            'divisis.nama as nama_divisi',
            'karyawans.nama as nama_karyawan'
            )
            ->leftJoin('karyawans', 'lemburs.issued_by', 'karyawans.id_karyawan')
            ->leftJoin('organisasis', 'lemburs.organisasi_id', 'organisasis.id_organisasi')
            ->leftJoin('departemens', 'lemburs.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'lemburs.divisi_id', 'divisis.id_divisi')
            ->rightJoin('detail_lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
            ->leftJoin('karyawan_posisi', 'lemburs.issued_by', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.id_lembur', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.issued_by', 'ILIKE', "%{$search}%");
            });
        }

        if (isset($dataFilter['month'])) {
            $data->whereMonth('detail_lemburs.rencana_mulai_lembur', $dataFilter['month']);
        }

        if (isset($dataFilter['year'])) {
            $data->whereYear('detail_lemburs.rencana_mulai_lembur', $dataFilter['year']);
        }

        if(isset($dataFilter['organisasi_id'])){
            $data->where('detail_lemburs.organisasi_id', $dataFilter['organisasi_id']);
            if(auth()->user()->hasRole('personalia')){
                if(isset($dataFilter['status'])){
                    $data->whereIn('lemburs.status', $dataFilter['status']);
                }

                if(isset($dataFilter['departemen'])){
                    $data->where('departemens.id_departemen', $dataFilter['departemen']);
                }

                if(isset($dataFilter['urutan'])){
                    if($dataFilter['urutan'] == 'NO'){
                        $data->orderBy('lemburs.issued_date', 'DESC');
                    } else {
                        $data->orderBy('lemburs.issued_date', 'ASC');
                    }
                } else {
                    $data->orderByRaw("((lemburs.status = 'WAITING') AND (lemburs.plan_approved_by IS NOT NULL AND lemburs.plan_legalized_by IS NULL AND lemburs.plan_reviewed_by IS NOT NULL)) OR ((lemburs.status = 'COMPLETED') AND (lemburs.actual_approved_by IS NOT NULL AND lemburs.actual_legalized_by IS NULL AND lemburs.actual_reviewed_by IS NOT NULL)) DESC");
                    $data->orderByRaw("lemburs.status = 'REJECTED' ASC");
                }
            } else {
                if(isset($dataFilter['divisi_id'])){
                    $data->whereIn('detail_lemburs.divisi_id', $dataFilter['divisi_id']);
                }

                $data->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('lemburs.status','WAITING');
                        $query->whereNotNull('lemburs.plan_checked_by');
                        $query->whereNull('lemburs.plan_approved_by');
                    });
                    $query->orWhere(function ($query) {
                        $query->where('lemburs.status', 'COMPLETED')
                        ->whereNotNull('lemburs.actual_checked_by')
                        ->whereNull('lemburs.actual_approved_by');
                    });
                });
                $data->orderByRaw("(lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NULL AND lemburs.plan_checked_by IS NOT NULL) OR (lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NOT NULL) DESC");
                $data->orderByRaw("lemburs.status = 'COMPLETED' AND lemburs.actual_approved_by IS NULL DESC");
            }
        }

        if(isset($dataFilter['jenisHari'])){
            $data->whereIn('lemburs.jenis_hari', $dataFilter['jenisHari']);
        }

        if(isset($dataFilter['issued_by'])){
            $data->where('lemburs.issued_by', $dataFilter['issued_by']);
            $data->orderBy('lemburs.issued_date', 'DESC');
        }

        if (isset($dataFilter['is_div_head'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('lemburs.status','WAITING');
                    $query->whereNotNull('lemburs.plan_checked_by');
                    $query->whereNull('lemburs.plan_approved_by');
                });
                $query->orWhere(function ($query) {
                    $query->where('lemburs.status', 'COMPLETED')
                    ->whereNotNull('lemburs.actual_checked_by')
                    ->whereNull('lemburs.actual_approved_by');
                });
            });
            $data->orderByRaw("(lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NULL AND lemburs.plan_checked_by IS NOT NULL) OR (lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NOT NULL) DESC");
            $data->orderByRaw("lemburs.status = 'COMPLETED' AND lemburs.actual_approved_by IS NULL DESC");
        } else {
            if (isset($dataFilter['member_posisi_ids'])) {
                $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
                if($dataFilter['mustChecked'] == 'true'){
                    $data->whereNull('lemburs.plan_checked_by');
                    $data->orWhere(function ($query) use ($dataFilter) {
                        $query->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
                        $query->where('lemburs.status', 'COMPLETED');
                        $query->whereNull('lemburs.actual_checked_by');
                    });
                } else {
                    if(isset($dataFilter['status'])){
                        $data->whereIn('lemburs.status', $dataFilter['status']);
                    } else {
                        $data->orderByRaw("(lemburs.status = 'WAITING' AND lemburs.plan_checked_by IS NULL) OR (lemburs.status = 'WAITING' AND lemburs.plan_checked_by IS NOT NULL) DESC");
                        $data->orderByRaw("lemburs.status = 'COMPLETED' AND lemburs.actual_checked_by IS NULL DESC");
                        $data->orderByRaw("lemburs.status = 'REJECTED' ASC");
                    }
                }
            }
        }

        $data->groupBy(
            'lemburs.id_lembur',
            'lemburs.organisasi_id',
            'lemburs.departemen_id',
            'lemburs.divisi_id',
            'lemburs.plan_checked_by',
            'lemburs.plan_checked_at',
            'lemburs.plan_approved_by',
            'lemburs.plan_approved_at',
            'lemburs.plan_reviewed_by',
            'lemburs.plan_reviewed_at',
            'lemburs.plan_legalized_by',
            'lemburs.plan_legalized_at',
            'lemburs.actual_checked_by',
            'lemburs.actual_checked_at',
            'lemburs.actual_approved_by',
            'lemburs.actual_approved_at',
            'lemburs.actual_reviewed_by',
            'lemburs.actual_reviewed_at',
            'lemburs.actual_legalized_by',
            'lemburs.actual_legalized_at',
            'lemburs.total_durasi',
            'lemburs.status',
            'lemburs.attachment',
            'lemburs.issued_date',
            'lemburs.issued_by',
            'lemburs.jenis_hari',
            'organisasis.nama',
            'departemens.nama',
            'divisis.nama',
            'karyawans.nama'
        );

        $result = $data;
        return $result;
    }

    public static function getData($dataFilter, $settings)
    {
        return self::_query($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->get()->count();
    }

    public static function isPastLembur($dataFilter)
    {
        $data = self::select(
            'lemburs.id_lembur',
            )
            ->leftJoin('karyawans', 'lemburs.issued_by', 'karyawans.id_karyawan')
            ->leftJoin('organisasis', 'lemburs.organisasi_id', 'organisasis.id_organisasi')
            ->leftJoin('departemens', 'lemburs.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'lemburs.divisi_id', 'divisis.id_divisi')
            ->rightJoin('detail_lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')
            ->leftJoin('karyawan_posisi', 'lemburs.issued_by', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi');

        if (isset($dataFilter['batasApprovalLembur'])) {
            $data->where('detail_lemburs.rencana_mulai_lembur', '<', $dataFilter['batasApprovalLembur']);
        }

        if(isset($dataFilter['organisasi_id'])){
            $data->where('detail_lemburs.organisasi_id', $dataFilter['organisasi_id']);
            if(auth()->user()->hasRole('personalia')){
                if(isset($dataFilter['status'])){
                    $data->whereIn('lemburs.status', $dataFilter['status']);
                }

                if(isset($dataFilter['departemen'])){
                    $data->where('departemens.id_departemen', $dataFilter['departemen']);
                }

                if(isset($dataFilter['urutan'])){
                    if($dataFilter['urutan'] == 'NO'){
                        $data->orderBy('lemburs.issued_date', 'DESC');
                    } else {
                        $data->orderBy('lemburs.issued_date', 'ASC');
                    }
                } else {
                    $data->orderByRaw("((lemburs.status = 'WAITING') AND (lemburs.plan_approved_by IS NOT NULL AND lemburs.plan_legalized_by IS NULL AND lemburs.plan_reviewed_by IS NOT NULL)) OR ((lemburs.status = 'COMPLETED') AND (lemburs.actual_approved_by IS NOT NULL AND lemburs.actual_legalized_by IS NULL AND lemburs.actual_reviewed_by IS NOT NULL)) DESC");
                    $data->orderByRaw("lemburs.status = 'REJECTED' ASC");
                }
            } else {
                if(isset($dataFilter['divisi_id'])){
                    $data->whereIn('detail_lemburs.divisi_id', $dataFilter['divisi_id']);
                }

                $data->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('lemburs.status','WAITING');
                        $query->whereNotNull('lemburs.plan_checked_by');
                        $query->whereNull('lemburs.plan_approved_by');
                    });
                });
                $data->orderByRaw("(lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NULL AND lemburs.plan_checked_by IS NOT NULL) OR (lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NOT NULL) DESC");
                $data->orderByRaw("lemburs.status = 'COMPLETED' AND lemburs.actual_approved_by IS NULL DESC");
            }
        }

        if (isset($dataFilter['is_div_head'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
            $data->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('lemburs.status','WAITING');
                    $query->whereNotNull('lemburs.plan_checked_by');
                    $query->whereNull('lemburs.plan_approved_by');
                });
            });
            $data->orderByRaw("(lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NULL AND lemburs.plan_checked_by IS NOT NULL) OR (lemburs.status = 'WAITING' AND lemburs.plan_approved_by IS NOT NULL) DESC");
            $data->orderByRaw("lemburs.status = 'COMPLETED' AND lemburs.actual_approved_by IS NULL DESC");
        } else {
            if (isset($dataFilter['member_posisi_ids'])) {
                $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_ids']);
                if($dataFilter['mustChecked'] == 'true'){
                    $data->whereNull('lemburs.plan_checked_by');
                } else {
                    if(isset($dataFilter['status'])){
                        $data->whereIn('lemburs.status', $dataFilter['status']);
                    } else {
                        $data->orderByRaw("(lemburs.status = 'WAITING' AND lemburs.plan_checked_by IS NULL) OR (lemburs.status = 'WAITING' AND lemburs.plan_checked_by IS NOT NULL) DESC");
                        $data->orderByRaw("lemburs.status = 'COMPLETED' AND lemburs.actual_checked_by IS NULL DESC");
                        $data->orderByRaw("lemburs.status = 'REJECTED' ASC");
                    }
                }
            }
        }

        $data->groupBy(
            'lemburs.id_lembur',
            'lemburs.organisasi_id',
            'lemburs.departemen_id',
            'lemburs.divisi_id',
            'lemburs.plan_checked_by',
            'lemburs.plan_checked_at',
            'lemburs.plan_approved_by',
            'lemburs.plan_approved_at',
            'lemburs.plan_reviewed_by',
            'lemburs.plan_reviewed_at',
            'lemburs.plan_legalized_by',
            'lemburs.plan_legalized_at',
            'lemburs.actual_checked_by',
            'lemburs.actual_checked_at',
            'lemburs.actual_approved_by',
            'lemburs.actual_approved_at',
            'lemburs.actual_reviewed_by',
            'lemburs.actual_reviewed_at',
            'lemburs.actual_legalized_by',
            'lemburs.actual_legalized_at',
            'lemburs.total_durasi',
            'lemburs.status',
            'lemburs.attachment',
            'lemburs.issued_date',
            'lemburs.issued_by',
            'lemburs.jenis_hari',
            'organisasis.nama',
            'departemens.nama',
            'divisis.nama',
            'karyawans.nama'
        );

        $result = $data;
        return $result->get();
    }
}
