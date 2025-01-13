<?php

namespace App\Http\Controllers\Attendance;

use Throwable;
use App\Models\Grup;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class ShiftgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grups = Grup::all();
        $dataPage = [
            'pageTitle' => "Attendance-E - Shift Group",
            'page' => 'attendance-shiftgroup',
            'grups' => $grups,
        ];
        return view('pages.attendance-e.shiftgroup.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'departemens.nama',
            1 => 'karyawans.nama',
            2 => 'karyawans.pin',
            3 => 'grups.nama',
        );

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

        $filterDepartemen = $request->departemen;
        if (isset($filterDepartemen)){
            $dataFilter['departemen'] = $filterDepartemen;
        }

        $filterGrup = $request->grup;
        if (isset($filterGrup)){
            $dataFilter['grup'] = $filterGrup;
        }

        $grup = Karyawan::getDataShiftgroup($dataFilter, $settings);
        $totalFiltered = Karyawan::countDataShiftgroup($dataFilter);
        $totalData = Karyawan::getDataShiftgroup($dataFilter, $settings)->count();


        $dataTable = [];

        if (!empty($grup)) {
            foreach ($grup as $data) {
                $nestedData['departemen'] = $data->departemen ?? $data->divisi ?? '-';
                $nestedData['karyawan'] = $data->nama;
                $nestedData['pin'] = $data->pin;
                $nestedData['shift'] = $data->grup;
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id-karyawan="'.$data->id_karyawan.'" data-id-grup="'.$data->grup_id.'" data-pin="'.$data->pin.'"><i class="fas fa-edit"></i></button>
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
        $file = $request->file('file');
        
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'File Harus bertipe Excel!'], 400);
        }

        DB::beginTransaction();
        try {

            if($request->hasFile('file')){
                $records = 'SG_' . time() . '.' . $file->getClientOriginalExtension();
                $shiftgroup_file = $file->storeAs("attachment/upload-shift-group", $records);
            } 

            if (file_exists(storage_path("app/public/".$shiftgroup_file))) {
                $spreadsheet = IOFactory::load(storage_path("app/public/".$shiftgroup_file));
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                unset($data[0]);
                if(!empty($data)){
                    $niKaryawanList = array_column($data, 0);
                    $organisasi_id = auth()->user()->organisasi_id;
                    $karyawanList = Karyawan::whereIn('ni_karyawan', $niKaryawanList)->get()->keyBy('ni_karyawan');

                    foreach ($data as $key => $row) {
                        if (isset($karyawanList[$row[0]])) {
                            $karyawanList[$row[0]]->update([
                                'grup_id' => $row[2]
                            ]);

                            $grup = Grup::find($row[2]);
                            $karyawanList[$row[0]]->karyawanGrup()->create([
                                'grup_id' => $row[2],
                                'pin' => $karyawanList[$row[0]]->pin,
                                'active_date' => now(),
                                'organisasi_id' => $organisasi_id,
                                'toleransi_waktu' => $grup->toleransi_waktu,
                                'jam_masuk' => $grup->jam_masuk,
                                'jam_keluar' => $grup->jam_keluar,
                            ]);
                        }
                    }
                } else {
                    DB::rollback();
                    return response()->json(['message' => 'File Kosong'], 400);
                }
                DB::commit();
                return response()->json(['message' => 'Berhasil Memperbarui Shift'], 200);
            } else {
                DB::rollback();
                return response()->json(['message' => 'Gagal Membaca File, Upload Ulang!'], 404);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error processing the file: ' . $e->getMessage()], 500);
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
    public function update(Request $request, string $id_karyawan)
    {
        $dataValidate = [
            'grup_edit' => ['required', 'integer', 'regex:/^\d+$/'],
            // 'pin_edit' => ['required', 'integer', 'regex:/^\d+$/'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $karyawan = Karyawan::find($id_karyawan);
            $karyawan->grup_id = $request->grup_edit;
            $karyawan->save();

            $grup = Grup::find($request->grup_edit);
            $karyawan->karyawanGrup()->create([
                'grup_id' => $request->grup_edit,
                'pin' => $karyawan->pin,
                'active_date' => now(),
                'organisasi_id' => auth()->user()->organisasi_id,
                'toleransi_waktu' => $grup->toleransi_waktu,
                'jam_masuk' => $grup->jam_masuk,
                'jam_keluar' => $grup->jam_keluar,
            ]);

            DB::commit();
            return response()->json(['message' => 'Data berhasil diubah'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
