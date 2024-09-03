<?php

namespace App\Http\Controllers\Cutie;

use App\Models\Cutie;
use App\Models\JenisCuti;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CutieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Dashboard",
            'page' => 'cutie-dashboard',
        ];
        return view('pages.cuti-e.index', $dataPage);
    }

    public function pengajuan_cuti_view()
    {
        $dataPage = [
            'pageTitle' => "Cutie - Pengajuan Cuti",
            'page' => 'cutie-pengajuan-cuti',
        ];
        return view('pages.cuti-e.pengajuan-cuti', $dataPage);
    }

    public function pengajuan_cuti_datatable(Request $request)
    {

        $columns = array(
            0 => 'id_cuti',
            1 => 'rencana_mulai_cuti',
            2 => 'rencana_selesai_cuti',
            3 => 'aktual_mulai_cuti',
            4 => 'aktual_selesai_cuti',
            5 => 'durasi_cuti',
            6 => 'jenis_cuti',
            7 => 'alasan_cuti',
            8 => 'karyawans.nama',
            9 => 'checked_at',
            10 => 'approved_at',
            11 => 'legalize_at',
            12 => 'status_dokumen',
            13 => 'status_cuti',
        );

        $totalData = Cutie::count();
        $totalFiltered = $totalData;

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

        if(auth()->user()->hasRole('user')){
            $dataFilter['karyawan_id'] = auth()->user()->id_karyawan;
        }

        $cutie = Cutie::getData($dataFilter, $settings);
        $totalFiltered = Cutie::countData($dataFilter);

        $dataTable = [];

        if (!empty($cutie)) {
            $i = 1;
            foreach ($cutie as $data) {
                $nestedData['no'] = $data->id_cuti = $i;
                $nestedData['rencana_mulai_cuti'] = $data->rencana_mulai_cuti;
                $nestedData['rencana_selesai_cuti'] = $data->rencana_selesai_cuti;
                $nestedData['aktual_mulai_cuti'] = $data->aktual_mulai_cuti;
                $nestedData['aktual_selesai_cuti'] = $data->aktual_selesai_cuti;
                $nestedData['durasi'] = $data->durasi_cuti.' Hari';
                $nestedData['jenis'] = $data->jenis_cuti !== 'KHUSUS' ? $data->jenis_cuti : $data->jenis_cuti.' - '.$data->jenis_cuti_khusus;
                $nestedData['alasan'] = $data->alasan_cuti;
                $nestedData['karyawan_pengganti'] = $data->nama_pengganti;
                $nestedData['checked'] = $data->checked_by.'<br>'.$data->checked_at;
                $nestedData['approved'] = $data->approved_by.'<br>'.$data->approved_at;
                $nestedData['legalized'] = $data->legalized_by.'<br>'.$data->legalized_at;
                $nestedData['status_dokumen'] = $data->status_dokumen == 'WAITING' ? '<span class="badge badge-pill badge-warning">'.$data->status_dokumen.'</span>' : ($data->status_dokumen == 'APPROVED' ? '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-danger">'.$data->status_dokumen.'</span>');
                $nestedData['status'] = $data->status_cuti == 'SCHEDULED' ? '<span class="badge badge-pill badge-warning">'.$data->status_cuti.'</span>' : ($data->status_cuti == 'ON LEAVE' ? '<span class="badge badge-pill badge-secondary">'.$data->status_dokumen.'</span>' : '<span class="badge badge-pill badge-success">'.$data->status_dokumen.'</span>');
                $nestedData['attachment'] = $data->attachment;
                $nestedData['aksi'] = '';
                // $nestedData['aksi'] = '
                // <div class="btn-group btn-group-sm">'.
                //     ($data->attachment !== null && $data->evidence !== null && $data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id="'.$data->id_kontrak.'" data-isreactive="'.$data->isReactive.'"><i class="far fa-check-circle"></i> Done</button>' : '').
                //     ($data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_kontrak.'"><i class="fas fa-edit"></i> Edit</button>' : '').
                //     ($data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_kontrak.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                //     '<a class="waves-effect waves-light btn btn-sm btn-info" href="'.url('master-data/kontrak/download-kontrak-kerja/'.$data->id_kontrak).'" target="_blank"><i class="fas fa-download"></i> Template</a>
                // </div>
                // ';
                $i++;

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

    public function get_data_jenis_cuti_khusus(){
        $data = JenisCuti::all();
        foreach ($data as $jc) {
            $dataJenisCutiKhusus[] = [
                'id' => $jc->id_cuti,
                'text' => $jc->jenis
            ];
        }
        return response()->json(['data' => $dataJenisCutiKhusus],200);
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
