<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grups';
    protected $primaryKey = 'id_grup';

    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_keluar',
        'toleransi_waktu',
        'seksi_id',
        'departemen_id',
        'organisasi_id'
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function seksi()
    {
        return $this->belongsTo(Seksi::class, 'seksi_id', 'id_seksi');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_grup',
            'nama',
            'jam_masuk',
            'jam_keluar',
            'toleransi_waktu'
        );

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