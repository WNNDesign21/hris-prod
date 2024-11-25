<?php

namespace App\Models;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GajiDepartemen extends Model
{
    use HasFactory;

    protected $table = 'gaji_departemens';
    protected $primaryKey = 'id_gaji_departemen';

    protected $fillable = [
        'departemen_id','periode','total_gaji','nominal_batas_lembur', 'organisasi_id'
    ];

    public function departemen()
    {
        $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'gaji_departemens.id_gaji_departemen',
            'gaji_departemens.departemen_id',
            'gaji_departemens.organisasi_id',
            'gaji_departemens.periode',
            'gaji_departemens.total_gaji',
            'gaji_departemens.nominal_batas_lembur',
            'departemens.nama as nama_departemen',
        );

        $data->leftJoin('departemens', 'gaji_departemens.departemen_id', 'departemens.id_departemen');

        $organisasi_id = auth()->user()->organisasi_id;

        $data->where('gaji_departemens.organisasi_id', $organisasi_id);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', '%' . $search . '%')
                    ->orWhere('gaji_departemens.periode', 'ILIKE', '%' . $search . '%')
                    ->orWhere('gaji_departemens.total_gaji', 'ILIKE', '%' . $search . '%')
                    ->orWhere('gaji_departemens.nominal_batas_lembur', 'ILIKE', '%' . $search . '%');
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
