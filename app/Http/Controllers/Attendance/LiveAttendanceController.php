<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();
        try {
            $organisasi_id = auth()->user()->organisasi_id;
            $data = Scanlog::create([
                'pin' => 'AGU-cltgv5ge6000es60ehsvxq6tu',
                'scan_date' => now(),
                'scan_status' => '1',
                'verify' => '4',
                'device_id' => 2,
                'organisasi_id' => 2,
                'start_date_scan' => date('Y-m-d'),
                'end_date_scan' => date('Y-m-d'),
            ]);

            LiveAttendanceEvent::dispatch(true, $organisasi_id);
            DB::commit();
            return response()->json(['message' => 'Success', 'data' => $data], 200);
        } catch (Throwable $th) {
            DB::rollBack();
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

        $dataFilter['date'] = date('Y-m-d');

        $scanlogs = Scanlog::getData($dataFilter, $settings);
        $totalFiltered = Scanlog::countData($dataFilter);

        $dataTable = [];

        if (!empty($scanlogs)) {
            foreach ($scanlogs as $data) {
                $nestedData['karyawan'] = $data->karyawan;
                $nestedData['departemen'] = 'ICT';
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

    public function get_live_attendance_chart(Request $request)
    {
        try {
            $dataFilter['date'] = date('Y-m-d');
            $dataFilter['organisasi_id'] = auth()->user()->organisasi_id;

            $data = Scanlog::getLiveAttendanceChart($dataFilter);
            return response()->json(['message' => 'Success', 'data' => $data], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }
}
