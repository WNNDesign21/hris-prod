<?php

namespace App\Http\Controllers\Attendance;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceGps;
use Illuminate\Support\Facades\Validator;

class AttendanceGpsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('security') || auth()->user()->hasRole('personalia')) {
                return redirect()->route('attendance.presensi');
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Attendance - GPS",
            'page' => 'attendance-gps',
        ];
        return view('pages.attendance-e.gps.index', $dataPage);
    }

    public function store(Request $request)
    {
        $dataValidate = [
            'status' => ['required', 'string', 'in:IN,OUT'],
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'type' => ['required', 'string', 'in:VS,TL'],
            'image' => ['required', 'mimes:png', 'max:2048'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $status = $request->status;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $image = $request->image;
            $organisasi_id = auth()->user()->organisasi_id;
            $karyawan_id = auth()->user()->karyawan->id_karyawan;
            $pin = auth()->user()->karyawan->pin;
            $ni_karyawan = auth()->user()->karyawan->ni_karyawan;
            $nama_karyawan = auth()->user()->karyawan->nama;
            $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;
            $divisi_id = auth()->user()->karyawan->posisi[0]->divisi_id;
            $attendance_date = Carbon::now()->format('Y-m-d');
            $attendance_time = Carbon::now();
            $type = $request->type;

            $fileName = Str::random(6) . '.' . $image->getClientOriginalExtension();
            $filePath = $image->storeAs('attachment/attendance_gps', $fileName);

            $dupeExists = AttendanceGps::where('karyawan_id', $karyawan_id)
                ->where('attendance_date', $attendance_date)
                ->where('status', $status)
                ->exists();

            if ($dupeExists) {
                DB::rollBack();
                return response()->json(['message' => 'Anda sudah melakukan presensi ' . $status . ' untuk hari ini'], 400);
            }

            $data = AttendanceGps::create([
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
                'status' => $status,
            ]);

            DB::commit();
            return response()->json(['message' => 'Berhasil melakukan presensi'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_att_gps_list()
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $karyawan_id = auth()->user()->karyawan->id_karyawan;
        $attendance_date = Carbon::now()->format('Y-m-d');

        try {
            $data = AttendanceGps::where('organisasi_id', $organisasi_id)
                ->where('karyawan_id', $karyawan_id)
                ->whereDate('attendance_date', $attendance_date)
                ->orderBy('attendance_date', 'DESC')
                ->get();

            $datas = [];
            foreach ($data as $item) {
                $datas[] = [
                    'id' => $item->id_att_gps,
                    'status' => $item->status,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'att_date' => Carbon::parse($item->attendance_date)->format('d M Y'),
                    'att_time' => Carbon::parse($item->attendance_time)->format('H:i:s') . ' WIB',
                    'attachment' => asset('storage/' . $item->attachment),
                    'type' => $item->type == 'VS' ? 'VENDOR STAY' : 'TUGAS LUAR',
                ];
            }
            return response()->json(['message' => 'Data Retrive Sucessfully', 'data' => $datas], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
