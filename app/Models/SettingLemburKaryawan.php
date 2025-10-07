<?php

namespace App\Models;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingLemburKaryawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'setting_lembur_karyawans';
    protected $primaryKey = 'id_setting_lembur_karyawan';

    protected $fillable = [
        'karyawan_id','organisasi_id','jabatan_id','departemen_id','gaji'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'setting_lembur_karyawans.id_setting_lembur_karyawan',
            'karyawans.id_karyawan',
            'karyawans.ni_karyawan',
            'karyawans.nama',
            'setting_lembur_karyawans.gaji',
            'departemens.nama as departemen',
            'departemens.id_departemen',
            'divisis.nama as divisi',
            'divisis.id_divisi',
        );

        $data->rightJoin('karyawans', 'setting_lembur_karyawans.karyawan_id', 'karyawans.id_karyawan')
        ->leftJoin('karyawan_posisi', 'karyawans.id_karyawan', 'karyawan_posisi.karyawan_id')
        ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
        ->leftJoin('departemens', 'posisis.departemen_id', 'departemens.id_departemen')
        ->leftJoin('divisis', 'departemens.divisi_id', 'divisis.id_divisi')
        ->leftJoin('users', 'karyawans.user_id', 'users.id')
        ->groupBy(
            'setting_lembur_karyawans.id_setting_lembur_karyawan',
            'karyawans.id_karyawan',
            'karyawans.ni_karyawan',
            'karyawans.nama',
            'setting_lembur_karyawans.gaji',
            'departemens.nama',
            'departemens.id_departemen',
            'divisis.nama',
            'divisis.id_divisi'
        );

        $organisasi_id = auth()->user()->organisasi_id;

        $data->where('users.organisasi_id', $organisasi_id);
        $data->where('karyawans.status_karyawan', 'AT');

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('karyawans.id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.ni_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('setting_lembur_karyawans.gaji', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
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
