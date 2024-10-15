<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';
    protected $primaryKey = 'id_event';

    protected $fillable = [
        'jenis_event','keterangan', 'organisasi_id' ,'durasi', 'tanggal_mulai', 'tanggal_selesai'
    ];

    public function scopeOrganisasi($query, $organisasi)
    {
        return $query->where('organisasi_id', $organisasi);
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_event',
            'jenis_event',
            'keterangan',
            'durasi',
            'tanggal_mulai',
            'tanggal_selesai',
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('jenis_event', 'ILIKE', "%{$search}%")
                    ->orWhere('keterangan', 'ILIKE', "%{$search}%")
                    ->orWhere('tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('tanggal_selesai', 'ILIKE', "%{$search}%")
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
