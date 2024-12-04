<?php

namespace App\Http\Controllers\Izine;

use Throwable;
use App\Models\Sakite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SakiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    public function lapor_skd_view()
    {
        $dataPage = [
            'pageTitle' => "Izin-E - Lapor SKD",
            'page' => 'izine-lapor-skd',
        ];
        return view('pages.izin-e.lapor-skd', $dataPage);
    }

    public function lapor_skd_datatable(Request $request)
    {

        $columns = array(
            0 => 'sakits.tanggal_mulai',
            1 => 'sakits.tanggal_selesai',
            2 => 'sakits.durasi',
            3 => 'sakits.keterangan',
            4 => 'sakits.approved_by',
            5 => 'sakits.legalized_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = (!empty($request->input('order.0.column'))) ? $columns[$request->input('order.0.column')] : $columns[0];
        $dir = (!empty($request->input('order.0.dir'))) ? $request->input('order.0.dir') : "DESC";

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];
        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;

        $totalData = Sakite::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        $sakite = Sakite::getData($dataFilter, $settings);
        $totalFiltered = Sakite::countData($dataFilter);
        $dataTable = [];

        if (!empty($sakite)) {
            foreach ($sakite as $data) {
                $durasi = $data->durasi . ' Hari';

                $tanggal_mulai = $data->tanggal_mulai ? Carbon::parse($data->tanggal_mulai)->format('d M Y') : '-';
                $tanggal_selesai = $data->tanggal_selesai ? Carbon::parse($data->tanggal_selesai)->format('d M Y') : '-';

                if($data->attachment){
                    $lampiran = '<a id="linkFoto'.$data->id_sakit.'" href="' . asset('img/no-image.png') . '"
                                    class="image-popup-vertical-fit" data-title="Lampiran SKD">
                                    <img id="imageReview'.$data->id_sakit.'" src="' . asset('img/no-image.png') . '" alt="Image Foto"
                                        style="width: 150px;height: 150px;" class="img-fluid">
                                </a>';
                }

                $nestedData['tanggal_mulai'] = $tanggal_mulai;
                $nestedData['tanggal_selesai'] = $tanggal_selesai;
                $nestedData['durasi'] = $durasi;
                $nestedData['lampiran'] = $data->attachment;
                $nestedData['approved_by'] = $data->approved_by;
                $nestedData['legalized_by'] = $data->legalized_by;
                $nestedData['aksi'] = '';

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
            "column"=>$request->input('order.0.column')
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
            'lampiran_skd' => ['mimes:jpg,png,jpeg', 'max:2048'],
            'tanggal_mulai' => ['required', 'date_format:Y-m-d', 'before_or_equal:tanggal_selesai'],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $lampiran_skd = $request->file('lampiran_skd');
            $tanggal_mulai = $request->tanggal_mulai;
            $tanggal_selesai = $request->tanggal_selesai;
            $keterangan = $request->keterangan;
            $karyawan_id = auth()->user()->karyawan->id_karyawan;
            $departemen_id = auth()->user()->karyawan->posisi[0]->departemen_id;
            $divisi_id = auth()->user()->karyawan->posisi[0]->divisi_id;
            $organisasi_id = auth()->user()->organisasi_id;

            if ($request->hasFile('lampiran_skd')) {
                $fileName = 'SKD-'.Str::random(5).'-'.date('YmdHis').'.'.$lampiran_skd->getClientOriginalExtension();
                $file_path = $lampiran_skd->storeAs("attachment/skd", $fileName);

                $sakit = Sakite::create([
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $departemen_id,
                    'divisi_id' => $divisi_id,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,  
                    'keterangan' => $keterangan,    
                    'attachment' => $file_path,
                ]);
            } else {
                $sakit = Sakite::create([
                    'karyawan_id' => $karyawan_id,
                    'organisasi_id' => $organisasi_id,
                    'departemen_id' => $departemen_id,
                    'divisi_id' => $divisi_id,
                    'tanggal_mulai' => $tanggal_mulai,
                    'tanggal_selesai' => $tanggal_selesai,  
                    'keterangan' => $keterangan,    
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Lapor SKD berhasil dibuat'], 200);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
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
