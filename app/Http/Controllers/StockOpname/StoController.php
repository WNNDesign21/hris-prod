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
use App\Models\StockOpname\StockOpnameUpload;
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
        return view('pages.sto.input_hasil', $dataPage);
    }

    public function hasil_datatable(Request $request)
    {
        $columns = array(
            0 => 'sto_lines.no_label',
            1 => 'sto_lines.customer_name',
            2 => 'sto_lines.wh_name',
            2 => 'sto_lines.location_area',
            3 => 'sto_lines.part_code',
            4 => 'sto_lines.part_name',
            5 => 'sto_lines.part_desc',
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
                $nestedData['location_area'] = $data->location_area;
                $nestedData['part_code'] = $data->part_code;
                $nestedData['part_name'] = $data->part_name;
                $nestedData['part_desc'] = $data->part_desc;
                $nestedData['quantity'] = $data->quantity;
                $nestedData['identitas_lot'] = $data->identitas_lot;
                $nestedData['updated_at'] = Carbon::parse($data->updated_at)->format('d M Y, H:i:s') . '<br><small>' . $data->updated_name . '</small>';
                // $nestedData['action'] = '-';
                $nestedData['action'] = '<div class="btn-group">
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="' . $data->id_sto_line . '" data-product-id="' . $data->product_id . '" data-product-name="' . $data->part_code . '-' . $data->part_name . '-' . $data->part_desc . '" data-customer-name="' . $data->customer_name . '" data-quantity="' . $data->quantity . '" data-identitas-lot="' . $data->identitas_lot . '" data-customer-id="' . $data->customer_id . '" data-no-label="' . $data->no_label . '"><i class="fas fa-edit"></i></button>
                    <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="' . $data->id_sto_line . '"><i class="fas fa-trash-alt"></i></button>
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
                'weight' => $product->weight,
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

        $query->where('isactive', 'Y')->where('ad_client_id', 1000000);

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
                'text' => $warehouse->value . ' - ' . $warehouse->name . ' - ' . $warehouse->description,
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
        )
            ->whereNull('product_id');

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
        $locator_value = iDempiereModel::getLocator( $request->wh_id);

        $start_label = $request->input('start_label');
        $end_label = $request->input('end_label');
        

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

            // Siapkan data untuk tabel Stock Opname Line
            $data = [];
            for ($i = $start_label; $i <= $end_label; $i++) {
                $data[] = [
                    'no_label' => $i,
                    'sto_header_id' => $stoHeader->id_sto_header,
                    'wh_id' => $stoHeader->wh_id,
                    'wh_name' => $stoHeader->wh_name,
                    'locator_id' => $locator_value['m_locator_id'],
                    'locator_value' => $locator_value['value'],
                ];

            }

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
        $dataValidate = [
            'no_label' => ['required', 'exists:sto_lines,no_label'],
            'product_id' => ['required'],
            'part_code' => ['required', 'string', 'max:255'],
            'part_name' => ['nullable', 'string', 'max:255'],
            'part_desc' => ['nullable', 'string'],
            'model' => ['nullable', 'string'],
            'customer' => ['required',],
            'location_area' => ['nullable'],
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
                'location_area' => $request->location_area,
                'quantity' => $request->quantity,
                'inputed_by' => auth()->user()->karyawan->id_karyawan,
                'inputed_name' => auth()->user()->karyawan->nama,
                'updated_by' => auth()->user()->karyawan->id_karyawan,
                'updated_name' => auth()->user()->karyawan->nama,
            ]);

            $data = StockOpnameLine::where('no_label', $request->no_label)->first();
            $qty_book = (int)iDempiereModel::getQuantityBook($data->wh_id,$data->product_id)->qtyonhand ?? 0;
            $balance = $data->quantity - $qty_book;
            $upload_sto = StockOpnameUpload::where('wh_id', $data->wh_id)->where('product_id', $data->product_id);

            // Jika data sudah ada, update data
            if($upload_sto->exists()){
                $upload_sto->update([
                    'qty_book' => $qty_book,
                    'qty_count' => $upload_sto->first()->qty_count + $data->quantity,
                    'balance' => $upload_sto->first()->qty_count + $data->quantity - $qty_book,
                    'processed' => 'N',
                ]);
            } else {
                StockOpnameUpload::create([
                    'wh_id' => $data->wh_id,
                    'wh_name' => $data->wh_name,
                    'locator_id' => $data->locator_id,
                    'locator_name' => $data->locator_value,
                    'customer_id' => $data->customer_id,
                    'customer_name' => $data->customer_name,
                    'product_id' => $data->product_id,
                    'product_code' => $data->part_code,
                    'product_name' => $data->part_name,
                    'product_desc' => $data->part_desc,
                    'model' => $data->model,
                    'qty_book' => $qty_book,
                    'qty_count' => $data->quantity,
                    'balance' => $balance,
                ]);
            }

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
            7 => 'location_area',
            8 => 'quantity',
            9 => 'identitas_lot',
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
                $nestedData['location_area'] = $data->location_area;
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
            'customer_edit' => ['required'],
            'identitas_lot_edit' => ['required'],
            'quantity_edit' => ['required'],
            'product_id_edit' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);

        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $sto = StockOpnameLine::find($id);
        $product = iDempiereModel::getProduct($request->input('product_id_edit'));
        $customer_name = iDempiereModel::fromCustomer()->select('name')->where('c_bpartner_id', $request->input('customer_edit'))->first()->name;

        DB::beginTransaction();
        try {
            //ROLLBACK DULU DATA STO UPLOAD
            $upload_sto_existing_data = StockOpnameUpload::where('wh_id', $sto->wh_id)->where('product_id', $sto->product_id)->first();
            if($upload_sto_existing_data){
                $current_qty = $upload_sto_existing_data->qty_count - $sto->quantity;
                $current_balance = $current_qty - $upload_sto_existing_data->qty_book;
                $upload_sto_existing_data->update([
                    'qty_count' => $current_qty,
                    'balance' => $current_balance,
                ]);
            }

            $sto->part_code = $product->value;
            $sto->part_name = $product->name;
            $sto->part_desc = $product->description;
            $sto->model = $product->classification;
            $sto->customer_name = $customer_name;
            $sto->customer_id = $request->input('customer_edit');
            $sto->product_id = $request->input('product_id_edit');
            $sto->identitas_lot = $request->input('identitas_lot_edit');
            $sto->quantity = $request->input('quantity_edit');
            $sto->updated_by = auth()->user()->karyawan->id_karyawan;
            $sto->updated_name = auth()->user()->karyawan->nama;
            $sto->save();


            //INPUT ULANG DATA STO UPLOAD
            $new_qty_book = (int)iDempiereModel::getQuantityBook($sto->wh_id,$request->input('product_id_edit'))->qtyonhand ?? 0;
            $upload_sto_new = StockOpnameUpload::where('wh_id', $sto->wh_id)->where('product_id', $request->input('product_id_edit'))->first();

            if($upload_sto_new){
                $new_qty = $upload_sto_new->qty_count + $request->input('quantity_edit');
                $new_balance = $new_qty - $upload_sto_new->qty_book;
                $upload_sto_new->update([
                    'qty_count' => $new_qty,
                    'balance' => $new_balance,
                ]);
            } else {
                $balance = $request->input('quantity_edit') - $new_qty_book;
                StockOpnameUpload::create([
                    'wh_id' => $sto->wh_id,
                    'wh_name' => $sto->wh_name,
                    'locator_id' => $sto->locator_id,
                    'locator_name' => $sto->locator_value,
                    'customer_id' => $request->input('customer_edit'),
                    'customer_name' => $customer_name,
                    'product_id' => $request->input('product_id_edit'),
                    'product_code' => $product->value,
                    'product_name' => $product->name,
                    'product_desc' => $product->description,
                    'model' => $product->classification,
                    'qty_book' => $new_qty_book,
                    'qty_count' => $request->input('quantity_edit'),
                    'balance' => $balance,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Data Updated!'], 200);
        } catch (Throwable $error) {
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
            $upload_sto = StockOpnameUpload::where('wh_id', $sto->wh_id)->where('product_id', $sto->product_id)->first();

            if($upload_sto){
                $deleted_qty = $upload_sto->qty_count - $sto->quantity;
                $deleted_balance = $deleted_qty - $upload_sto->qty_book;
                $upload_sto->update([
                    'qty_count' => $deleted_qty,
                    'balance' => $deleted_balance,
                ]);
            }

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
        try {
            $sto = StockOpnameLine::find($id);
            return response()->json(['message' => 'Data ditemukan!', 'data' => $sto], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
