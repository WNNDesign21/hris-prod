<?php

namespace App\Models;

use App\Models\Divisi;
use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sakite extends Model
{
    use HasFactory;

    protected $table = 'sakits';
    protected $primaryKey = 'id_sakit';

    protected $fillable = [
        'id_sakit',
        'karyawan_id',
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'keterangan',
        'karyawan_pengganti_id',
        'approved_at',
        'approved_by',
        'legalized_at',
        'legalized_by',
        'rejected_at',
        'rejected_by',
        'rejected_note',
        'attachment',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function karyawanPengganti()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pengganti_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {
        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $data = self::select(
            'sakits.id_sakit',
            'sakits.karyawan_id',
            'sakits.organisasi_id',
            'sakits.departemen_id',
            'sakits.divisi_id',
            'sakits.tanggal_mulai',
            'sakits.tanggal_selesai',
            'sakits.durasi',
            'sakits.keterangan',
            'sakits.karyawan_pengganti_id',
            'sakits.approved_at',
            'sakits.approved_by',
            'sakits.legalized_at',
            'sakits.legalized_by',
            'sakits.rejected_at',
            'sakits.rejected_by',
            'sakits.rejected_note',
            'sakits.attachment',
            'sakits.created_at',
            'karyawans.nama as nama',
            'kp.nama_pengganti as karyawan_pengganti',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'posisis.nama as posisi',
            )
            ->leftJoin('karyawans', 'sakits.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('sakits.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'sakits.divisi_id', 'divisis.id_divisi');

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('sakits.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['karyawan_id'])) {
            $data->where('sakits.karyawan_id', $dataFilter['karyawan_id']);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (isset($dataFilter['date'])) {
            $data->whereDate('sakits.tanggal_mulai', $dataFilter['date']);
        }

        //FILTER CUSTOM
        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            if ($status == 1) {
                $data->whereNotNull('sakits.attachment')->whereNull('sakits.approved_by')->whereNull('sakits.rejected_by');
            } elseif ($status == 2) {
                $data->whereNotNull('sakits.attachment')->whereNull('sakits.legalized_by')->whereNull('sakits.rejected_by');
            } else {
                $data->whereNotNull('sakits.rejected_by');
            }
        }

        if (isset($dataFilter['departemen'])) {
            $departemen = $dataFilter['departemen'];
            $data->where('sakits.departemen_id', $departemen);
        }

        if (isset($dataFilter['departemens'])) {
            $departemens = $dataFilter['departemens'];
            $data->whereIn('sakits.departemen_id', $departemens);
        }

        if (isset($dataFilter['urutan'])) {
            $urutan = $dataFilter['urutan'];
            if($urutan == 'ON') {
                $data->orderBy('sakits.tanggal_mulai', 'ASC');
            } else {
                $data->orderBy('sakits.tanggal_mulai', 'ASC');
            }
        } else {
            $data->orderBy('sakits.tanggal_mulai', 'DESC');
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('sakits.tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('sakits.tanggal_selesai', 'ILIKE', "%{$search}%")
                    ->orWhere('posisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%");
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
        return self::_query($dataFilter)->get()->count();
    }

    // Must Approved
    private static function _mustApproved($dataFilter)
    {
        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $data = self::select(
            'sakits.id_sakit',
            'sakits.karyawan_id',
            'sakits.organisasi_id',
            'sakits.departemen_id',
            'sakits.divisi_id',
            'sakits.tanggal_mulai',
            'sakits.tanggal_selesai',
            'sakits.durasi',
            'sakits.keterangan',
            'sakits.karyawan_pengganti_id',
            'sakits.approved_at',
            'sakits.approved_by',
            'sakits.legalized_at',
            'sakits.legalized_by',
            'sakits.rejected_at',
            'sakits.rejected_by',
            'sakits.rejected_note',
            'sakits.attachment',
            'sakits.created_at',
            'karyawans.nama as nama',
            'kp.nama_pengganti as karyawan_pengganti',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'posisis.nama as posisi',
            )
            ->leftJoin('karyawans', 'sakits.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('sakits.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'sakits.divisi_id', 'divisis.id_divisi');

        if (auth()->user()->hasRole('personalia')) {
            $data->where('sakits.organisasi_id', auth()->user()->organisasi_id);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (isset($dataFilter['date'])) {
            $data->whereDate('sakits.tanggal_mulai', $dataFilter['date']);
        }

        if (auth()->user()->hasRole('personalia')) {
            $data->where(function ($query) {
                $query->whereNull('sakits.legalized_by')
                ->whereNull('sakits.rejected_by');
            });
        } else {
            $data->where(function ($query) {
                $query->whereNull('sakits.approved_by')
                ->whereNull('sakits.rejected_by');
            });
        }

        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            if ($status == 1) {
                $data->whereNotNull('sakits.attachment')->whereNull('sakits.approved_by')->whereNull('sakits.rejected_by');
            } elseif ($status == 2) {
                $data->whereNotNull('sakits.attachment')->whereNull('sakits.legalized_by')->whereNull('sakits.rejected_by');
            } else {
                $data->whereNotNull('sakits.rejected_by');
            }
        }

        if (isset($dataFilter['departemen'])) {
            $departemen = $dataFilter['departemen'];
            $data->where('sakits.departemen_id', $departemen);
        }

        if (isset($dataFilter['departemens'])) {
            $departemens = $dataFilter['departemens'];
            $data->whereIn('sakits.departemen_id', $departemens);
        }

        if (isset($dataFilter['urutan'])) {
            $urutan = $dataFilter['urutan'];
            if($urutan == 'ON') {
                $data->orderBy('sakits.tanggal_mulai', 'ASC');
            } else {
                $data->orderBy('sakits.tanggal_mulai', 'ASC');
            }
        } else {
            $data->orderBy('sakits.tanggal_mulai', 'DESC');
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('sakits.tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('sakits.tanggal_selesai', 'ILIKE', "%{$search}%")
                    ->orWhere('posisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%");
            });
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
        return self::_mustApproved($dataFilter)->get()->count();
    }

    // All Data
    private static function _alldata($dataFilter)
    {
        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $data = self::select(
            'sakits.id_sakit',
            'sakits.karyawan_id',
            'sakits.organisasi_id',
            'sakits.departemen_id',
            'sakits.divisi_id',
            'sakits.tanggal_mulai',
            'sakits.tanggal_selesai',
            'sakits.durasi',
            'sakits.keterangan',
            'sakits.karyawan_pengganti_id',
            'sakits.approved_at',
            'sakits.approved_by',
            'sakits.legalized_at',
            'sakits.legalized_by',
            'sakits.rejected_at',
            'sakits.rejected_by',
            'sakits.rejected_note',
            'sakits.attachment',
            'sakits.created_at',
            'karyawans.nama as nama',
            'kp.nama_pengganti as karyawan_pengganti',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'posisis.nama as posisi',
            )
            ->leftJoin('karyawans', 'sakits.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('sakits.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'sakits.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'sakits.divisi_id', 'divisis.id_divisi');

        if (auth()->user()->hasRole('personalia')) {
            $data->where('sakits.organisasi_id', auth()->user()->organisasi_id);
        }

        if (isset($dataFilter['member_posisi_id'])) {
            $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        }

        if (isset($dataFilter['date'])) {
            $data->whereDate('sakits.tanggal_mulai', $dataFilter['date']);
        }

        if (isset($dataFilter['status'])) {
            $status = $dataFilter['status'];
            if ($status == 1) {
                $data->whereNotNull('sakits.attachment')->whereNull('sakits.approved_by')->whereNull('sakits.rejected_by');
            } elseif ($status == 2) {
                $data->whereNotNull('sakits.attachment')->whereNull('sakits.legalized_by')->whereNull('sakits.rejected_by');
            } else {
                $data->whereNotNull('sakits.rejected_by');
            }
        }

        if (isset($dataFilter['departemen'])) {
            $departemen = $dataFilter['departemen'];
            $data->where('sakits.departemen_id', $departemen);
        }

        if (isset($dataFilter['departemens'])) {
            $departemens = $dataFilter['departemens'];
            $data->whereIn('sakits.departemen_id', $departemens);
        }

        if (isset($dataFilter['urutan'])) {
            $urutan = $dataFilter['urutan'];
            if($urutan == 'ON') {
                $data->orderBy('sakits.tanggal_mulai', 'ASC');
            } else {
                $data->orderBy('sakits.tanggal_mulai', 'ASC');
            }
        } else {
            $data->orderBy('sakits.tanggal_mulai', 'DESC');
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('sakits.tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('sakits.tanggal_selesai', 'ILIKE', "%{$search}%")
                    ->orWhere('posisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%");
            });
        }

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

    public static function getDataSakit($dataFilter)
    {
        return self::_query($dataFilter)->get();
    }
}
