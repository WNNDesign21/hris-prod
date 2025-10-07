<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupPattern extends Model
{
    use HasFactory;

    protected $table = 'grup_patterns';
    protected $primaryKey = 'id_grup_pattern';

    protected $fillable = [
        'organisasi_id',
        'nama',
        'urutan'
    ];

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_grup_pattern',
            'nama',
            'organisasi_id',
            'urutan'
        );

        if(isset($dataFilter['organisasi_id'])){
            $data->where('organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('nama', 'ILIKE', "%{$search}%");
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
