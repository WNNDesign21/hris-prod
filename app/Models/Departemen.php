<?php

namespace App\Models;

use App\Models\Divisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departemens';
    protected $primaryKey = 'id_departemen';

    protected $fillable = [
        'divisi_id','nama'
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_departemen',
            'divisi_id',
            'departemens.nama as nama_departemen',
            'divisis.nama as nama_divisi'
        )
        ->leftJoin('divisis', 'departemens.divisi_id', 'divisis.id_divisi');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('divisis.nama', 'ILIKE', "%{$search}%");
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
