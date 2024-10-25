<?php

namespace App\Http\Controllers\Lembure;

use App\Models\Lembure;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LembureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Dashboard",
            'page' => 'lembure-dashboard'
        ];
        return view('pages.lembur-e.index', $dataPage);
    }

    public function pengajuan_lembur_view()
    {
        $dataPage = [
            'pageTitle' => "Lembur-E - Pengajuan Lembur",
            'page' => 'lembure-pengajuan-lembur',
        ];
        return view('pages.lembur-e.pengajuan-lembur', $dataPage);
    }

    public function pengajuan_lembur_datatable(Request $request)
    {
        $columns = array(
            0 => 'lemburs.id_lembur',
            1 => 'lemburs.issued_date',
            2 => 'karyawans.nama',
            3 => 'lemburs.total_durasi',
            4 => 'lemburs.status',
            5 => 'lemburs.plan_checked_by',
            6 => 'lemburs.plan_approved_by',
            7 => 'lemburs.plan_legalized_by',
            8 => 'lemburs.actual_checked_by',
            9 => 'lemburs.actual_approved_by',
            10 => 'lemburs.actual_legalized_by'
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

        $totalData = Lembure::where('issued_by', auth()->user()->karyawan->id_karyawan)->count();
        $totalFiltered = $totalData;
        
        $lembure = Lembure::getData($dataFilter, $settings);
        $totalFiltered = $lembure->count();
        $dataTable = [];

        if (!empty($lembure)) {
            foreach ($lembure as $data) {
                $nestedData['id_lembur'] = $data->id_lembur;
                $nestedData['issued_date'] = $data->issued_date;
                $nestedData['issued_by'] = $data->nama_karyawan;
                $nestedData['total_durasi'] = $data->total_durasi;
                $nestedData['status'] = $data->status;
                $nestedData['plan_checked_by'] = $data->plan_checked_by;
                $nestedData['plan_approved_by'] = $data->plan_approved_by;
                $nestedData['plan_legalized_by'] = $data->plan_legalized_by;
                $nestedData['actual_checked_by'] = $data->actual_checked_by;
                $nestedData['actual_approved_by'] = $data->actual_approved_by;
                $nestedData['actual_legalized_by'] = $data->actual_legalized_by;
                $nestedData['aksi'] = '<div class="btn-group btn-group-sm">
                    <button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit"><i class="fas fa-edit"></i> Edit</button>
                </div>';

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

    public function get_data_karyawan_lembur(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = Karyawan::select(
            'id_karyawan',
            'nama',
        );

        $posisi = auth()->user()->karyawan->posisi;
        $id_posisi_members = $this->get_member_posisi($posisi);

        foreach ($posisi as $ps){
            $index = array_search($ps->id_posisi, $id_posisi_members);
            array_splice($id_posisi_members, $index, 1);
        }

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('id_karyawan', 'ILIKE', "%{$search}%")
                    ->orWhere('nama', 'ILIKE', "%{$search}%");
            });
        }

        //Ambil karyawan yang scope Aktif jika ada parameter status
        $query->aktif();


        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $karyawan) {
            $dataUser[] = [
                'id' => $karyawan->id_karyawan,
                'text' => $karyawan->nama
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }

    function get_member_posisi($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_posisi($ps->children));
            }
            $data[] = $ps->id_posisi;
        }
        return $data;
    }
}
