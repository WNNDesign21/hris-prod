<?php

namespace App\Models\Attendance;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Organisasi;
use App\Models\Attendance\Scanlog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceGps extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance_gps';
    protected $primaryKey = 'id_att_gps';

    protected $fillable = [
        'organisasi_id',
        'departemen_id',
        'divisi_id',
        'karyawan_id',
        'pin',
        'latitude',
        'longitude',
        'attendance_date',
        'attendance_time',
        'attachment',
        'type',
        'status',
        'scanlog_id',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id', 'id_divisi');
    }

    public function scanlog()
    {
        return $this->belongsTo(Scanlog::class, 'scanlog_id', 'id_scanlog');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'attendance_gps.*',
            'organisasis.nama as organisasi',
            'departemens.nama as departemen',
            'divisis.nama as divisi',
            'karyawans.nama as karyawan'
        );

        $data->leftJoin('organisasis', 'organisasis.id_organisasi', 'attendance_gps.organisasi_id')
            ->leftJoin('departemens', 'departemens.id_departemen', 'attendance_gps.departemen_id')
            ->leftJoin('divisis', 'divisis.id_divisi', 'attendance_gps.divisi_id')
            ->leftJoin('karyawans', 'karyawans.id_karyawan', 'attendance_gps.karyawan_id');
        
        if (isset($dataFilter['organisasi_id'])) {
            $data->where('attendance_gps.organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('organisasis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('divisis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('karyawans.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.pin', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.latitude', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.longitude', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.attendance_date', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.attendance_time', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.attachment', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.type', 'ILIKE', "%{$search}%")
                    ->orWhere('attendance_gps.status', 'ILIKE', "%{$search}%");
            });
        }

        return $data;
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
