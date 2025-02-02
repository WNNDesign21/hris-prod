<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scanlog extends Model
{
    use HasFactory;

    protected $table = 'attendance_scanlogs';
    protected $primaryKey = 'id_scanlog';

    protected $fillable = [
        'start_date_scan',
        'end_date_scan',
        'organisasi_id',
        'device_id',
        'pin',
        'scan_date',
        'scan_status',
        'verify',
        'created_at',
        'updated_at',
    ];

    private static function _query($dataFilter)
    {

        $data = self::select(
            'karyawans.nama as karyawan',
            'attendance_scanlogs.organisasi_id',
            'attendance_scanlogs.id_scanlog',
            'attendance_scanlogs.device_id',
            'attendance_scanlogs.scan_date',
            'attendance_scanlogs.pin',
            'attendance_scanlogs.verify',
            'attendance_scanlogs.scan_status',
        );

        $data->leftJoin('karyawans', 'karyawans.pin','attendance_scanlogs.pin');
        $data->leftJoin('users', 'users.id','karyawans.user_id');

        $data->where('attendance_scanlogs.organisasi_id', auth()->user()->organisasi_id);
        $data->where('users.organisasi_id', auth()->user()->organisasi_id);

        if (isset($dataFilter['date'])) {
            $data->whereDate('attendance_scanlogs.scan_date', $dataFilter['date']);
        }

        if (isset($dataFilter['karyawan_id'])) {
            $data->where('karyawans.id_karyawan', $dataFilter['karyawan_id']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('attendance_scanlogs.organisasi_id', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_scanlogs.device_id', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_scanlogs.scan_date', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_scanlogs.pin', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_scanlogs.verify', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_scanlogs.scan_status', 'ILIKE', "%{$search}%");
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
