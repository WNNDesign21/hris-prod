<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisCuti extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_cutis';
    protected $primaryKey = 'id_jenis_cuti';

    protected $fillable = [
        'jenis', 'durasi'
    ];

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_jenis_cuti',
            'jenis',
            'durasi',
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('jenis', 'ILIKE', "%{$search}%")
                ->orWhere('durasi', 'ILIKE', "%{$search}%");
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
