<?php

namespace App\Http\Controllers\StockOpname;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StockOpname\StockOpnameLine;
use App\Models\StockOpname\StockOpnameUpload;

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
            0 => 'sto_upload.customer_name',
            1 => 'sto_upload.wh_name',
            2 => 'sto_upload.locator_name',
            3 => 'sto_upload.product_code',
            4 => 'sto_upload.product_name',
            5 => 'sto_upload.product_desc',
            6 => 'sto_upload.model',
            7 => 'sto_upload.qty_book',
            8 => 'sto_upload.qty_count',
            9 => 'sto_upload.balance',
            10 => 'sto_upload.processed',
        );

        $totalData = StockOpnameUpload::count();
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
        // $dataFilter = ['hasilSto'] = 'Y';

        $sto = StockOpnameUpload::getData($dataFilter, $settings);
        $totalFiltered = StockOpnameUpload::countData($dataFilter);

        $dataTable = [];

        if (!empty($sto)) {
            foreach ($sto as $data) {
                $nestedData['customer_name'] = $data->customer_name;
                $nestedData['wh_name'] = $data->wh_name;
                $nestedData['locator_name'] = $data->locator_name;
                $nestedData['product_code'] = $data->product_code;
                $nestedData['product_name'] = $data->product_name;
                $nestedData['product_desc'] = $data->product_desc;
                $nestedData['model'] = $data->model;
                $nestedData['qty_book'] = $data->qty_book;                
                $nestedData['qty_count'] = $data->qty_count;                
                $nestedData['balance'] = $data->balance;                
                $nestedData['processed'] = $data->processed;                
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
