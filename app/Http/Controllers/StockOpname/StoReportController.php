<?php

namespace App\Http\Controllers\StockOpname;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\StockOpname\StockOpnameLine;
use App\Models\StockOpname\StockOpnameUpload;

class StoReportController extends Controller
{


    /**
     * compare functions
     */
    public function compare(Request $request)
    {
        $dataPage = [
            'pageTitle' => 'STO - Compare Hasil',
            'page' => 'sto-compare',
        ];

        $stoUpload = StockOpnameUpload::query();
        $isFilter = false;
        if ($request->has('warehouse') && !empty($request->wh_name)) {
            $stoUpload->where('wh_name', $request->wh_name);
            $isFilter = true;
        }

        $records = $isFilter ? $stoUpload->get() :  collect();


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
            10 => 'sto_upload.organization_id',
            11 => 'sto_upload.processed',
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
        $warehouseId = $request->input('wh_id');
        if (!empty($warehouseId)) {
            $dataFilter['wh_id'] = $warehouseId;
        }

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
                $nestedData['organization_id'] = $data->organization_id;                
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

    public function export(Request $request)
    {


        $dataFilter = [];
        if ($request->input('wh_id')) {
            $dataFilter['wh_id'] = $request->input('wh_id');
            // $whName = StockOpnameUpload::where('wh_id', $request->input('wh_id'))->first()->wh_name ?? 'Unknown';
        }
        
        

        $data = StockOpnameUpload::getData($dataFilter, [
            'start' => 0,
            'limit' => PHP_INT_MAX, // Ambil semua data
            'order' => 'wh_id',
            'dir' => 'ASC',
        ]);


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Styles
        $styleThead = [
            'font' => [
                'bold' => true,
                'size' => 10,
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];

        $styleContentCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];

        // $dataSto = StockOpnameUpload:: getData($dataFilter)
        $headers = ['Customer Name', 'Warehouse', 'Locator', 
        'Product Code', 'Product Name', 'Product Number', 
        'Model', 'Qty Book', 'Qty Count','Balance', 'Organization', 'Processed'];

        $sheet->fromArray($headers, null, 'A1');
        $headerRange = 'A1:' . chr(64 + count($headers)) . '1'; 

        $sheet->getStyle($headerRange)->applyFromArray($styleThead);

    // Data rows
    $rowIndex = 2;
    foreach ($data as $row) {
        $sheet->fromArray([
            $row->customer_name, $row->wh_name, $row->locator,
            $row->product_code, $row->product_name, $row->product_desc,
            $row->model, $row->qty_book, $row->qty_count,
            $row->balance, $row->organization_id, $row->processed
        ], null, 'A' . $rowIndex);

        $sheet->getStyle('A' . $rowIndex . ':L' . $rowIndex)->applyFromArray($styleContentCenter);
        $rowIndex++;
    }

    $fileName = 'Data_STO.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Stream file ke browser
    $response = response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $fileName);

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', "attachment; filename=\"{$fileName}\"");

    return $response;

    }
}
