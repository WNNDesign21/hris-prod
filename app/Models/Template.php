<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'templates';
    protected $primaryKey = 'id_template';

    protected $fillable = [
        'nama','type', 'template_path', 'isActive', 'organisasi_id'
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 'Y');
    }

    public function scopeOrganisasi($query, $organisasi)
    {
        return $query->where('organisasi_id', $organisasi);
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_template',
            'nama',
            'type',
            'isActive',
            'template_path'
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('nama', 'ILIKE', "%{$search}%")
                    ->orWhere('isActive', 'ILIKE', "%{$search}%")
                    ->orWhere('type', 'ILIKE', "%{$search}%");
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
