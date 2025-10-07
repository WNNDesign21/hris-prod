<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormat;
use App\Models\Attendance\Device;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\SummarizeAttendanceJob;
use App\Models\Attendance\AttendanceGps;

class AttendanceController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'organisasi_id' => 'required|integer|exists:organisasis,id_organisasi',
            'departemen_id' => 'nullable|integer|exists:departemens,id_departemen',
            'divisi_id' => 'nullable|integer|exists:divisis,id_divisi',
            'karyawan_id' => 'required|exists:karyawans,id_karyawan',
            'pin' => 'required|exists:karyawans,pin',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'attendance_date' => 'required|date',
            'attendance_time' => 'required|date_format:Y-m-d H:i:s',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'type' => 'required|in:0,1,2', // 0 = VS, 1 = TL, 2 = MOBILE
            'status' => 'required|in:0,1', // 0 = IN, 1 = OUT
        ]);

        DB::beginTransaction();
        try {
            $organisasi_id = $request->organisasi_id;
            $departemen_id = $request->departemen_id;
            $divisi_id = $request->divisi_id;
            $karyawan_id = $request->karyawan_id;
            $pin = $request->pin;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $attendance_date = $request->attendance_date;
            $attendance_time = $request->attendance_time;
            $type = $request->type == '0' ? 'VS' : 'TL';
            $status = $request->status == '0' ? 'IN' : 'OUT';
            $attachment = $request->attachment;
            if ($attachment) {
                $fileName = Str::random(6) . '.' . $attachment->getClientOriginalExtension();
                $filePath = $attachment->storeAs('attachment/attendance_gps', $fileName);
            } else {
                $filePath = '0';
            }
            $karyawan = Karyawan::find($karyawan_id);
            $user = $karyawan->user;

            if ($karyawan->status_karyawan !== 'AT') {
                DB::rollBack();
                return ResponseFormat::error(null, 'Karyawan sudah tidak aktif.', 400);
            }

            $attendanceGps = AttendanceGps::create([
                'organisasi_id' => $organisasi_id,
                'departemen_id' => $departemen_id,
                'divisi_id' => $divisi_id,
                'karyawan_id' => $karyawan_id,
                'pin' => $pin,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'attendance_date' => $attendance_date,
                'attendance_time' => $attendance_time,
                'attachment' => $filePath,
                'type' => $type,
                'status' => $status
            ]);

            $device = Device::where('organisasi_id', $organisasi_id)->first();
            $scanlog = Scanlog::create([
                'device_id' => $device->id_device,
                'organisasi_id' => $organisasi_id,
                'pin' => $pin,
                'scan_date' => $attendance_time,
                'scan_status' => $request->status,
                'verify' => '5',
                'start_date_scan' => $attendance_date,
                'end_date_scan' => $attendance_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $attendanceGps->scanlog_id = $scanlog->id_scanlog;
            $attendanceGps->save();

            DB::commit();
            SummarizeAttendanceJob::dispatch([$pin], $attendanceGps->organisasi_id, $user, $attendanceGps->attendance_date);
            return ResponseFormat::success([
                'gps' => $attendanceGps,
                'scanlog' => $scanlog,
            ], 'Attendance created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseFormat::error(null, $e->getMessage(), 400);
        }
    }

    public function getDataByKaryawanId(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id_karyawan',
        ]);

        try {
            $perPage = $request->input('per_page', 3);
            $attendanceGps = AttendanceGps::where('karyawan_id', $request->karyawan_id)
                ->orderBy('attendance_time', 'DESC')
                ->paginate($perPage);
            return ResponseFormat::success($attendanceGps, 'Data retrieved successfully');
        } catch (Exception $e) {
            return ResponseFormat::error(null, $e->getMessage(), 400);
        }
    }
}
