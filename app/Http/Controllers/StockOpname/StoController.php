<?php

namespace App\Http\Controllers\StockOpname;

use Exception;
use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\StockOpname\iDempiereModel;
use App\Models\StockOpname\StockOpnameLine;
use App\Models\StockOpname\StockOpnameHeader;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function input_label()
    {
        $dataPage = [
            'pageTitle' => 'STO - Input Label',
            'page' => 'sto-input-label',
        ];
        return view('pages.sto.input_label', $dataPage);
        
    }

    public function label_datatable(Request $request)
    {
        $columns = array(
            0 => 'no_label',
            1 => 'issued_name',
            2 => 'wh_name',
            3 => 'created_at',
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
                $nestedData['issued_name'] = $data->issued_name;
                $nestedData['wh_name'] = $data->wh_name;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->format('d M Y, H:i:s');

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
    
    public function input_hasil()
    {
        $dataPage = [
            'pageTitle' => 'STO - Input Hasil',
            'page' => 'sto-input-hasil',
        ];
        return view('pages.sto.input_hasil', $dataPage );   
    }

    public function hasil_datatable(Request $request)
    {
        $columns = array(
            0 => 'sto_lines.no_label',
            1 => 'sto_lines.customer_name',
            2 => 'sto_lines.wh_name',
            3 => 'sto_lines.part_code',
            4 => 'sto_lines.part_name',
            5 => 'sto_lines.part_number',
            6 => 'sto_lines.quantity',
            7 => 'sto_lines.identitas_lot',
            8 => 'sto_lines.updated_at',
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

        $dataFilter['hasilSto'] = 'Y';

        $sto = StockOpnameLine::getData($dataFilter, $settings);
        $totalFiltered = StockOpnameLine::countData($dataFilter);

        $dataTable = [];

        if (!empty($sto)) {
            foreach ($sto as $data) {
                $nestedData['no_label'] = $data->no_label;
                $nestedData['customer_name'] = $data->customer_name;
                $nestedData['wh_name'] = $data->wh_name;
                $nestedData['part_code'] = $data->part_code;
                $nestedData['part_name'] = $data->part_name;
                $nestedData['part_number'] = $data->part_number;
                $nestedData['quantity'] = $data->quantity;
                $nestedData['identitas_lot'] = $data->identitas_lot;
                $nestedData['updated_at'] = Carbon::parse($data->updated_at)->format('d M Y, H:i:s');
                $nestedData['action'] = '<div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_sto_line.'"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_sto_line.'"><i class="fas fa-trash-alt"></i></button>
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
        );

        return response()->json($json_data, 200);
    }

    public function get_part($part_code)
    {
        $product = iDempiereModel::getProduct($part_code);
    
        if ($product) {
            return response()->json([
                'value' => $product->value,
                'name' => $product->name,
                'description' => $product->description,
                'classification' => $product->classification,
                'uom' => $product->uom,
                'partner_id' => $product->partner_id,
                'partner_name' => $product->partner_name,
            ]);
        } else {
            return response()->json(['error' => 'Product not found.'], 404);
        }
    }
    public function get_part_code(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');    
        
        $query = iDempiereModel::fromProduct()->select(
            'm_product_id',
            'value',
            'name',
            'description',
        );

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('value', 'ILIKE', "%{$search}%")
                    ->orWhere('m_product_id', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $warehouse) {
            $dataUser[] = [
                'id' => $warehouse->m_product_id,
                'text' => $warehouse->value.' - '.$warehouse->name.' - '.$warehouse->description,
                'name' => $warehouse->name,
                'description' => $warehouse->description
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
    public function get_warehouse($no_label)
    {
        $warehouse = StockOpnameLine::select(
            'wh_name',
        )->where('no_label', $no_label)->first()->wh_name;
    
        if ($warehouse) {
            return response()->json([
                'wh_name' => $warehouse,
            ]);
        } else {
            return response()->json(['error' => 'Product not found.'], 404);
        }
    }
    public function get_no_label(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');    
        
        $query = StockOpnameLine::select(
            'id_sto_line',
            'no_label',
        )->whereNull('product_id');

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('id_sto_line', 'ILIKE', "%{$search}%")
                    ->orWhere('no_label', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $noLabel) {
            $dataUser[] = [
                'id' => $noLabel->no_label,
                'text' => $noLabel->no_label
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
    public function get_customer(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');    
        
        $query = iDempiereModel::fromCustomer()->select(
            'c_bpartner_id',
            'name',
        );
        // dd($query)->get();

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('c_bpartner_id', 'ILIKE', "%{$search}%")
                    ->orWhere('name', 'ILIKE', "%{$search}%");
            });
        }


        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $customer) {
            $dataUser[] = [
                'id' => $customer->c_bpartner_id,
                'text' => $customer->name
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

    public function compare()
    {
        $dataPage = [
            'pageTitle' => 'STO - Compare Hasil',
            'page' => 'sto-compare',
        ];
        return view('pages.sto.compare', $dataPage);
        
    }

    public function get_wh_label(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');    
        
        $query = iDempiereModel::fromWarehouse()->select(
            'm_warehouse_id',
            'name',
        );

        if (!empty($search)) {
            $query->where(function ($dat) use ($search) {
                $dat->where('m_warehouse_id', 'ILIKE', "%{$search}%")
                    ->orWhere('name', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $warehouse) {
            $dataUser[] = [
                'id' => $warehouse->m_warehouse_id,
                'text' => $warehouse->name
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


public function store_label(Request $request)
{
    // Validasi input
    $request->validate([
        'start_label' => 'required|integer',
        'end_label' => 'required|integer|gte:start_label',
        'wh_id' => 'required',
    ]);

    $wh_name = iDempiereModel::fromWarehouse()->select('name')->where('m_warehouse_id', $request->wh_id)->first()->name;
    

    try {
        // Buat header Stock Opname
        $stoHeader = StockOpnameHeader::create([
            'year' => now()->format('m-Y'),
            'issued_name' => auth()->user()->karyawan->nama,
            'issued_by' => auth()->user()->karyawan->id_karyawan,
            'organization_id' => auth()->user()->organisasi_id,
            'doc_date' => now(),
            'wh_name' => $wh_name,
            'wh_id' => $request->input('wh_id'),
        ]);

        // dd($stoHeader);

        // Ambil range label
        $start_label = $request->input('start_label');
        $end_label = $request->input('end_label');

        // Siapkan data untuk tabel Stock Opname Line
        $data = [];
        for ($i = $start_label; $i <= $end_label; $i++) {
            $data[] = [
                'no_label' => $i,
                'sto_header_id' => $stoHeader->id_sto_header,
                'wh_id' => $stoHeader->wh_id,
                'wh_name' => $stoHeader->wh_name,
            ];
        }
        // dd($data);

        // Insert data ke tabel Stock Opname Line
        StockOpnameLine::insert($data);

        // Kembalikan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan!',
        ], 200);

    } catch (\Exception $e) {
        // Tangani error dan kembalikan response error
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
        ], 500);
    }
}



    public function store_hasil(Request $request)
    {
        // dd($request->all());

        $dataValidate = [
            'no_label' => ['required', 'exists:sto_lines,no_label'],
            'product_id' => ['required'],
            'part_code' => ['required', 'string', 'max:255'],
            'part_name' => ['nullable', 'string', 'max:255'],
            'part_desc' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'customer' => ['required',],
            'identitas_lot' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric'],
        ];


        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        DB::beginTransaction();
        try {
            $customer_name = iDempiereModel::fromCustomer()->select('name')->where('c_bpartner_id', $request->customer)->first()->name;
            StockOpnameLine::where('no_label', $request->no_label)
                ->update([
                    'product_id' => $request->product_id,
                    'part_code' => $request->part_code,
                    'part_name' => $request->part_name,
                    'part_desc' => $request->part_desc,
                    'model' => $request->model,
                    'customer_id' => $request->customer,
                    'customer_name' => $customer_name,
                    'identitas_lot' => $request->identitas_lot,
                    'quantity' => $request->quantity,
                ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json([
            'message' => 'Data Berhasil Disimpan!',
        ], 200);
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
        $dataValidate = [
            'no_label_edit' => ['required'],
            'customer_edit' => ['required'],
            'identitas_lot_edit' => ['required'],
            'quantity_edit' => ['required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $sto = StockOpnameLine::find($id);

        DB::beginTransaction();
        try{
            $sto->no_label = $request->input('no_label_edit');
            $sto->customer = $request->input('customer_edit');
            $sto->identitas_lot = $request->input('identitas_lot_edit');
            $sto->quantity = $request->input('quantity_edit');
            $sto->save();
            DB::commit();
            return response()->json(['message' => 'Organisasi Updated!'], 200);
        } catch(\Throwable $error){
            DB::rollback();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $sto = StockOpnameLine::findOrFail($id); 
            $sto->delete();
            DB::commit();
            return response()->json(['message' => 'Data deleted!', 'data' => $sto], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting data: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function get_sto_line($id)
    {
        try{
            $sto = StockOpnameLine::find($id);
            return response()->json(['message' => 'Data ditemukan!', 'data' => $sto], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
