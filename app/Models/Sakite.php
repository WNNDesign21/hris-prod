<?php

namespace App\Models;

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
            'karyawans.nama as nama_karyawan',
            'kp.nama_pengganti as nama_pengganti',
            'departemens.nama as nama_departemen',
            'divisis.nama as nama_divisi'
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

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('sakits.tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('sakits.tanggal_selesai', 'ILIKE', "%{$search}%");
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
