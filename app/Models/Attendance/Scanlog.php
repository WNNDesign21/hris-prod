<?php

namespace App\Models\Attendance;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

        $data->where('attendance_scanlogs.organisasi_id', auth()->user()->organisasi_id);
        $data->where('karyawans.organisasi_id', auth()->user()->organisasi_id);

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

    public static function getLiveAttendanceChart($dataFilter)
    {
        $totalKaryawan = DB::table('karyawans')
            ->join('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
            ->join('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
            ->join('departemens', 'departemens.id_departemen', 'posisis.departemen_id')
            ->where('karyawans.organisasi_id', $dataFilter['organisasi_id'])
            ->whereNull('karyawans.deleted_at')
            ->select('departemens.nama as departemen', DB::raw('COUNT(DISTINCT karyawans.pin) as total_karyawan'))
            ->groupBy('departemens.nama')
            ->get();

        $karyawanHadir = self::select(
            'departemens.nama as departemen',
            DB::raw('COUNT(DISTINCT attendance_scanlogs.pin) as karyawan_hadir')
        )
            ->leftJoin('karyawans', 'karyawans.pin', 'attendance_scanlogs.pin')
            ->leftJoin('karyawan_posisi', 'karyawan_posisi.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('posisis', 'posisis.id_posisi', 'karyawan_posisi.posisi_id')
            ->leftJoin('departemens', 'departemens.id_departemen', 'posisis.departemen_id')
            ->whereNull('departemens.deleted_at')
            ->where('attendance_scanlogs.organisasi_id', $dataFilter['organisasi_id']);

        if (isset($dataFilter['date'])) {
            $karyawanHadir->whereDate('attendance_scanlogs.scan_date', $dataFilter['date']);
        }

        $karyawanHadir = $karyawanHadir->groupBy('departemens.nama')->get();

        $result = $totalKaryawan->map(function ($item) use ($karyawanHadir) {
            $hadir = $karyawanHadir->where('departemen', $item->departemen)->first();
            $item->karyawan_hadir = $hadir ? $hadir->karyawan_hadir : 0;
            return $item;
        });
        return $result;
    }
}
