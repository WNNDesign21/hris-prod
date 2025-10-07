<?php

namespace App\Models;

use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piket extends Model
{
    use HasFactory;

    protected $table = 'pikets';
    protected $primaryKey = 'id_piket';

    protected $fillable = [
        'karyawan_id', 'organisasi_id', 'departemen_id', 'expired_date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'pikets.id_piket',
            'karyawans.nama as karyawan',
            'pikets.karyawan_id',
            'departemens.nama as departemen',
            'pikets.expired_date',
        );

        $data->leftJoin('karyawans', 'pikets.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('departemens', 'pikets.departemen_id', 'departemens.id_departemen');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('pikets.expired_date', 'ILIKE', "%{$search}%");
            });
        }

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('pikets.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['departemen_id'])) {
            $data->where('pikets.departemen_id', $dataFilter['departemen_id']);
        }

        if (isset($dataFilter['year'])) {
            $data->whereYear('pikets.expired_date', $dataFilter['year']);
        }

        if (isset($dataFilter['month'])) {
            $data->whereMonth('pikets.expired_date', $dataFilter['month']);
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
