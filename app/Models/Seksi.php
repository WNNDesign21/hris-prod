<?php

namespace App\Models;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seksis';
    protected $primaryKey = 'id_seksi';

    protected $fillable = [
        'departemen_id','nama'
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'departemen_id', 'id_divisi');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_seksi',
            'departemen_id',
            'seksis.nama as nama_seksi',
            'departemens.nama as nama_departemen'
        )
        ->leftJoin('departemens', 'seksis.departemen_id', 'departemens.id_departemen');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('seksis.nama', 'ILIKE', "%{$search}%")
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
        return self::_query($dataFilter)->count();
    }
}
