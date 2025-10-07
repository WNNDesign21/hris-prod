<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance_devices';
    protected $primaryKey = 'id_device';

    protected $fillable = [
        'organisasi_id',
        'cloud_id',
        'device_sn',
        'device_name',
        'server_ip',
        'server_port',
    ];

    private static function _query($dataFilter)
    {

        $data = self::select(
            'organisasis.nama as organisasi',
            'attendance_devices.organisasi_id',
            'attendance_devices.id_device',
            'attendance_devices.cloud_id',
            'attendance_devices.device_sn',
            'attendance_devices.device_name',
            'attendance_devices.server_ip',
            'attendance_devices.server_port',
        );

        $data->leftJoin('organisasis', 'organisasis.id_organisasi', '=', 'attendance_devices.organisasi_id');
        $data->where('attendance_devices.organisasi_id', auth()->user()->organisasi_id);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('attendance_devices.organisasi_id', 'ILIKE', "%{$search}%")
                    ->orWhere('organisasis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_devices.cloud_id', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_devices.device_sn', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_devices.device_name', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_devices.server_ip', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_devices.server_port', 'ILIKE', "%{$search}%");
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
