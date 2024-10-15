<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Template Surat",
            'page' => 'masterdata-template',
        ];
        return view('pages.master-data.template.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'nama',
            1 => 'type',
            2 => 'isactive'
        );

        $totalData = Template::count();
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

        $template = Template::getData($dataFilter, $settings);
        $totalFiltered = Template::countData($dataFilter);

        $dataTable = [];

        if (!empty($template)) {
            foreach ($template as $data) {
                $nestedData['nama'] = $data->nama;
                $nestedData['type'] = $data->type;
                $nestedData['isActive'] = $data->isActive == 'Y' ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
                $nestedData['aksi'] = '
                <div class="btn-group">
                    <a href="'.asset('storage/'.$data->template_path).'" class="waves-effect waves-light btn btn-info" target="_blank"><i class="fas fa-file-word"></i></a>
                    <button type="button" class="waves-effect waves-light btn btn-warning btnEdit" data-id="'.$data->id_template.'" data-template-nama="'.$data->nama.'" data-type="'.$data->type.'" data-isactive="'.$data->isActive.'"><i class="fas fa-edit"></i></button>
                </div>
                ';

                // <button type="button" class="waves-effect waves-light btn btn-danger btnDelete" data-id="'.$data->id_template.'"><i class="fas fa-trash-alt"></i></button>

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
            'nama_template' => ['required'],
            'type_template' => ['required'],
            'file_template' => ['file', 'max:10000', 'mimes:docx,doc', 'required'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Pastikan nama template tidak sama dan file berformat Docx/Doc!'], 402);
        }

        $organisasi_id = auth()->user()->organisasi_id;
    
        DB::beginTransaction();
        try{

            if($request->hasFile('file_template')){
                $file = $request->file('file_template');
                $fileName = $file->getClientOriginalName();
                $file_path = $file->storeAs("attachment/template", $fileName);
            }

            $template = Template::create([
                'nama' => $request->nama_template,
                'type' => $request->type_template,
                'organisasi_id' => $organisasi_id,
                'template_path' => $file_path
            ]);

            DB::commit();
            return response()->json(['message' => 'Template Ditambahkan!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
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
    public function update(Request $request, string $id_template)
    {
        $dataValidate = [
            'nama_template_edit' => ['required'],
            'type_template_edit' => ['required'],
            'isactive_template_edit' => ['required'],
            'file_template_edit' => ['file', 'max:10000', 'mimes:docx,doc'],
        ];
    
        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Pastikan nama template tidak sama dan file berformat Docx/Doc!'], 402);
        }

        
        DB::beginTransaction();
        try{
            $template = Template::find($id_template);

            //only one template pertype can be active
            $template_exist = Template::where('type', $request->type_template_edit)->where('isActive', 'Y')->where('id_template', '!=', $id_template)->first();
            if($template_exist){
                if($request->isactive_template_edit == 'Y'){
                    $template_exist->isActive = 'N';
                    $template_exist->save();
                }
            } else {
                if($request->isactive_template_edit == 'N'){
                    DB::commit();
                    return response()->json(['message' => 'Tidak bisa menonaktifkan template, Harus ada template yang aktif!'], 402);
                }
            }

            $template->nama = $request->nama_template_edit;
            $template->type = $request->type_template_edit;
            $template->isActive = $request->isactive_template_edit;
            if($request->hasFile('file_template_edit')){
                $file = $request->file('file_template_edit');
                $fileName = $file->getClientOriginalName();
                $file_path = $file->storeAs("attachment/template", $fileName);
                if($template->file_path){
                    Storage::delete($template->template_path);
                }
                $template->template_path = $file_path;
            }
            $template->save();
            DB::commit();
            return response()->json(['message' => 'Template Updated!'], 200);
        } catch(\Throwable $error){
            DB::rollback();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function delete(string $id_template)
    {
        DB::beginTransaction();
        try {
            $template = Template::findOrFail($id_template); 
            $template->delete();
            DB::commit();
            return response()->json(['message' => 'Template deleted!'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            DB::rollback();
            Log::error('Error deleting template: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
