<?php

namespace App\Http\Controllers\Attendance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceGps;

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
            2 => 'divisis.nama',
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
                    if($data->rejected_by){
                        $formattedIsLegalized = '<span class="badge badge-danger mb-1">REJECTED</span><br><small class="text-fade">❌ HRD & GA - '.Carbon::parse($data->updated_at)->format('Y-m-d').'</small><br><small class="text-fade"> Note : '.$data->rejected_note.'</small>';
                    } else {
                        $formattedIsLegalized = '<div class="btn-group"><button class="btn btn-sm btn-success btnLegalized" data-id="'.$data->id_att_gps.'"><i class="fas fa-balance-scale"></i> Legalized</button><button type="button" class="btn btn-sm btn-danger waves-effect btnReject" data-id="'.$data->id_att_gps.'"><i class="far fa-times-circle"></i> Reject</button></div>';
                    }
                }


                $nestedData['nama'] = $data->karyawan;
                $nestedData['departemen'] = $data->departemen;
                $nestedData['divisi'] = $data->divisi;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
