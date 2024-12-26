<?php

namespace App\Http\Controllers\StockOpname;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\StockOpname\iDempiereModel;
use App\Models\StockOpname\StockOpnameLine;
use App\Models\StockOpname\StockOpnameHeader;

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
    public function input_hasil()
    {
        $dataPage = [
            'pageTitle' => 'STO - Input Hasil',
            'page' => 'sto-input-hasil',
        ];
        return view('pages.sto.input_hasil', $dataPage );
        
    }
    public function get_part($part_code)
    {
        $product = iDempiereModel::fromProduct()->select(
            'm_product_id',
            'name',
            'value',
            'description',
            'classification'
        )->where('m_product_id', $part_code)->first();
    
        if ($product) {
            return response()->json([
                'value' => $product->value,
                'name' => $product->name,
                'description' => $product->description,
                'classification' => $product->classification
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
        );

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
                'id' => $customer->name,
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
            'issued_name' => Auth()->user()->karyawan->nama,
            'issued_by' => Auth()->user()->karyawan->id_karyawan,
            'organization_id' => Auth()->user()->organisasi_id,
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
            'customer' => ['required', 'string', 'max:255'],
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
            StockOpnameLine::where('no_label', $request->no_label)
                ->update([
                    'product_id' => $request->product_id,
                    'part_code' => $request->part_code,
                    'part_name' => $request->part_name,
                    'part_desc' => $request->part_desc,
                    'customer' => $request->customer,
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
            'redirect_url' => url()->previous()// Replace '/form' with your desired redirect URL
        ]);

    
    }    

    /**
     * Display the specified resource.
     */
    // public function datatable()
    // {
    //     $data = StockOpnameLine::all();

    //     return response()->json($data);
    // }
    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'no_label',
            1 => 'customer',
            2 => 'part_code',
            3 => 'part_name',
            4 => 'part_desc',
            5 => 'wh_name',
            6 => 'quantity',
            7 => 'input_by',
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

        $event = StockOpnameLine::getData($dataFilter, $settings);
        $totalFiltered = StockOpnameLine::countData($dataFilter);

        $dataTable = [];

        if (!empty($event)) {
            foreach ($event as $data) {
                $nestedData['no_label'] = $data->no_label;
                $nestedData['customer'] = $data->customer;
                $nestedData['part_code'] = $data->part_code;
                $nestedData['part_name'] = $data->part_name;
                $nestedData['part_desc'] = $data->part_desc;
                $nestedData['wh_name'] = $data->wh_name;
                $nestedData['quantity'] = $data->quantity;
                $nestedData['input_by'] = $data->input_by;

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
