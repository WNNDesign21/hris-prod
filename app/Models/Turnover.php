<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Turnover extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'turnovers';
    protected $primaryKey = 'id_turnover';

    protected $fillable = [
        'karyawan_id', 'status_karyawan',
        'tanggal_keluar', 'keterangan',
        'jumlah_aktif_karyawan_terakhir', 'organisasi_id'
    ];

    protected $dates = ['tanggal_keluar'];

    public function scopeOrganisasi($query, $organisasi)
    {
        return $query->where('turnovers.organisasi_id', $organisasi);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'id_turnover',
            'turnovers.karyawan_id',
            'karyawans.nama',
            'turnovers.status_karyawan',
            'turnovers.tanggal_keluar',
            'turnovers.keterangan',
            'turnovers.created_at',
        )
        ->leftJoin('karyawans', 'turnovers.karyawan_id', 'karyawans.id_karyawan');
        
        $organisasi_id = auth()->user()->organisasi_id;
        if($organisasi_id){
            $data->where('turnovers.organisasi_id', $organisasi_id);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('turnovers.karyawan_id', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('turnovers.status_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('turnovers.tanggal_keluar', 'ILIKE', "%{$search}%")
                    ->orWhere('turnovers.keterangan', 'ILIKE', "%{$search}%");
            });
        }

        $data->orderBy('turnovers.tanggal_keluar', 'desc');

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
