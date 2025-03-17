<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance\Device;
use App\Models\Attendance\Scanlog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceGps;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Attendance - Approval",
            'page' => 'attendance-approval',
        ];
        return view('pages.attendance-e.approval.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'karyawans.nama',
            1 => 'departemens.nama',
            2 => 'attendance_gps.attendance_time',
            3 => 'attendance_gps.type',
            4 => 'attendance_gps.latitude',
            5 => 'attendance_gps.attachment',
            6 => 'attendance_gps.status',
            7 => 'attendance_gps.scanlog_id',
        );

        $totalData = AttendanceGps::count();
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

        $organisasi_id = auth()->user()->organisasi_id;
        $dataFilter['organisasi_id'] = $organisasi_id;

        $devices = AttendanceGps::getData($dataFilter, $settings);
        $totalFiltered = AttendanceGps::countData($dataFilter);

        $dataTable = [];

        if (!empty($devices)) {
            foreach ($devices as $data) {

                
                if ($data->latitude && $data->longitude) {
                    $formattedLocation = '<a href="https://www.google.com/maps/place/' . $data->latitude . ',' . $data->longitude . '" target="_blank">View Location</a>';
                } else {
                    $formattedLocation = '';
                }

                if ($data->type == 'VS') {
                    $formattedType = 'Vendor Stay';
                } else {
                    $formattedType = 'Tugas Luar';
                }

                if ($data->attachment) {
                    $formattedAttachment = '<a id="linkFoto'.$data->id_att_gps.'" href="' . asset('storage/'.$data->attachment) . '"
                                    class="image-popup-vertical-fit" data-title="'.$data->id_att_gps.'">
                                    <img id="imageReview'.$data->id_att_gps.'" src="' . asset('storage/'.$data->attachment) . '" alt="Image Foto"
                                        style="width: 80px;height: 80px;" class="img-fluid">
                                </a>';
                } else {
                    $formattedAttachment = 'No Attachment';
                }

                if ($data->status == 'IN') {
                    $formattedStatus = '<span class="badge badge-success">IN</span>';
                } else {
                    $formattedStatus = '<span class="badge badge-danger">OUT</span>';
                }

                if ($data->scanlog_id) {
                    $formattedIsLegalized = '✅<br><small class="text-bold">HRD & GA</small><br><small class="text-fade">'.Carbon::parse($data->updated_at)->diffForHumans().'</small>';
                } else {
                    $formattedIsLegalized = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id="'.$data->id_att_gps.'"><i class="fas fa-balance-scale"></i> Legalized</button></div>';
                    // if($data->rejected_by){
                    //     $formattedIsLegalized = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ HRD & GA - '.Carbon::parse($data->updated_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    // } else {
                    //     $formattedIsLegalized = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id="'.$data->id_att_gps.'"><i class="fas fa-balance-scale"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id="'.$data->id_att_gps.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                    // }
                }


                $nestedData['nama'] = $data->karyawan;
                $nestedData['departemen'] = $data->departemen ? $data->departemen : ($data->divisi ? $data->divisi : '-');
                $nestedData['tanggal'] = Carbon::parse($data->attendance_time)->format('Y-m-d H:i'). ' WIB';
                $nestedData['tipe'] = $formattedType;
                $nestedData['lokasi'] = $formattedLocation;
                $nestedData['attachment'] = $formattedAttachment;
                $nestedData['status'] = $formattedStatus;
                $nestedData['is_legalized'] = $formattedIsLegalized;

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

    public function legalized(Request $request, string $id_att_gps)
    {
        $att_gps = AttendanceGps::find($id_att_gps);

        DB::beginTransaction();
        try{
            if ($att_gps->scanlog_id) {
                return response()->json(['message' => 'Attendance sudah di legalized, silahkan refresh halaman!'], 403);
            } elseif ($att_gps->rejected_by) {
                return response()->json(['message' => 'Attendance yang sudah di reject tidak dapat di Legalized!'], 403);
            }

            $device = Device::where('organisasi_id', auth()->user()->organisasi_id)->first();

            $scanlog = Scanlog::create([
                'device_id' => $device->id_device,
                'organisasi_id' => $att_gps->organisasi_id,
                'pin' => $att_gps->pin,
                'scan_date' => $att_gps->attendance_time,
                'scan_status' => $att_gps->status == 'IN' ? '0' : '1',
                'verify' => '5', // GPS
                'start_date_scan' => $att_gps->attendance_date,
                'end_date_scan' => $att_gps->attendance_date,
                'created_at' => $att_gps->created_at,
                'updated_at' => $att_gps->updated_at
            ]);

            $att_gps->scanlog_id = $scanlog->id_scanlog;
            $att_gps->save();

            DB::commit();
            return response()->json(['message' => 'TL berhasil di Legalized!'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
