<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organisasis';
    protected $primaryKey = 'id_organisasi';

    protected $fillable = [
        'nama', 'alamat'
    ];

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_organisasi',
            'nama',
            'alamat',
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('nama', 'ILIKE', "%{$search}%")
                ->orWhere('alamat', 'ILIKE', "%{$search}%");
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
