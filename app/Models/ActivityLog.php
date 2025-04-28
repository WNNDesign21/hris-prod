<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_log';

    private static function _query($dataFilter)
    {
        $data = self::select(
            'activity_log.log_name',
            'users.username',
            'activity_log.created_at',
            'activity_log.description',
        );
        $data->leftJoin('users', 'activity_log.causer_id', 'users.id');

        if (isset($dataFilter['log_name'])) {
            $data->where('activity_log.log_name',  $dataFilter['log_name']);

            if ($dataFilter['log_name'] == 'error_job_upload_karyawan') {
                $data->whereDate('activity_log.created_at', date('Y-m-d'));
            }

            if ($dataFilter['log_name'] == 'error_job_upload_kontrak') {
                $data->whereDate('activity_log.created_at', date('Y-m-d'));
            }
        }

        if (isset($dataFilter['start']) && isset($dataFilter['end'])) {
            $data->whereDate('activity_log.created_at', '>=', $dataFilter['start']);
            $data->whereDate('activity_log.created_at', '<=', $dataFilter['end']);
        }

        if (isset($dataFilter['causer_id'])) {
            $data->where('activity_log.causer_id', $dataFilter['causer_id']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('activity_log.log_name', 'ILIKE', "%{$search}%")
                    ->orWhere('users.username', 'ILIKE', "%{$search}%")
                    ->orWhere('activity_log.created_at', 'ILIKE', "%{$search}%")
                    ->orWhere('activity_log.description', 'ILIKE', "%{$search}%");
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
