<?php

namespace App\Models;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lembure extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lemburs';
    protected $primaryKey = 'id_lembur';

    protected $fillable = [
        'organisasi_id','departemen_id','divisi_id','plan_checked_by','plan_checked_at','plan_approved_by',
        'plan_approved_at','plan_legalized_by','plan_legalized_at','actual_checked_by','actual_checked_at',
        'actual_approved_by','actual_approved_at','actual_legalized_by','actual_legalized_at','total_durasi',
        'status','attachment','issued_date','issued_by'
    ];

    // public function scopeIssuedBy($query, $issued_by)
    // {
    //     if ($issued_by) {
    //         return $query->where('issued_by', $issued_by);
    //     } else {
    //         return $query;
    //     }
    // }

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
            'lemburs.plan_legalized_by',
            'lemburs.plan_legalized_at',
            'lemburs.actual_checked_by',
            'lemburs.actual_checked_at',
            'lemburs.actual_approved_by',
            'lemburs.actual_approved_at',
            'lemburs.actual_legalized_by',
            'lemburs.actual_legalized_at',
            'lemburs.total_durasi',
            'lemburs.status',
            'lemburs.attachment',
            'lemburs.issued_date',
            'lemburs.issued_by',
            'organisasis.nama as nama_organisasi',
            'departemens.nama as nama_departemen',
            'divisis.nama as nama_divisi',
            'karyawans.nama as nama_karyawan'
            )
            ->leftJoin('karyawans', 'lemburs.issued_by', 'karyawans.id_karyawan')
            ->leftJoin('organisasis', 'lemburs.organisasi_id', 'organisasis.id_organisasi')
            ->leftJoin('departemens', 'lemburs.departemen_id', 'departemens.id_departemen')
            ->leftJoin('divisis', 'lemburs.divisi_id', 'divisis.id_divisi');
        
        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.id_lembur', 'ILIKE', "%{$search}%")
                    ->orWhere('divisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.status', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.issued_date', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.status', 'ILIKE', "%{$search}%")
                    ->orWhere('lemburs.issued_by', 'ILIKE', "%{$search}%");
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
}
