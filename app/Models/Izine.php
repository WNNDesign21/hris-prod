<?php

namespace App\Models;

use App\Models\Karyawan;
use App\Helpers\Approval;
use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Izine extends Model
{
    use HasFactory;

    protected $table = 'izins';
    protected $primaryKey = 'id_izin';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_izin',
        'karyawan_id',
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'jenis_izin',
        'rencana_mulai_or_masuk',
        'rencana_selesai_or_keluar',
        'aktual_mulai_or_masuk',
        'aktual_selesai_or_keluar',
        'durasi',
        'keterangan',
        'karyawan_pengganti_id',
        'checked_at',
        'checked_by',
        'approved_at',
        'approved_by',
        'legalized_at',
        'legalized_by',
        'rejected_at',
        'rejected_by',
        'rejected_note',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function karyawanPengganti()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pengganti_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as karyawan_pengganti");
        $data = self::select(
            'izins.id_izin',
            'izins.karyawan_id',
            'izins.organisasi_id',
            'izins.departemen_id',
            'izins.divisi_id',
            'izins.jenis_izin',
            'izins.rencana_mulai_or_masuk',
            'izins.rencana_selesai_or_keluar',
            'izins.aktual_mulai_or_masuk',
            'izins.aktual_selesai_or_keluar',
            'izins.durasi',
            'izins.keterangan',
            'izins.karyawan_pengganti_id',
            'izins.checked_at',
            'izins.checked_by',
            'izins.approved_at',
            'izins.approved_by',
            'izins.legalized_at',
            'izins.legalized_by',
            'izins.rejected_at',
            'izins.rejected_by',
            'izins.rejected_note',
            'karyawans.nama as nama',
            'kp.karyawan_pengganti as karyawan_pengganti',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'posisis.nama as posisi'
            )
            ->leftJoin('karyawans', 'izins.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('izins.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'izins.divisi_id', 'divisis.id_divisi');

        if (isset($dataFilter['jenis_izin'])) {
            $data->whereIn('izins.jenis_izin', $dataFilter['jenis_izin']);
        }

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('izins.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['is_security'])) {
            $data->where(function ($query) {
                $query->whereNotNull('izins.aktual_mulai_or_masuk')
                    ->orWhereNotNull('izins.aktual_selesai_or_keluar');
            });
        }

        if (isset($dataFilter['karyawan_id'])) {
            $data->where('izins.karyawan_id', $dataFilter['karyawan_id']);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (isset($dataFilter['is_shift_malam'])) {
            $data->where(function ($query) {
            $query->whereTime('izins.rencana_mulai_or_masuk', '>=', '21:00:00')
                ->orWhereTime('izins.rencana_mulai_or_masuk', '<=', '06:00:00')
                ->orWhereTime('izins.rencana_selesai_or_keluar', '>=', '21:00:00')
                ->orWhereTime('izins.rencana_selesai_or_keluar', '<=', '06:00:00')
                ->orWhereTime('izins.aktual_mulai_or_masuk', '>=', '21:00:00')
                ->orWhereTime('izins.aktual_mulai_or_masuk', '<=', '06:00:00')
                ->orWhereTime('izins.aktual_selesai_or_keluar', '>=', '21:00:00')
                ->orWhereTime('izins.aktual_selesai_or_keluar', '<=', '06:00:00');
            });
        }

        //FILTER CUSTOM
        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            if ($status == 1) {
                $data->whereNull('izins.checked_by')->whereNull('izins.rejected_by');
            } elseif ($status == 2) {
                $data->whereNull('izins.approved_by')->whereNull('izins.legalized_by')->whereNull('izins.rejected_by');
            } elseif ($status == 3)  {
                $data->whereNull('izins.legalized_by')->whereNull('izins.rejected_by');
            } else {
                $data->whereNotNull('izins.rejected_by');
            }
        }

        if (isset($dataFilter['departemen'])) {
            $departemen = $dataFilter['departemen'];
            $data->where('izins.departemen_id', $departemen);
        }

        if (isset($dataFilter['departemens'])) {
            $departemens = $dataFilter['departemens'];
            $data->whereIn('izins.departemen_id', $departemens);
        }

        if (isset($dataFilter['urutan'])) {
            $urutan = $dataFilter['urutan'];
            if($urutan == 'ON') {
                $data->orderBy('izins.rencana_mulai_or_masuk', 'ASC');
            } else {
                $data->orderBy('izins.rencana_mulai_or_masuk', 'DESC');
            }
        }

        if (isset($dataFilter['date'])) {
            $date = $dataFilter['date'];
            $data->whereDate('izins.rencana_mulai_or_masuk', $date);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('izins.rencana_mulai_or_masuk', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.rencana_selesai_or_keluar', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.aktual_mulai_or_masuk', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.aktual_selesai_or_keluar', 'ILIKE', "%{$search}%");
            });
        }

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
        return self::_query($dataFilter)->count();
    }

    public static function getDataIzin($dataFilter)
    {
        return self::_query($dataFilter)->get();
    }

    // ALL DATA
    private static function _alldata($dataFilter)
    {

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as karyawan_pengganti");
        $data = self::select(
            'izins.*',
            'karyawans.nama as nama',
            'kp.karyawan_pengganti as karyawan_pengganti',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'posisis.nama as posisi'
            )
            ->leftJoin('karyawans', 'izins.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('izins.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'izins.divisi_id', 'divisis.id_divisi');

        if (auth()->user()->hasRole('personalia')) {
            $data->where('izins.organisasi_id', auth()->user()->organisasi_id);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (isset($dataFilter['is_shift_malam'])) {
            $data->where(function ($query) {
            $query->whereTime('izins.rencana_mulai_or_masuk', '>=', '21:00:00')
                ->orWhereTime('izins.rencana_mulai_or_masuk', '<=', '06:00:00')
                ->orWhereTime('izins.rencana_selesai_or_keluar', '>=', '21:00:00')
                ->orWhereTime('izins.rencana_selesai_or_keluar', '<=', '06:00:00')
                ->orWhereTime('izins.aktual_mulai_or_masuk', '>=', '21:00:00')
                ->orWhereTime('izins.aktual_mulai_or_masuk', '<=', '06:00:00')
                ->orWhereTime('izins.aktual_selesai_or_keluar', '>=', '21:00:00')
                ->orWhereTime('izins.aktual_selesai_or_keluar', '<=', '06:00:00');
            });
        }

        //FILTER CUSTOM
        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            if ($status == 1) {
                $data->whereNull('izins.checked_by')->whereNull('izins.rejected_by');
            } elseif ($status == 2) {
                $data->whereNull('izins.approved_by')->whereNull('izins.legalized_by')->whereNull('izins.rejected_by');
            } elseif ($status == 3)  {
                $data->whereNull('izins.legalized_by')->whereNull('izins.rejected_by');
            } else {
                $data->whereNotNull('izins.rejected_by');
            }
        }

        if (isset($dataFilter['departemen'])) {
            $departemen = $dataFilter['departemen'];
            $data->where('izins.departemen_id', $departemen);
        }

        if (isset($dataFilter['departemens'])) {
            $departemens = $dataFilter['departemens'];
            $data->whereIn('izins.departemen_id', $departemens);
        }

        if (isset($dataFilter['urutan'])) {
            $urutan = $dataFilter['urutan'];
            if($urutan == 'ON') {
                $data->orderBy('izins.rencana_mulai_or_masuk', 'ASC');
            } else {
                $data->orderBy('izins.rencana_mulai_or_masuk', 'DESC');
            }
        }

        if (isset($dataFilter['date'])) {
            $date = $dataFilter['date'];
            $data->whereDate('izins.rencana_mulai_or_masuk', $date);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('izins.rencana_mulai_or_masuk', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.rencana_selesai_or_keluar', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.aktual_mulai_or_masuk', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.aktual_selesai_or_keluar', 'ILIKE', "%{$search}%");
            });
        }

        $data->orderByDesc('izins.updated_at');

        $result = $data;
        return $result;
    }

    public static function getAllData($dataFilter, $settings)
    {
        return self::_alldata($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countAllData($dataFilter)
    {
        return self::_alldata($dataFilter)->count();
    }

    // MUST APPROVED
    private static function _mustApproved($dataFilter)
    {

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as karyawan_pengganti");
        $data = self::select(
            'izins.*',
            'karyawans.nama as nama',
            'kp.karyawan_pengganti as karyawan_pengganti',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'posisis.nama as posisi'
            )
            ->leftJoin('karyawans', 'izins.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('izins.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'izins.divisi_id', 'divisis.id_divisi');

        if (auth()->user()->hasRole('personalia')) {
            $data->where('izins.organisasi_id', auth()->user()->organisasi_id);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (auth()->user()->hasRole('personalia')) {
            $data->where(function ($query) {
                $query->whereNotNull('izins.checked_by')
                    ->whereNotNull('izins.approved_by')
                    ->whereNull('izins.legalized_by');
            });
        } else {
            $data->where(function ($query) {
                $query->whereNull('izins.checked_by')
                    ->orWhereNull('izins.approved_by');
            });
        }

        if (isset($dataFilter['is_shift_malam'])) {
            $data->where(function ($query) {
                $query->whereTime('izins.rencana_mulai_or_masuk', '>=', '21:00:00')
                    ->orWhereTime('izins.rencana_mulai_or_masuk', '<=', '06:00:00')
                    ->orWhereTime('izins.rencana_selesai_or_keluar', '>=', '21:00:00')
                    ->orWhereTime('izins.rencana_selesai_or_keluar', '<=', '06:00:00')
                    ->orWhereTime('izins.aktual_mulai_or_masuk', '>=', '21:00:00')
                    ->orWhereTime('izins.aktual_mulai_or_masuk', '<=', '06:00:00')
                    ->orWhereTime('izins.aktual_selesai_or_keluar', '>=', '21:00:00')
                    ->orWhereTime('izins.aktual_selesai_or_keluar', '<=', '06:00:00');
            });
        }

        //FILTER CUSTOM
        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            if ($status == 1) {
                $data->whereNull('izins.checked_by')->whereNull('izins.rejected_by');
            } elseif ($status == 2) {
                $data->whereNull('izins.approved_by')->whereNull('izins.legalized_by')->whereNull('izins.rejected_by');
            } elseif ($status == 3)  {
                $data->whereNull('izins.legalized_by')->whereNull('izins.rejected_by');
            } else {
                $data->whereNotNull('izins.rejected_by');
            }
        }

        if (isset($dataFilter['departemen'])) {
            $departemen = $dataFilter['departemen'];
            $data->where('izins.departemen_id', $departemen);
        }

        if (isset($dataFilter['departemens'])) {
            $departemens = $dataFilter['departemens'];
            $data->whereIn('izins.departemen_id', $departemens);
        }

        if (isset($dataFilter['urutan'])) {
            $urutan = $dataFilter['urutan'];
            if($urutan == 'ON') {
                $data->orderBy('izins.rencana_mulai_or_masuk', 'ASC');
            } else {
                $data->orderBy('izins.rencana_mulai_or_masuk', 'DESC');
            }
        }

        if (isset($dataFilter['date'])) {
            $date = $dataFilter['date'];
            $data->whereDate('izins.rencana_mulai_or_masuk', $date);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('izins.rencana_mulai_or_masuk', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.rencana_selesai_or_keluar', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.aktual_mulai_or_masuk', 'ILIKE', "%{$search}%")
                    ->orWhere('izins.aktual_selesai_or_keluar', 'ILIKE', "%{$search}%");
            });
        }

        if (auth()->user()->hasRole('personalia')) {
            $data->orderByRaw('izins.checked_by IS NOT NULL DESC, izins.approved_by IS NOT NULL DESC, izins.legalized_by IS NULL DESC');
        } else {
            $data->orderByRaw('izins.checked_by IS NULL DESC, izins.approved_by IS NULL DESC');
        }

        $result = $data;
        return $result;
    }

    public static function getMustApprovedData($dataFilter, $settings)
    {
        return self::_mustApproved($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countMustApprovedData($dataFilter)
    {
        return self::_mustApproved($dataFilter)->count();
    }
}
