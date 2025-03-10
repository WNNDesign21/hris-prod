<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance\Device;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    public function get_att_tcf(Request $request, string $organisasi_id)
    {
        activity('webhook_attendance')->log('Hitting webhook');
        $body = $request->getContent();

        //Pengolahan ke Database
        $response = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            activity('webhook_attendance')->log('Invalid JSON');
            return response()->json(['message' => 'Invalid JSON'], 400);
        }
        $cloudId = $response['cloud_id'];
        $device_id = Device::where('cloud_id', $cloudId)->pluck('id_device')->first();
        $data = $response['data'];

        if (!$device_id) {
            activity('webhook_attendance')->log('Device not found');
            return response()->json(['message' => 'Device not found'], 404);
        }

        if (empty($data)) {
            activity('webhook_attendance')->log('Data not found');
            return response()->json(['message' => 'Data not found'], 404);
        }

        $pin = $data['pin'];
        $date = Carbon::createFromFormat('Y-m-d H:i', $data['scan'])->format('Y-m-d');
        $scanDate = Carbon::createFromFormat('Y-m-d H:i', $data['scan'])->format('Y-m-d H:i:s');
        $verify = $data['verify'];
        $scanStatus = $data['status_scan']; 

        DB::beginTransaction();
        try {
            $scanlog = Scanlog::create([
                'pin' => $pin,
                'scan_date' => $scanDate,
                'scan_status' => $scanStatus,
                'verify' => $verify,
                'device_id' => $device_id,
                'organisasi_id' => $organisasi_id,
                'start_date_scan' => $date,
                'end_date_scan' => $date,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            activity('webhook_attendance')->log('Success: ' . $scanlog->pin);
            DB::commit();
            $file = "attendance/karawang/attendance-" . Carbon::now()->format('Y-m-d_H-i-s') . ".txt";
            Storage::append($file, $body);
            return response()->json(['message' => 'OK'], 200);
        } catch (Throwable $e) {
            DB::rollback();
            activity('webhook_attendance')->log('Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
