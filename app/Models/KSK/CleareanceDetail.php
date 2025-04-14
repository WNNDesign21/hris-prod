<?php

namespace App\Models\KSK;

use App\Models\Karyawan;
use App\Models\KSK\Cleareance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CleareanceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cleareance_details';
    protected $primaryKey = 'id_cleareance_detail';

    protected $fillable = [
        'cleareance_id',
        'organisasi_id',
        'type',
        'is_clear',
        'attachment',
        'keterangan',
        'confirmed_by_id',
        'confirmed_by',
        'confirmed_at',
    ];

    public function cleareance()
    {
        return $this->belongsTo(Cleareance::class, 'cleareance_id', 'id_cleareance');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'confirmed_by_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'cleareance_details.id_cleareance_detail',
            'cleareance_details.cleareance_id',
            'cleareance_details.type',
            'cleareance_details.is_clear',
            'cleareance_details.attachment',
            'cleareance_details.keterangan',
            'cleareance_details.confirmed_by_id',
            'cleareance_details.confirmed_by',
            'cleareance_details.confirmed_at',
            'cleareances.nama_departemen',
            'cleareances.nama_divisi',
            'cleareances.nama_jabatan',
            'cleareances.nama_posisi',
            'karyawans.ni_karyawan',
            'karyawans.nama as nama_karyawan',
        );
        $data->leftJoin('cleareances', 'cleareance_details.cleareance_id', 'cleareances.id_cleareance');
        $data->leftJoin('karyawans', 'cleareances.karyawan_id', 'karyawans.id_karyawan');

        if (isset($dataFilter['id_karyawan'])) {
            $data->where('cleareance_details.confirmed_by_id', $dataFilter['id_karyawan']);
        }

        if (isset($dataFilter['is_clear'])) {
            $data->where('cleareance_details.is_clear', $dataFilter['is_clear']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('cleareance_details.id_cleareance_detail', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.cleareance_id', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.type', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.is_clear', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.attachment', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.keterangan', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.confirmed_by_id', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.confirmed_by', 'LIKE', "%{$search}%")
                    ->orWhere('cleareance_details.confirmed_at', 'LIKE', "%{$search}%")
                    ->orWhere('cleareances.id_cleareance', 'LIKE', "%{$search}%");
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
