<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance\Scanlog;
use App\Events\LiveAttendanceEvent;
use App\Http\Controllers\Controller;

class LiveAttendanceController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Attendance - Live Attendance",
            'page' => 'attendance-live-attendance',
        ];
        return view('pages.attendance-e.live.index', $dataPage);
    }

    public function test()
    {
        try {
            Scanlog::create([
                'pin' => '261',
                'scan_date' => now(),
                'scan_status' => '1',
                'verify' => '1',
                'device_id' => 1,
                'organisasi_id' => 1,
                'start_date_scan' => date('Y-m-d'),
                'end_date_scan' => date('Y-m-d'),
            ]);

            LiveAttendanceEvent::dispatch(true);
            return response()->json(['message' => 'Success'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'karyawans.nama',
            1 => 'attendance_scanlogs.pin',
            2 => 'attendance_scanlogs.scan_date',
            3 => 'attendance_scanlogs.verify',
        );

        $totalData = Scanlog::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $scanlogs = Scanlog::getData($dataFilter, $settings);
        $totalFiltered = Scanlog::countData($dataFilter);

        $dataTable = [];

        if (!empty($scanlogs)) {
            foreach ($scanlogs as $data) {
                if ($data->verify == '1') {
                    $nestedData['verify'] = '<i class="fas fa-fingerprint"></i> Finger';
                } elseif ($data->verify == '2') {
                    $nestedData['verify'] = '<i class="fas fa-unlock-alt"></i> Password';
                } elseif ($data->verify == '3') {
                    $nestedData['verify'] = '<i class="fas fa-id-card"></i> Card';
                } elseif ($data->verify == '4') {
                    $nestedData['verify'] = '<i class="fas fa-laugh-beam"></i> Face';
                } elseif ($data->verify == '5') {
                    $nestedData['verify'] = '<i class="fas fa-map-marker-alt"></i> GPS';
                } elseif ($data->verify == '6') {
                    $nestedData['verify'] = '<i class="fas fa-user"></i> Vein';
                } else {
                    $nestedData['verify'] = '<i class="fas fa-laugh-beam"></i> Face';
                }

                $nestedData['karyawan'] = $data->karyawan;
                $nestedData['pin'] = $data->pin;
                $nestedData['scan_date'] = Carbon::parse($data->scan_date)->format('d M Y, H:i');

                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "statusFilter" => !empty($dataFilter['statusFilter']) ? $dataFilter['statusFilter'] : "Kosong",
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }
}
