<?php

namespace App\Http\Controllers\Attendancee;

use Throwable;
use Illuminate\Http\Request;
use App\Models\Attendance\Device;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Attendance-E - Device",
            'page' => 'attendance-device',
        ];
        return view('pages.attendance-e.device.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'attendance_devices.id_device',
            1 => 'attendance_devices.cloud_id',
            2 => 'attendance_devices.device_sn',
            3 => 'attendance_devices.device_name',
            4 => 'attendance_devices.server_ip',
            5 => 'attendance_devices.server_port',
        );

        $totalData = Device::count();
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

        $devices = Device::getData($dataFilter, $settings);
        $totalFiltered = Device::countData($dataFilter);

        $dataTable = [];

        if (!empty($devices)) {
            $no = $start;
            foreach ($devices as $data) {
                $no++;
                $nestedData['id_device'] = $data->id_device;
                $nestedData['cloud_id'] = $data->cloud_id;
                $nestedData['device_sn'] = $data->device_sn;
                $nestedData['device_name'] = $data->device_name;
                $nestedData['server_ip'] = $data->server_ip;
                $nestedData['server_port'] = $data->server_port;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_device.'" data-cloud-id="'.$data->cloud_id.'" data-device-sn="'.$data->device_sn.'" data-device-name="'.$data->device_name.'" data-server-ip="'.$data->server_ip.'"  data-server-port="'.$data->server_port.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_device.'"><i class="fas fa-trash-alt"></i></button>
                </div>
                ';

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
        $dataValidate = [
            'cloud_id' => ['required', 'unique:attendance_devices'],
            'device_sn' => ['required', 'unique:attendance_devices'],
            'device_name' => ['required'],
            'server_ip' => ['required'],
            'server_port' => ['required']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }
    
        DB::beginTransaction();
        try{
            Device::create([
                'organisasi_id' => auth()->user()->organisasi_id,
                'cloud_id' => $request->cloud_id,
                'device_sn' => $request->device_sn,
                'device_name' => $request->device_name,
                'server_ip' => $request->server_ip,
                'server_port' => $request->server_port,
            ]);

            DB::commit();
            return response()->json(['message' => 'Data Device Attendance Berhasil Ditambahkan!'], 200);
        } catch(Throwable $error){
            return response()->json(['message' => $error->getMessage()], 500);
        }
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
    public function update(Request $request, string $id_device)
    {
        $dataValidate = [
            'cloud_idEdit' => ['required', 'unique:attendance_devices,cloud_id,'.$id_device.',id_device'],
            'device_snEdit' => ['required', 'unique:attendance_devices,device_sn,'.$id_device.',id_device'],
            'device_nameEdit' => ['required'],
            'server_ipEdit' => ['required'],
            'server_portEdit' => ['required']
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try{

            $device = Device::find($id_device);
            $device->update([
                'cloud_id' => $request->cloud_idEdit,
                'device_sn' => $request->device_snEdit,
                'device_name' => $request->device_nameEdit,
                'server_ip' => $request->server_ipEdit,
                'server_port' => $request->server_portEdit,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data Device Attendance Berhasil Diubah!'], 200);
        } catch(Throwable $error){
            return response()->json(['message' => $error->getMessage()], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete(int $id_device)
    {
        DB::beginTransaction();
        try{
            $device = Device::find($id_device);

            if(!$device){
                return response()->json(['message' => 'Data tidak ditemukan!'], 404);
            }

            $device->delete();
            DB::commit();
            return response()->json(['message' => 'Data Device Berhasil dihapus!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
