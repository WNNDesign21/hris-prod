<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Izine extends Model
{
    use HasFactory;

    protected $table = 'izins';
    protected $primaryKey = 'id_izin';

    protected $fillable = [
        'karyawan_id',
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
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
        'attachment',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function karyawanPengganti()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pengganti_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {

        $getKaryawanPengganti = Karyawan::select("id_karyawan as kp_id", "nama as nama_pengganti");
        $data = self::select(
            'izins.id_izin',
            'izins.karyawan_id',
            'izins.organisasi_id',
            'izins.departemen_id',
            'izins.divisi_id',
            'izins.jenis_izin',
            'izins.tanggal_mulai',
            'izins.tanggal_selesai',
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
            'izins.attachment',
            'karyawans.nama as nama_karyawan',
            'kp.nama_pengganti as nama_pengganti',
            'departemens.nama as nama_departemen',
            'divisis.nama as nama_divisi'
            )
            ->leftJoin('karyawans', 'izins.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoinSub($getKaryawanPengganti, 'kp', function (JoinClause $joinKaryawanPengganti) {
                $joinKaryawanPengganti->on('izins.karyawan_pengganti_id', 'kp.kp_id');
            })
            ->leftJoin('karyawan_posisi', 'izins.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'izins.divisi_id', 'divisis.id_divisi');
        
        if (isset($dataFilter['organisasi_id'])) {
            $data->where('izins.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['karyawan_id'])) {
            $data->where('izins.karyawan_id', $dataFilter['karyawan_id']);
        }

        // if (isset($dataFilter['member_posisi_id'])) {
        //     $data->whereIn('posisis.id_posisi', $dataFilter['member_posisi_id']);
        // }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('rencana_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('rencana_selesai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('aktual_mulai_cuti', 'ILIKE', "%{$search}%")
                    ->orWhere('aktual_selesai_cuti', 'ILIKE', "%{$search}%");
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
}
