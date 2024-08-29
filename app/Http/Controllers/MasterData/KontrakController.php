<?php

namespace App\Http\Controllers\MasterData;

use Throwable;
use Carbon\Carbon;
use App\Models\Posisi;
use App\Models\Kontrak;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Master Data - Kontrak",
            'page' => 'masterdata-kontrak',
        ];
        return view('pages.master-data.kontrak.index', $dataPage);
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'id_kontrak',
            1 => 'karyawans.nama',
            2 => 'kontraks.nama_posisi',
            3 => 'no_surat',
            4 => 'issued_date',
            5 => 'jenis',
            6 => 'status',
            7 => 'durasi',
            8 => 'salary',
            9 => 'status_change_by',
            10 => 'tanggal_mulai',
            11 => 'tanggal_selesai',
        );

        $totalData = Kontrak::count();
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

        $kontrak = Kontrak::getData($dataFilter, $settings);
        $totalFiltered = Kontrak::countData($dataFilter);

        $dataTable = [];

        if (!empty($kontrak)) {
            foreach ($kontrak as $data) {
                $nestedData['id_kontrak'] = $data->id_kontrak;
                $nestedData['nama'] = $data->nama_karyawan;
                $nestedData['nama_posisi'] = $data->nama_posisi;
                $nestedData['no_surat'] = $data->no_surat;
                $nestedData['issued_date'] = $data->issued_date;
                $nestedData['jenis'] = $data->jenis;
                $nestedData['status'] = $data->status == 'DONE' ? '<span class="badge badge-pill badge-success">'.$data->status.'</span>' : '<span class="badge badge-pill badge-warning">'.$data->status.'</span>';
                $nestedData['durasi'] = $data->durasi.' Bulan';
                $nestedData['salary'] = $data->salary;
                $nestedData['status_change_by'] = '<small class="text-bold">'.$data->status_change_by.'</small> - '.'<br>'.'<small class="text-primary">'.$data->status_change_date.'</small>';
                $nestedData['tanggal_mulai'] = $data->tanggal_mulai_kontrak;
                $nestedData['tanggal_selesai'] = $data->tanggal_selesai_kontrak;
                $nestedData['attachment'] = $data->attachment ? '<div class="btn-group btn-group-sm"><button data-type="attachment" data-id="'.$data->id_kontrak.'" class="btn btn-sm btn-primary btn-file-change" type="button"><i class="fas fa-upload"></i> Change</button><input type="file" name="attachment" id="attachment_change_'.$data->id_kontrak.'" class="d-none"><a href="'.asset('storage/'.$data->attachment).'" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download"></i> Download</a></div>' : '<button data-id="'.$data->id_kontrak.'" data-type="attachment" class="btn btn-sm btn-primary btn-file" type="button">Upload</button><input type="file" name="attachment" id="attachment_'.$data->id_kontrak.'" class="d-none">';
                $nestedData['evidence'] = $data->evidence ? '<div class="btn-group btn-group-sm"><button data-type="evidence" data-id="'.$data->id_kontrak.'" class="btn btn-sm btn-primary btn-file-change" type="button"><i class="fas fa-upload"></i> Change</button><input type="file" name="evidence" id="evidence_change_'.$data->id_kontrak.'" class="d-none"><a href="'.asset('storage/'.$data->evidence).'" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-download"></i> Download</a></div>' : '<button data-id="'.$data->id_kontrak.'" data-type="evidence" class="btn btn-sm btn-primary btn-file" type="button">Upload</button><input type="file" name="evidence" id="evidence_'.$data->id_kontrak.'" class="d-none">';
                $nestedData['aksi'] = '
                <div class="btn-group btn-group-sm">'.
                    ($data->attachment !== null && $data->evidence !== null && $data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-success btnDone" data-id="'.$data->id_kontrak.'" data-isreactive="'.$data->isReactive.'"><i class="far fa-check-circle"></i> Done</button>' : '').
                    ($data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-warning btnEdit" data-id="'.$data->id_kontrak.'"><i class="fas fa-edit"></i> Edit</button>' : '').
                    ($data->status !== 'DONE' ? '<button type="button" class="waves-effect waves-light btn btn-sm btn-danger btnDelete" data-id="'.$data->id_kontrak.'"><i class="fas fa-trash-alt"></i> Hapus </button>' : '').
                    '<a class="waves-effect waves-light btn btn-sm btn-info" href="'.url('master-data/kontrak/download-kontrak-kerja/'.$data->id_kontrak).'" target="_blank"><i class="fas fa-download"></i> Template</a>
                </div>
                ';

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
        $dataValidate = [
            'karyawan_id.*' => ['required'],
            'jenis' => ['required'],
            'posisi' => ['required'],
            'durasi' => ['numeric','nullable'],
            'salary' => ['numeric','required'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'issued_date' => ['required', 'date'],
            'tempat_administrasi' => ['required'],
            'no_surat' => ['required', 'digits:3'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $karyawan_id = $request->karyawan_id;
        $jenis = $request->jenis;
        $posisi_id = $request->posisi;
        $durasi = $request->durasi;
        $salary = $request->salary;
        $deskripsi = $request->deskripsi;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;
        $issued_date = $request->issued_date;
        $tempat_administrasi = $request->tempat_administrasi;
        $no_surat = $request->no_surat;
        $isReactive = $request->isReactive;

        DB::beginTransaction(); 
        try{

            //CEK APAKAH DIA SUDAH ADA KONTRAK SEBELUMNYA ATAU BELUM
            $is_kontrak_exist = Kontrak::where('karyawan_id', $karyawan_id)->where('status', 'DONE')->orderBy('tanggal_selesai', 'DESC')->first();
            if($isReactive == 'Y'){
                if(!$is_kontrak_exist){
                    DB::commit();
                    return response()->json(['message' => 'Karyawan belum memiliki Kontrak untuk memilih Reactive!'], 402);
                }
            }

            if($jenis !== 'PKWTT'){

                if ($durasi == 0) {
                    DB::commit();
                    return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
                }

                $no_surat_int = intval($no_surat);
                foreach ($karyawan_id as $karyawan) {
                    $kry = Karyawan::find($karyawan);
                    $kry->jenis_kontrak = $jenis;
                    $kry->save();
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . (now()->timestamp + 1),
                        'karyawan_id' => $karyawan,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => Posisi::find($posisi_id)->nama,
                        'jenis' => $jenis,
                        'durasi' => $durasi,
                        'salary' => $salary,
                        'issued_date' => $issued_date,
                        'tempat_administrasi' => $tempat_administrasi,
                        'no_surat' => str_pad($no_surat_int, 3, '0', STR_PAD_LEFT),
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                        'isReactive' => $isReactive == 'Y' ? 'Y' : 'N',
                        'tanggal_mulai_before' => $isReactive == 'Y' ? $kry->tanggal_mulai : null,
                        'tanggal_selesai_before' => $isReactive == 'Y' ? $kry->tanggal_selesai : null,
                    ]);
                    $no_surat_int++;
                }

            } else {

                $no_surat_int = intval($no_surat);
                foreach($karyawan_id as $karyawan){
                    $kry = Karyawan::find($karyawan);
                    $kry->jenis_kontrak = $jenis;
                    $kry->save();
                    $kontrak = Kontrak::create([
                        'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
                        'karyawan_id' => $karyawan,
                        'posisi_id' => $posisi_id,
                        'nama_posisi' => Posisi::find($posisi_id)->nama,
                        'jenis' => $jenis,
                        'durasi' => null,
                        'salary' => $salary,
                        'issued_date' => $issued_date,
                        'no_surat' => str_pad($no_surat_int, 3, '0', STR_PAD_LEFT),
                        'tempat_administrasi' => $tempat_administrasi,
                        'deskripsi' => $deskripsi,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => null,
                        'isReactive' => $isReactive == 'Y' ? 'Y' : 'N',
                        'tanggal_mulai_before' => $isReactive == 'Y' ? $kry->tanggal_mulai : null,
                        'tanggal_selesai_before' => $isReactive == 'Y' ? $kry->tanggal_selesai : null,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Ditambahkan!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
    }

    // Tidak Dipakai Lagi
    // public function store_or_update(Request $request)
    // {
    //     $dataValidate = [
    //         'karyawan_id_kontrakEdit' => ['required'],
    //         'jenis_kontrakEdit' => ['required'],
    //         'posisi_kontrakEdit' => ['required'],
    //         'durasi_kontrakEdit' => ['numeric','nullable'],
    //         'salary_kontrakEdit' => ['numeric','required'],
    //         'tanggal_mulai_kontrakEdit' => ['required', 'date'],
    //         'tanggal_selesai_kontrakEdit' => ['nullable', 'date'],
    //         'issued_date_kontrakEdit' => ['required', 'date'],
    //         'tempat_administrasi_kontrakEdit' => ['required'],
    //         'no_surat_kontrakEdit' => ['required'],
    //     ];

    //     $validator = Validator::make(request()->all(), $dataValidate);
    
    //     if ($validator->fails()) {
    //         return response()->json(['message' => 'Fill your input correctly!'], 402);
    //     }

    //     $karyawan_id = $request->karyawan_id_kontrakEdit;
    //     $jenis = $request->jenis_kontrakEdit;
    //     $posisi_id = $request->posisi_kontrakEdit;
    //     $durasi = (int)$request->durasi_kontrakEdit;
    //     $salary = $request->salary_kontrakEdit;
    //     $deskripsi = $request->deskripsi_kontrakEdit;
    //     $tanggal_mulai = $request->tanggal_mulai_kontrakEdit;
    //     $tanggal_selesai = $request->tanggal_selesai_kontrakEdit;
    //     $id_kontrak = $request->id_kontrakEdit;
    //     $issued_date = $request->issued_date_kontrakEdit;
    //     $tempat_administrasi = $request->tempat_administrasi_kontrakEdit;
    //     $no_surat = $request->no_surat_kontrakEdit;

    //     DB::beginTransaction();
    //     try{

    //         if(!$id_kontrak){
    //             if($jenis !== 'PKWTT'){

    //                 if ($durasi === 0) {
    //                     DB::commit();
    //                     return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
    //                 }

    //                 $kry = Karyawan::find($karyawan_id);
    //                 $kry->jenis_kontrak = $jenis;
    //                 $kry->save();

    //                 $kontrak = Kontrak::create([
    //                     'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
    //                     'karyawan_id' => $karyawan_id,
    //                     'posisi_id' => $posisi_id,
    //                     'nama_posisi' => Posisi::find($posisi_id)->nama,
    //                     'jenis' => $jenis,
    //                     'durasi' => $durasi,
    //                     'salary' => $salary,
    //                     'issued_date' => $issued_date,
    //                     'tempat_administrasi' => $tempat_administrasi,
    //                     'no_surat' => $no_surat,
    //                     'deskripsi' => $deskripsi,
    //                     'tanggal_mulai' => $tanggal_mulai,
    //                     'tanggal_selesai' => $tanggal_selesai,
    //                 ]);
    //             } else {
    //                 $kry = Karyawan::find($karyawan_id);
    //                 $kry->jenis_kontrak = $jenis;
    //                 $kry->save();
    //                 $kontrak = Kontrak::create([
    //                     'id_kontrak' => 'KONTRAK-'. Str::random(4) . '-' . now()->timestamp,
    //                     'karyawan_id' => $karyawan_id,
    //                     'posisi_id' => $posisi_id,
    //                     'nama_posisi' => Posisi::find($posisi_id)->nama,
    //                     'jenis' => $jenis,
    //                     'durasi' => $durasi,
    //                     'salary' => $salary,
    //                     'issued_date' => $issued_date,
    //                     'no_surat' => $no_surat,
    //                     'tempat_administrasi' => $tempat_administrasi,
    //                     'deskripsi' => $deskripsi,
    //                     'tanggal_mulai' => $tanggal_mulai,
    //                 ]);
    //             }
    //             $text = 'Kontrak Berhasil Ditambahkan!';
    //         } else {

    //             if ($durasi === 0 && $jenis !== 'PKWTT') {
    //                 DB::commit();
    //                 return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
    //             }

    //             $kry = Karyawan::find($karyawan_id);
    //             $kry->jenis_kontrak = $jenis;
    //             $kry->save();

    //             $kontrak = Kontrak::find($id_kontrak);
    //             $kontrak->update([
    //                 'posisi_id' => $posisi_id,
    //                 'nama_posisi' => Posisi::find($posisi_id)->nama,
    //                 'jenis' => $jenis,
    //                 'durasi' => $durasi,
    //                 'salary' => $salary,
    //                 'deskripsi' => $deskripsi,
    //                 'tanggal_mulai' => $tanggal_mulai,
    //                 'tempat_administrasi' => $tempat_administrasi,
    //                 'no_surat' => $no_surat,
    //                 'tanggal_selesai' => $jenis !== 'PKWTT' ? $tanggal_selesai : null,
    //                 'issued_date' => $issued_date
    //             ]);
    //             $text = 'Kontrak Berhasil Diupdate!';
    //         }
    //         DB::commit();
    //         return response()->json(['message' => $text, 'data' => $kontrak],200);
    //     } catch(Throwable $error){
    //         DB::rollBack();
    //         return response()->json(['message' => $error->getMessage()], 500);
    //     } catch (QueryException $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
    //     } catch (ModelNotFoundException $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
    //     }
    // }

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
    public function update(Request $request, string $id_kontrak)
    {
        $dataValidate = [
            'no_surat_kontrakEdit' => ['required','digits:3'],
            'issued_date_kontrakEdit' => ['required','date'],   
            'tempat_administrasi_kontrakEdit' => ['required'],
            'durasi_kontrakEdit' => ['numeric','nullable'],
            'salary_kontrakEdit' => ['numeric','required'],
            'tanggal_mulai_kontrakEdit' => ['required','date'],
            'tanggal_selesai_kontrakEdit' => ['nullable','date'],
            'jenis_kontrakEdit' => ['required'],
            'posisi_kontrakEdit' => ['required'], 
            'status_kontrakEdit' => ['required'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $no_surat = $request->no_surat_kontrakEdit;
        $issued_date = $request->issued_date_kontrakEdit;
        $tempat_administrasi = $request->tempat_administrasi_kontrakEdit;
        $durasi = $request->durasi_kontrakEdit; 
        $salary = $request->salary_kontrakEdit;
        $tanggal_mulai = $request->tanggal_mulai_kontrakEdit;
        $tanggal_selesai = $request->tanggal_selesai_kontrakEdit;
        $jenis = $request->jenis_kontrakEdit;
        $posisi = $request->posisi_kontrakEdit; 
        $deskripsi = $request->deskripsi_kontrakEdit;
        $status = $request->status_kontrakEdit;

        DB::beginTransaction();
        try{

            if ($jenis !== 'PKWTT' && $durasi == 0){
                DB::commit();
                return response()->json(['message' => 'Durasi tidak boleh kosong!'], 402);
            }

            $kontrak = Kontrak::find($id_kontrak);
            $kontrak->no_surat = $no_surat;
            $kontrak->issued_date = $issued_date;   
            $kontrak->tempat_administrasi = $tempat_administrasi;

            $kry = Karyawan::find($kontrak->karyawan_id);
            $kry->jenis_kontrak = $jenis;
            $kry->save();

            if($jenis == 'PKWTT'){
                $kontrak->durasi = null;
                $kontrak->tanggal_selesai = null;
            } else {
                $kontrak->durasi = $durasi;
                $kontrak->tanggal_selesai = $tanggal_selesai;
            }
            
            $kontrak->durasi = $durasi;
            $kontrak->salary = $salary;
            $kontrak->tanggal_mulai = $tanggal_mulai;
            $kontrak->jenis = $jenis;

            if($kontrak->status == $status){ 
                $kontrak->status = $status;
            } else {
                $kontrak->status = $status;
                $kontrak->status_change_by = auth()->user()->karyawan->nama;
                $kontrak->status_change_date = now()->format('Y-m-d');
            }

            $kontrak->status = $status;
            $kontrak->posisi_id = $posisi;
            $kontrak->nama_posisi = Posisi::find($posisi)->nama;
            $kontrak->deskripsi = $deskripsi;

            $kontrak->save();

            DB::commit();
            return response()->json(['message' => 'Kontrak Diupdate!'],200);
        } catch(Throwable $error){
            DB::rollBack();
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

    public function delete(string $id)
    {
        DB::beginTransaction();
        try{
            $kontrak = Kontrak::find($id);
            $kontrak->delete();
            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Dihapus!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
    }

    public function get_data_list_kontrak(string $karyawan_id)
    {
        $kontrak = Kontrak::where('karyawan_id', $karyawan_id)->orderBy('tanggal_mulai', 'DESC')->get();
        $list = [];
        if($kontrak){
            foreach($kontrak as $item){
                if($item->status == 'DONE'){
                    $badge = '<span class="badge badge-pill badge-warning">'.$item->status.'</span>';
                } else {
                    $badge = '<span class="badge badge-pill badge-success">'.$item->status.'</span>';
                } 
                $list[] = [
                    'id_kontrak' => $item->id_kontrak,
                    'nama_posisi' => $item->nama_posisi,
                    'posisi_id' => $item->posisi_id,
                    'jenis' => $item->jenis,
                    'status' => $item->status,
                    'status_badge' => $badge,
                    'issued_date' => $item->issued_date,
                    'issued_date_text' => Carbon::parse($item->issued_date)->format('d M Y'),
                    'tempat_administrasi' => $item->tempat_administrasi,
                    'durasi' => $item->durasi,
                    'no_surat' => 'No. ' . $item->no_surat . '/'.$item->jenis.' - I/HRD-TCF3/V/' . date('Y'),
                    'salary' => 'Rp. ' . number_format($item->salary, 0, ',', '.').' ,-',
                    'deskripsi' => $item->deskripsi,
                    'tanggal_mulai' => Carbon::parse($item->tanggal_mulai)->format('d M Y'),
                    'tanggal_selesai' => $item->tanggal_selesai !== null ? Carbon::parse($item->tanggal_selesai)->format('d M Y') : 'Unknown',
                    'attachment' => $item->attachment ? asset('storage/'.$item->attachment) : null
                ];
            }
            return response()->json(['data' => $list], 200);
        } else {
            return response()->json(['message' => 'Data Karyawan tidak ditemukan!'], 404);
        }

    }

    public function get_data_detail_kontrak(string $idKontrak)
    {
        $kontrak = Kontrak::find($idKontrak);
        if($kontrak){
            $data = [
                'id_kontrak' => $kontrak->id_kontrak,
                'nama_karyawan' => $kontrak->karyawan->nama,
                'posisi_id' => $kontrak->posisi_id,
                'jenis' => $kontrak->jenis,
                'status' => $kontrak->status,
                'issued_date' => $kontrak->issued_date,
                'tempat_administrasi' => $kontrak->tempat_administrasi,
                'durasi' => $kontrak->durasi,
                'no_surat' => $kontrak->no_surat,
                'salary' => $kontrak->salary,
                'deskripsi' => $kontrak->deskripsi,
                'tanggal_mulai' => $kontrak->tanggal_mulai,
                'tanggal_selesai' => $kontrak->tanggal_selesai,
                'isReactive' => $kontrak->isReactive
            ];
            return response()->json(['data' => $data], 200);
        } else {
            return response()->json(['message' => 'Data Kontrak tidak ditemukan!'], 404);
        }
    }

    public function download_kontrak_kerja(string $idKontrak)
    {
        $kontrak = Kontrak::find($idKontrak);

        $templatePath = public_path('template/kontrak_pkwt.docx');
        $templateProcessor = new TemplateProcessor($templatePath);
        $tanggal_lahir = Carbon::parse($kontrak->karyawan->tanggal_lahir)->locale('id')->isoFormat('LL');
        $day = $this->get_nama_hari($kontrak->issued_date);
        $issued_date = Carbon::parse($kontrak->issued_date)->format('d/m/Y');
        $issued_date_format = Carbon::parse($kontrak->issued_date)->locale('id')->isoFormat('LL');
        $issued_date_text = $this->tanggal_to_kalimat($kontrak->issued_date);
        $tanggal_mulai = Carbon::parse($kontrak->tanggal_mulai)->format('d/m/Y');
        $tanggal_mulai_text = $this->tanggal_to_kalimat($kontrak->tanggal_mulai);
        $tanggal_selesai = Carbon::parse($kontrak->tanggal_selesai)->format('d/m/Y');
        $tanggal_selesai_text = $this->tanggal_to_kalimat($kontrak->tanggal_selesai);
        $durasi = $kontrak->durasi;
        $durasi_text = $this->angka_to_kata($durasi);
        $departemen = $kontrak->posisi->departemen->nama;
        $jabatan = $kontrak->posisi->jabatan->nama;
        $salary = $kontrak->salary;
        $salary_rupiah = 'Rp. ' . number_format($salary, 0, ',', '.').' ,-';
        $salary_text = $this->terbilang($salary).'Rupiah';

        $templateProcessor->setValue('nama', $kontrak->karyawan->nama);
        $templateProcessor->setValue('no_surat', $kontrak->no_surat);
        $templateProcessor->setValue('nik', $kontrak->karyawan->nik);
        $templateProcessor->setValue('tempat_lahir', $kontrak->karyawan->tempat_lahir);
        $templateProcessor->setValue('jenis_kelamin', $kontrak->karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan');
        $templateProcessor->setValue('tanggal_lahir', $tanggal_lahir);
        $templateProcessor->setValue('alamat', $kontrak->karyawan->alamat);
        $templateProcessor->setValue('day', $day);
        $templateProcessor->setValue('issued_date', $issued_date);
        $templateProcessor->setValue('issued_date_text', $issued_date_text);
        $templateProcessor->setValue('issued_date_format', $issued_date_format);
        $templateProcessor->setValue('durasi', $durasi);
        $templateProcessor->setValue('durasi_text', $durasi_text);
        $templateProcessor->setValue('jabatan', $kontrak->posisi->jabatan->nama);
        $templateProcessor->setValue('departemen', $kontrak->posisi->jabatan->id_jabatan !== [1, 2] ? 'Departemen '.$kontrak->posisi->departemen->nama : 'Divisi '. $kontrak->posisi->divisi->nama);
        $templateProcessor->setValue('tanggal_mulai', $tanggal_mulai);
        $templateProcessor->setValue('tanggal_mulai_text', $tanggal_mulai_text);
        $templateProcessor->setValue('tanggal_selesai', $tanggal_selesai);
        $templateProcessor->setValue('tanggal_selesai_text', $tanggal_selesai_text);
        $templateProcessor->setValue('salary', $salary_rupiah);
        $templateProcessor->setValue('salary_text', $salary_text);

        header("Content-Disposition: attachment; filename=".$kontrak->id_kontrak.".docx");
        $templateProcessor->saveAs('php://output');
    }

    function get_nama_hari($tanggal) {
        $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
        $date = Carbon::createFromFormat('Y-m-d', $tanggal);
        $namaHari = $date->locale('id')->isoFormat('dddd');
    
        return $namaHari;
    }

    function tanggal_to_kalimat($tanggal) {

        $tanggal = Carbon::parse($tanggal);
        $bulanIndonesia = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    
        $hari = $tanggal->day;
        $bulan = $bulanIndonesia[$tanggal->month];
        $tahun = $tanggal->year;
        $tahun = substr($tahun, 2);
    
        $angkaKeKata = [
            '1' => 'Satu',
            '2' => 'Dua',
            '3' => 'Tiga',
            '4' => 'Empat',
            '5' => 'Lima',
            '6' => 'Enam',
            '7' => 'Tujuh',
            '8' => 'Delapan',
            '9' => 'Sembilan',
            '10' => 'Sepuluh',
            '11' => 'Sebelas',
            '12' => 'Dua Belas',
            '13' => 'Tiga Belas',
            '14' => 'Empat Belas',
            '15' => 'Lima Belas',
            '16' => 'Enam Belas',
            '17' => 'Tujuh Belas',
            '18' => 'Delapan Belas',
            '19' => 'Sembilan Belas',
            '20' => 'Dua Puluh',
            '21' => 'Dua Puluh Satu',
            '22' => 'Dua Puluh Dua',
            '23' => 'Dua Puluh Tiga',
            '24' => 'Dua Puluh Empat',
            '25' => 'Dua Puluh Lima',
            '26' => 'Dua Puluh Enam',
            '27' => 'Dua Puluh Tujuh',
            '28' => 'Dua Puluh Delapan',
            '29' => 'Dua Puluh Sembilan',
            '30' => 'Tiga Puluh',
            '31' => 'Tiga Puluh Satu',
        ];
    
        $hariKata = $angkaKeKata[$hari];
        $kalimatTanggal = $hariKata . ' ' . $bulan . ' ' . 'Dua Ribu ' . $angkaKeKata[$tahun];
    
        return $kalimatTanggal;
    }

    function angka_to_kata($angka) {

        $angkaKeKata = [
            '1' => 'Satu',
            '2' => 'Dua',
            '3' => 'Tiga',
            '4' => 'Empat',
            '5' => 'Lima',
            '6' => 'Enam',
            '7' => 'Tujuh',
            '8' => 'Delapan',
            '9' => 'Sembilan',
            '10' => 'Sepuluh',
            '11' => 'Sebelas',
            '12' => 'Dua Belas',
            '13' => 'Tiga Belas',
            '14' => 'Empat Belas',
            '15' => 'Lima Belas',
            '16' => 'Enam Belas',
            '17' => 'Tujuh Belas',
            '18' => 'Delapan Belas',
            '19' => 'Sembilan Belas',
            '20' => 'Dua Puluh',
            '21' => 'Dua Puluh Satu',
            '22' => 'Dua Puluh Dua',
            '23' => 'Dua Puluh Tiga',
            '24' => 'Dua Puluh Empat',
            '25' => 'Dua Puluh Lima',
            '26' => 'Dua Puluh Enam',
            '27' => 'Dua Puluh Tujuh',
            '28' => 'Dua Puluh Delapan',
            '29' => 'Dua Puluh Sembilan',
            '30' => 'Tiga Puluh',
            '31' => 'Tiga Puluh Satu',
        ];
        return $angkaKeKata[$angka];
    }

    function angka_to_rupiah_text($angka) {
        $angka = (int) $angka;
    
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $cabutan = ['', 'Ribu', 'Juta', 'Miliar', 'Triliun'];
    
        $nilai = '';
        if ($angka < 12) {
            $nilai = $bilangan[$angka];
        } else if ($angka < 20) {
            $nilai = $bilangan[$angka - 10] . ' Belas';
        } else {
            $i = 0;
            $baca = '';
            while ($angka >= 1000) {
                $sub_angka = $angka % 1000;
                $angka = intdiv($angka, 1000); 
                $baca = $this->angka_to_rupiah_text($sub_angka) . ' ' . $cabutan[$i] . ' ';
                $i++;
            }

            if ($angka > 0 && $angka < 100) {
                if ($angka >= 10) {
                    $baca = $bilangan[$angka] . ' Ratus ' . $baca;
                } else {
                    $baca = $bilangan[$angka] . ' ' . $baca;
                }
            } else if ($angka > 0) {
                $baca = $bilangan[$angka] . ' ' . $baca;
            }

            dd($baca);
    
            $nilai = trim($baca);
        }
    
        return $nilai . ' Rupiah';
    }

    function terbilang($angka)
    {
        $angka = (int)$angka;
        $bilangan = array('', 'Satu ', 'Dua ', 'Tiga ', 'Empat ', 'Lima ', 'Enam ', 'Tujuh ', 'Delapan ', 'Sembilan ', 'Sepuluh ', 'Sebelas ');

        $temp = '';

        if ($angka < 12) {
            $temp = $bilangan[$angka];
        } else if ($angka < 20) {
            $temp = $bilangan[$angka - 10] . 'Belas ';
        } else if ($angka < 100) {
            $temp = self::terbilang($angka / 10) . 'Puluh ' . self::terbilang($angka % 10);
        } else if ($angka < 200) {
            $temp = 'Seratus' . self::terbilang($angka - 100);
        } else if ($angka < 1000) {
            $temp = self::terbilang($angka / 100) . 'Ratus ' . self::terbilang($angka % 100);
        } else if ($angka < 2000) {
            $temp = 'Seribu' . self::terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $temp = self::terbilang($angka / 1000) . 'Ribu ' . self::terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $temp = self::terbilang($angka / 1000000) . 'Juta ' . self::terbilang($angka % 1000000);
        }

        return $temp;
    }

    public function upload_kontrak(Request $request, string $type, string $id_kontrak){
        $dataValidate = [
            'attachment' => ['file', 'max:5000', 'mimes:pdf'],
            'evidence' => ['file', 'max:5000', 'mimes:pdf'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'File harus bertipe PDF & Berukuran maksimal 5 mb.'], 402);
        }

        DB::beginTransaction();
        try {
            $kontrak = Kontrak::find($id_kontrak);

            if($type == 'attachment'){
                if($request->hasFile('attachment')){
                    $file = $request->file('attachment');
                    $kontrak_scan = $kontrak->karyawan->nama . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file_path = $file->storeAs("attachment/kontrak", $kontrak_scan);
                    if($kontrak->attachment)
                    {
                        Storage::delete($kontrak->attachment);
                    }
                    $kontrak->attachment = $file_path;
                    $kontrak->save();
                }
            } else {
                if($request->hasFile('evidence')){
                    $file = $request->file('evidence');
                    $evidence = $kontrak->karyawan->nama . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file_path = $file->storeAs("attachment/evidence", $evidence);
                    if($kontrak->evidence)
                    {
                        Storage::delete($kontrak->evidence);
                    }
                    $kontrak->evidence = $file_path;
                    $kontrak->save();
                }
            }
            
            DB::commit();
            return response()->json(['message' => 'Upload File pada '.$id_kontrak.' Sukses!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
    }

    public function done_kontrak(Request $request, string $id_kontrak)
    {
        DB::beginTransaction();
        try{

            $isReactive = $request->isReactive;
            $kontrak = Kontrak::find($id_kontrak);
            $karyawan = Karyawan::find($kontrak->karyawan_id);


            //cek apakah file evidence & attachment sudah diupload
            if($kontrak->evidence == null || $kontrak->attachment == null)
            {
                DB::commit();
                return response()->json(['message' => 'Upload File Evidence & Attachment terlebih dahulu!'], 402);
            }

            //update status kontrak
            $kontrak->status = 'DONE';
            $kontrak->save();

            //update tanggal selesai karyawan
            if ($isReactive == 'Y') {
                $karyawan->tanggal_mulai = $kontrak->tanggal_mulai;
                $karyawan->tanggal_selesai = $kontrak->tanggal_selesai;
            } else {
                $karyawan->tanggal_selesai = $kontrak->tanggal_selesai;
            }
            $karyawan->jenis_kontrak = $kontrak->jenis;
            $karyawan->save();
            
            DB::commit();
            return response()->json(['message' => 'Kontrak Berhasil Selesai!'], 200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Model not found: ' . $e->getMessage()], 404);
        }
    }
}
