<?php

namespace App\Http\Controllers\KSK;

use Carbon\Carbon;
use App\Models\KSK\KSK;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "KSK-E - Approval KSK",
            'page' => 'ksk-approval',
        ];
        return view('pages.ksk-e.approval.index', $dataPage);
    }

    public function datatable_must_approved(Request $request)
    {
        $columns = array(
            0 => 'ksk.id_ksk',
            1 => 'ksk.nama_divisi',
            2 => 'ksk.nama_departemen',
            3 => 'ksk.release_date',
            4 => 'ksk.parent_id',
            5 => 'ksk.released_by',
            6 => 'ksk.checked_by',
            7 => 'ksk.approved_by',
            8 => 'ksk.reviewed_div_by',
            9 => 'ksk.reviewed_ph_by',
            10 => 'ksk.reviewed_dir_by',
            11 => 'ksk.legalized_by',
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

        $ksk = KSK::getData($dataFilter, $settings);
        $totalData = KSK::countData($dataFilter);
        $totalFiltered = $totalData;

        $dataTable = [];

        if (!empty($ksk)) {
            foreach ($ksk as $data) {

                $releasedFormatted = $data->released_by ? $data->released_by : '⏳ Waiting';
                $checkedFormatted = $data->checked_by ? $data->checked_by : '⏳ Waiting';
                $approvedFormatted = $data->approved_by ? $data->approved_by : '⏳ Waiting';
                $reviewedDivFormatted = $data->reviewed_div_by ? $data->reviewed_div_by : '⏳ Waiting';
                $reviewedPhFormatted = $data->reviewed_ph_by ? $data->reviewed_ph_by : '⏳ Waiting';
                $reviewedDirFormatted = $data->reviewed_dir_by ? $data->reviewed_dir_by : '⏳ Waiting';
                $legalizedFormatted = $data->legalized_by ? $data->legalized_by : '⏳ Waiting';

                $actionFormatted = '<a href="javascript:void(0)" class="btnApproved" data-id-ksk="'.$data->id_ksk.'" data-id-departemen="'.$data->departemen_id.'" data-id-divisi="'.$data->divisi_id.'" data-parent-id="'.$data->parent_id.'" data-nama-departemen="'.$data->nama_departemen.'" data-nama-divisi="'.$data->nama_divisi.'" data-id-organisasi="'.$data->organisasi_id.'">'.$data->id_ksk.' <i class="fas fa-search"></i></a>';

                $nestedData['id_ksk'] = $actionFormatted;
                $nestedData['nama_divisi'] = $data->nama_divisi;
                $nestedData['nama_departemen'] = $data->nama_departemen;
                $nestedData['parent_name'] = $data->parent_name;
                $nestedData['release_date'] = Carbon::createFromDate($data->release_date)->format('F Y');
                $nestedData['released_by'] = $releasedFormatted;
                $nestedData['checked_by'] = $checkedFormatted;
                $nestedData['approved_by'] = $approvedFormatted;
                $nestedData['reviewed_div_by'] = $reviewedDivFormatted;
                $nestedData['reviewed_ph_by'] = $reviewedPhFormatted;
                $nestedData['reviewed_dir_by'] = $reviewedDirFormatted;
                $nestedData['legalized_by'] = $legalizedFormatted;

                $dataTable[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
        );

        return response()->json($json_data, 200);
    }
}
