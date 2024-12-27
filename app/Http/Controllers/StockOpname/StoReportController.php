<?php

namespace App\Http\Controllers\StockOpname;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StockOpname\StockOpnameLine;

class StoReportController extends Controller
{


    /**
     * compare functions
     */
    public function compare()
    {
        $dataPage = [
            'pageTitle' => 'STO - Compare Hasil',
            'page' => 'sto-compare',
        ];
        return view('pages.sto.compare', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'no_label',
            1 => 'customer_name',
            2 => 'part_code',
            3 => 'part_name',
            4 => 'part_desc',
            5 => 'model',
            6 => 'wh_name',
            7 => 'quantity',
            8 => 'identitas_lot',
        );

        $totalData = StockOpnameLine::count();
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

        $sto = StockOpnameLine::getData($dataFilter, $settings);
        $totalFiltered = StockOpnameLine::countData($dataFilter);

        $dataTable = [];

        if (!empty($sto)) {
            foreach ($sto as $data) {
                $nestedData['no_label'] = $data->no_label;
                $nestedData['customer'] = $data->customer_name;
                $nestedData['part_code'] = $data->part_code;
                $nestedData['part_name'] = $data->part_name;
                $nestedData['part_desc'] = $data->part_desc;
                $nestedData['model'] = $data->model;
                $nestedData['wh_name'] = $data->wh_name;
                $nestedData['quantity'] = $data->quantity;
                $nestedData['identitas_lot'] = $data->identitas_lot;

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
}
